<?php

namespace App\Http\Controllers;

use App\Models\InstallmentScheme;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * CheckoutController — wire FE checkout form ke DB persistence (task t_a3f2fe94).
 *
 * Flow:
 *   1. Validate FE payload (customer, address, cart_json, payment_type, scheme).
 *   2. Re-resolve produk dari slug + harga server-side (jangan trust client price).
 *   3. Generate order_number unik (MFP-YYYYMMDD-XXXXXX).
 *   4. Insert orders + order_items + order_payments dalam DB transaction.
 *   5. Generate payment schedule:
 *      - Lunas: 1 row pending sebesar grand_total
 *      - Cicilan: row 1 = DP (dp_pct% × total), row 2..N = sisa terbagi rata
 *        (interval_days dari scheme — paid_at di-set null, akan di-update saat verifikasi)
 *   6. Status order awal: 'pending' (schema source-of-truth — task body sebut
 *      'awaiting_payment' yang ngga ada di enum, default ke schema).
 *   7. Redirect ke signed URL /upload/{order_number} (TTL 24 jam) supaya customer
 *      bisa upload bukti tanpa expose order ke publik permanen.
 *
 * Catatan ref_code: optional, di-attach apa adanya (validation oleh affiliate side
 * via webhook M3, di-store sebagai string biasa).
 */
class CheckoutController extends Controller
{
    /**
     * Kurir whitelist — dari config/store.php shipping_methods (pakai key 'code').
     * Cart bisa cuma kelas (digital, ngga butuh shipping) — shipping_method optional.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'customer_phone' => ['required', 'string', 'min:8', 'max:25'],
            'address_line' => ['required', 'string', 'max:500'],
            'address_city' => ['nullable', 'string', 'max:120'],
            'address_province' => ['nullable', 'string', 'max:120'],
            'address_postal' => ['nullable', 'string', 'max:20'],
            'shipping_method' => ['nullable', 'string', 'max:50'],
            'payment_type' => ['required', 'string', 'in:lunas,cicilan'],
            'installment_scheme_id' => ['nullable', 'integer', 'exists:installment_schemes,id'],
            'cart_json' => ['required', 'string', 'min:2'],
            'cart_total' => ['required', 'integer', 'min:1'],
            'ref_code' => ['nullable', 'string', 'max:64'],
        ]);

        $cart = $this->parseCartJson($validated['cart_json']);

        if ($validated['payment_type'] === 'cicilan' && empty($validated['installment_scheme_id'])) {
            throw ValidationException::withMessages([
                'installment_scheme_id' => 'Skema cicilan wajib dipilih untuk pembayaran cicilan.',
            ]);
        }

        $scheme = null;
        if ($validated['payment_type'] === 'cicilan') {
            $scheme = InstallmentScheme::where('id', $validated['installment_scheme_id'])
                ->where('active', true)
                ->first();

            if (! $scheme) {
                throw ValidationException::withMessages([
                    'installment_scheme_id' => 'Skema cicilan tidak aktif atau tidak ditemukan.',
                ]);
            }
        }

        // Resolve produk dari slug + recalc subtotal server-side. Cart-level only:
        // ngga ada per-product scheme di task ini (forProduct(null) dari FE config).
        [$resolvedItems, $serverSubtotal] = $this->resolveCartItems($cart);

        if (empty($resolvedItems)) {
            throw ValidationException::withMessages([
                'cart_json' => 'Cart kosong atau tidak ada produk yang valid.',
            ]);
        }

        // Shipping cost: ambil dari config kalau code match. Default 0 (kelas digital).
        $shippingCost = $this->resolveShippingCost($validated['shipping_method'] ?? null);
        $grandTotal = $serverSubtotal + $shippingCost;

        // Sanity check: client-reported total ngga boleh menyimpang > 1% dari server.
        // Kalau divergence besar, kemungkinan tampered atau cart stale — reject.
        $clientTotal = (int) $validated['cart_total'];
        if ($clientTotal > 0 && abs($clientTotal - $grandTotal) > max(1000, $grandTotal * 0.01)) {
            throw ValidationException::withMessages([
                'cart_total' => 'Total cart berbeda dengan kalkulasi server. Refresh halaman dan coba lagi.',
            ]);
        }

        $order = DB::transaction(function () use (
            $validated,
            $resolvedItems,
            $grandTotal,
            $scheme,
        ) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
                'email' => $validated['customer_email'] ?? null,
                'address' => $this->composeAddress(
                    $validated['address_line'],
                    $validated['address_city'] ?? null,
                    $validated['address_province'] ?? null,
                    $validated['address_postal'] ?? null,
                ),
                'total' => $grandTotal,
                'status' => 'pending',
                'ref_code' => $validated['ref_code'] ?? null,
            ]);

            foreach ($resolvedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $this->generatePaymentSchedule($order, $grandTotal, $scheme);

            return $order;
        });

        // Signed URL TTL 24h (pre-payment window). Customer share aman karena
        // signature pakai APP_KEY — task t_8a063559 nanti bakal harden lebih lanjut.
        $uploadUrl = URL::temporarySignedRoute(
            'upload.show',
            now()->addHours(24),
            ['order_number' => $order->order_number],
        );

        return redirect($uploadUrl)
            ->with('checkout.success', true)
            ->with('checkout.order_number', $order->order_number);
    }

    /**
     * Parse cart JSON dengan defensive guards. Cart shape dari Alpine store/cart.js:
     *   [{ slug, name, price, qty, image?, category? }, ...]
     */
    protected function parseCartJson(string $cartJson): array
    {
        $decoded = json_decode($cartJson, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'cart_json' => 'Format cart tidak valid.',
            ]);
        }

        return array_values(array_filter($decoded, fn ($i) => is_array($i) && ! empty($i['slug'])));
    }

    /**
     * Resolve cart items: lookup produk dari slug, gunakan harga DB (bukan client),
     * agregasi qty per slug, hasilkan subtotal server-side. Produk yang ngga ada
     * di DB / non-active di-skip silently (atau bisa raise — pilihan: raise untuk
     * fail-loud lebih baik UX).
     *
     * @return array{0: array<int, array{product_id:int, qty:int, unit_price:int, subtotal:int}>, 1: int}
     */
    protected function resolveCartItems(array $cart): array
    {
        $slugs = array_unique(array_map(fn ($i) => (string) $i['slug'], $cart));
        $products = Product::whereIn('slug', $slugs)
            ->where('status', 'active')
            ->get()
            ->keyBy('slug');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $entry) {
            $slug = (string) ($entry['slug'] ?? '');
            $qty = max(1, (int) ($entry['qty'] ?? 1));
            $product = $products->get($slug);

            if (! $product) {
                throw ValidationException::withMessages([
                    'cart_json' => "Produk '{$slug}' tidak ditemukan atau tidak aktif.",
                ]);
            }

            $unitPrice = (int) $product->price;
            $rowSubtotal = $unitPrice * $qty;

            $items[] = [
                'product_id' => $product->id,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $rowSubtotal,
            ];

            $subtotal += $rowSubtotal;
        }

        return [$items, $subtotal];
    }

    protected function resolveShippingCost(?string $code): int
    {
        if (! $code) {
            return 0;
        }
        $methods = config('store.shipping_methods', []);
        foreach ($methods as $method) {
            if (($method['code'] ?? null) === $code) {
                return (int) ($method['price'] ?? 0);
            }
        }

        return 0;
    }

    protected function composeAddress(string $line, ?string $city, ?string $province, ?string $postal): string
    {
        return collect([$line, $city, $province, $postal])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->implode(', ');
    }

    /**
     * Generate unique order_number. Format: MFP-YYYYMMDD-XXXXXX (6 hex upper).
     * Retry up to 5x kalau bentrok (probability sangat rendah, tapi safe-guard).
     */
    protected function generateOrderNumber(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = 'MFP-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
            if (! Order::where('order_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Fallback: append microsecond random — astronomically rare to collide.
        return 'MFP-'.now()->format('Ymd').'-'.strtoupper(Str::random(8));
    }

    /**
     * Generate payment schedule rows di order_payments (status='pending'):
     *   - Lunas: 1 row sebesar grand_total
     *   - Cicilan: row 1 = DP (dp_pct% × total), row 2..N = (sisa) / (N-1)
     *     Round amount ke integer. Last installment absorb rounding diff biar
     *     sum-of-installments == grand_total tepat.
     *
     * paid_at di-set null. Akan di-update saat customer upload bukti +
     * admin verify (handled task t_812d1980 udah merged).
     */
    protected function generatePaymentSchedule(Order $order, int $grandTotal, ?InstallmentScheme $scheme): void
    {
        if (! $scheme) {
            // Lunas: single row.
            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $grandTotal,
                'method' => 'transfer',
                'status' => 'pending',
            ]);

            return;
        }

        $n = max(1, (int) $scheme->n_installments);
        $dpPct = (float) $scheme->dp_pct;
        $dpAmount = (int) round($grandTotal * $dpPct / 100);

        // Edge case: scheme dengan n=1 → treat seperti lunas, satu row sebesar grandTotal.
        if ($n === 1) {
            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $grandTotal,
                'method' => 'transfer',
                'status' => 'pending',
            ]);

            return;
        }

        // Row 1 = DP
        OrderPayment::create([
            'order_id' => $order->id,
            'amount' => $dpAmount,
            'method' => 'transfer',
            'status' => 'pending',
        ]);

        // Row 2..N = sisa dibagi rata
        $remaining = $grandTotal - $dpAmount;
        $perInstallment = (int) floor($remaining / ($n - 1));
        $lastAdjust = $remaining - ($perInstallment * ($n - 1));

        for ($i = 2; $i <= $n; $i++) {
            $amount = $perInstallment + ($i === $n ? $lastAdjust : 0);
            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'method' => 'transfer',
                'status' => 'pending',
            ]);
        }
    }
}
