<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PaymentSubmitted — dispatched saat customer upload bukti bayar.
 *
 * Listener target (downstream task t_e5d877f3): write entry ke
 * `wa_notifications` untuk notify admin verifikasi. Listener belum
 * diimplement di task ini — event di-fire saja supaya wiring siap.
 *
 * `sequence` diisi 0-indexed posisi payment ini di order.payments order-by-id
 * (matching pattern di UploadController). Dipakai listener buat label cicilan
 * ke-N di template WA.
 */
class PaymentSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Order $order,
        public OrderPayment $payment,
        public int $sequence = 0,
    ) {}
}
