@php
    /** Demo seed item — placeholder M1, akan di-replace dari product detail "Tambah ke Keranjang" di task berikutnya. */
    $demoSeed = [
        'slug' => 'kelas-reguler-amc',
        'name' => 'Kelas Reguler AMC',
        'price' => 4500000,
        'image' => asset('images/placeholders/produk-kelas-amc.webp'),
        'category' => 'Kelas',
    ];
@endphp

<x-layouts.store
    title="Keranjang Belanja — Firman Pratama"
    description="Periksa item dan jumlah pesanan sebelum lanjut ke proses checkout."
    bodyClass="relative pb-28 lg:pb-0"
>
    {{-- Decorative blobs (consistent dengan prototype) --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 -top-20 h-80 w-80 rounded-full bg-primary-200/70 blur-3xl animate-blob"></div>
        <div class="absolute -bottom-24 -right-16 h-80 w-80 rounded-full bg-secondary-200/70 blur-3xl animate-blob"></div>
    </div>

    <section
        class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20"
        x-data
    >
        <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-primary-600">Checkout Flow</p>
        <h1 class="mt-3 text-4xl font-extrabold leading-tight text-slate-900 md:text-5xl">
            Keranjang Belanja
        </h1>
        <p class="mt-4 max-w-3xl text-lg leading-relaxed text-slate-600">
            Pastikan item, jumlah pesanan, dan nilai transaksi sudah sesuai sebelum lanjut ke proses checkout.
        </p>

        {{-- ========================================================== --}}
        {{-- Cart state container — reserve min-height to prevent CLS    --}}
        {{-- (empty/non-empty toggle happens post-hydration via Alpine)  --}}
        {{-- ========================================================== --}}
        <div class="mt-10 min-h-[420px]">
        {{-- Empty state --}}
        <div
            x-show="$store.cart.isEmpty"
            x-cloak
            class="glass rounded-3xl border border-white/60 p-10 text-center sm:p-16"
        >
            <div class="mx-auto inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <i data-lucide="shopping-cart" class="h-10 w-10"></i>
            </div>
            <h2 class="mt-6 text-2xl font-bold text-slate-900 sm:text-3xl">Keranjang masih kosong</h2>
            <p class="mt-3 max-w-xl mx-auto text-base text-slate-600">
                Belum ada produk yang dipilih. Yuk lihat katalog kelas dan buku Mas Firman.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <x-button :href="route('products.index')" variant="primary" icon="arrow-right">
                    Lihat Produk
                </x-button>
                {{-- Dev seed shortcut — gampang test cart tanpa product detail --}}
                @if (! app()->environment('production'))
                    <button
                        type="button"
                        @click='$store.cart.add(@json($demoSeed))'
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-primary-300 hover:text-primary-600"
                    >
                        <i data-lucide="zap" class="h-4 w-4"></i>
                        Tambah Item Demo
                    </button>
                @endif
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- Cart with items                                             --}}
        {{-- ========================================================== --}}
        <div
            x-show="! $store.cart.isEmpty"
            x-cloak
            class="mt-8 grid gap-8 lg:grid-cols-3"
        >
            {{-- Items list ───────────────────────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <template x-for="item in $store.cart.items" :key="item.slug">
                        <article
                            class="panel-card glass hover-lift rounded-3xl border border-white/60 p-5 sm:p-6"
                            :data-cart-item="item.slug"
                        >
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                                {{-- Thumbnail --}}
                                <div class="img-zoom-container relative h-28 w-28 shrink-0 overflow-hidden rounded-2xl bg-slate-100 sm:h-32 sm:w-32">
                                    <template x-if="item.image">
                                        <img
                                            :src="item.image"
                                            :alt="item.name"
                                            class="img-zoom h-full w-full object-cover"
                                            loading="lazy"
                                        >
                                    </template>
                                    <template x-if="! item.image">
                                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                                            <i data-lucide="image" class="h-10 w-10"></i>
                                        </div>
                                    </template>
                                </div>

                                {{-- Info + controls --}}
                                <div class="flex flex-1 flex-col gap-4">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <template x-if="item.category">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600" x-text="item.category"></p>
                                            </template>
                                            <h3 class="mt-1 text-lg font-bold leading-tight text-slate-900 sm:text-xl" x-text="item.name"></h3>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Harga satuan
                                                <span class="font-semibold text-slate-700" x-text="$store.cart.format(item.price)"></span>
                                            </p>
                                        </div>
                                        <p
                                            class="text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl"
                                            x-text="$store.cart.format(item.price * item.qty)"
                                            :aria-label="'Subtotal ' + item.name"
                                        ></p>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                        {{-- Qty stepper --}}
                                        <div class="inline-flex items-center overflow-hidden rounded-xl border border-slate-200 bg-white">
                                            <button
                                                type="button"
                                                class="inline-flex h-11 w-11 items-center justify-center text-slate-600 transition hover:bg-slate-50 hover:text-primary-600 disabled:cursor-not-allowed disabled:opacity-40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300"
                                                @click="$store.cart.decrement(item.slug)"
                                                :disabled="item.qty <= 1"
                                                :aria-label="'Kurangi jumlah ' + item.name"
                                            >
                                                <i data-lucide="minus" class="h-4 w-4"></i>
                                            </button>
                                            <input
                                                type="number"
                                                min="1"
                                                inputmode="numeric"
                                                class="h-11 w-14 border-x border-slate-200 px-2 text-center text-base font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                                :value="item.qty"
                                                @change="$store.cart.update(item.slug, $event.target.value)"
                                                :aria-label="'Jumlah ' + item.name"
                                            >
                                            <button
                                                type="button"
                                                class="inline-flex h-11 w-11 items-center justify-center text-slate-600 transition hover:bg-slate-50 hover:text-primary-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300"
                                                @click="$store.cart.increment(item.slug)"
                                                :aria-label="'Tambah jumlah ' + item.name"
                                            >
                                                <i data-lucide="plus" class="h-4 w-4"></i>
                                            </button>
                                        </div>

                                        {{-- Remove --}}
                                        <button
                                            type="button"
                                            class="inline-flex min-h-[44px] items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:-translate-y-0.5 hover:bg-rose-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-300"
                                            @click="$store.cart.remove(item.slug)"
                                            :aria-label="'Hapus ' + item.name + ' dari keranjang'"
                                        >
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>

                {{-- Footer actions di bawah list --}}
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 font-semibold text-slate-600 transition hover:text-primary-600"
                    >
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Lanjut belanja
                    </a>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 font-semibold text-slate-500 transition hover:text-rose-600"
                        @click="if (confirm('Kosongkan seluruh keranjang?')) $store.cart.clear()"
                    >
                        <i data-lucide="trash" class="h-4 w-4"></i>
                        Kosongkan keranjang
                    </button>
                </div>
            </div>

            {{-- Summary card ─────────────────────────────────────── --}}
            <aside class="lg:col-span-1">
                <div class="panel-card glass hover-lift sticky top-28 rounded-3xl border border-white/60 p-6 sm:p-8">
                    <h2 class="text-2xl font-bold leading-tight text-slate-900">Ringkasan</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        <span x-text="$store.cart.count"></span> item dalam keranjang
                    </p>

                    <dl class="mt-5 space-y-3 text-base">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-600">Subtotal</dt>
                            <dd class="font-semibold text-slate-900" x-text="$store.cart.format($store.cart.subtotal)"></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-600">Ongkir</dt>
                            <dd class="text-slate-500">
                                <template x-if="$store.cart.shipping > 0">
                                    <span class="font-semibold text-slate-900" x-text="$store.cart.format($store.cart.shipping)"></span>
                                </template>
                                <template x-if="! ($store.cart.shipping > 0)">
                                    <span class="italic">Dihitung di checkout</span>
                                </template>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-lg">
                            <dt class="font-bold text-slate-900">Total</dt>
                            <dd class="font-extrabold text-primary-600" x-text="$store.cart.format($store.cart.total)"></dd>
                        </div>
                    </dl>

                    <a
                        href="{{ route('checkout.index') }}"
                        class="ripple mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary-600 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-primary-500/30 transition hover:-translate-y-0.5 hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300"
                    >
                        Lanjut Checkout
                        <i data-lucide="arrow-right" class="h-5 w-5"></i>
                    </a>

                    <p class="mt-4 flex items-start gap-2 text-xs leading-relaxed text-slate-500">
                        <i data-lucide="shield-check" class="mt-0.5 h-4 w-4 shrink-0 text-secondary-600"></i>
                        Data dan pembayaran kamu aman. Pembayaran manual transfer dengan upload bukti.
                    </p>
                </div>
            </aside>
        </div>
        </div> {{-- /min-h-[420px] cart state container (CLS guard) --}}

        {{-- ========================================================== --}}
        {{-- Sticky mobile checkout bar (only when cart not empty)        --}}
        {{-- ========================================================== --}}
        <div
            x-show="! $store.cart.isEmpty"
            x-cloak
            class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur lg:hidden"
            role="region"
            aria-label="Ringkasan checkout"
        >
            <div class="mx-auto w-full max-w-7xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            Total <span class="font-medium normal-case text-slate-500">(<span x-text="$store.cart.count"></span> item)</span>
                        </p>
                        <p class="text-xl font-extrabold leading-tight text-primary-600" x-text="$store.cart.format($store.cart.total)"></p>
                    </div>
                    <a
                        href="{{ route('checkout.index') }}"
                        class="ripple inline-flex min-h-[44px] shrink-0 items-center justify-center gap-2 rounded-full bg-primary-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-primary-500/30 transition hover:-translate-y-0.5 hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300"
                        aria-label="Lanjut ke halaman checkout"
                    >
                        Checkout
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.store>
