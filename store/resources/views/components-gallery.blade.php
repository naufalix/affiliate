<x-layouts.store
    title="Component Gallery — MasFirmanPratama"
    description="Smoke-test untuk seluruh Blade component library."
    :cartCount="3"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-20">

        {{-- Header --}}
        <header class="text-center max-w-3xl mx-auto">
            <p class="text-xs tracking-[0.2em] font-extrabold text-accent-600 uppercase mb-3">SPRINT M1 · KOMPONEN</p>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                Blade <span class="text-gradient">Component Library</span>
            </h1>
            <p class="mt-4 text-lg text-slate-600">Smoke-test untuk seluruh component <code class="text-primary-600">resources/views/components</code>.</p>
        </header>

        {{-- Buttons --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Buttons</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-button variant="primary" icon="arrow-right">Primary CTA</x-button>
                <x-button variant="secondary" icon="sparkles" iconPosition="left">Secondary</x-button>
                <x-button variant="outline" icon="play">Outline</x-button>
                <x-button variant="primary" size="sm">Small</x-button>
                <x-button variant="primary" size="lg" icon="rocket" iconPosition="left">Large</x-button>
                <x-button :href="url('/produk')" variant="primary" icon="shopping-bag">As Link</x-button>
            </div>
        </section>

        {{-- Badges --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Badges</h2>
            <div class="flex flex-wrap items-center gap-3">
                <x-badge variant="success" icon="check-circle">Tersedia</x-badge>
                <x-badge variant="warning" icon="flame">Best Seller</x-badge>
                <x-badge variant="info" icon="info">Info</x-badge>
                <x-badge variant="danger" icon="alert-triangle">Habis</x-badge>
                <x-badge variant="category">Kelas Online</x-badge>
                <x-badge variant="neutral">Default</x-badge>
            </div>
        </section>

        {{-- Benefit cards --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Benefit Cards</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-benefit-card icon="brain-circuit" title="Metode AMC Teruji" iconColor="primary">
                    Alpha Mind Control sudah membantu 2.500+ peserta mengubah hidup secara nyata.
                </x-benefit-card>
                <x-benefit-card icon="users" title="Akses Komunitas" iconColor="secondary">
                    Grup eksklusif untuk konsultasi dan support dari sesama alumni.
                </x-benefit-card>
                <x-benefit-card icon="award" title="Sertifikat Resmi" iconColor="accent">
                    Setiap kelas memberikan sertifikat resmi sebagai bukti kompetensi.
                </x-benefit-card>
                <x-benefit-card icon="shield-check" title="Halal &amp; Logis" iconColor="secondary">
                    Pendekatan ilmiah, bisa diterima logika, dan tidak bertentangan dengan agama.
                </x-benefit-card>
                <x-benefit-card icon="heart" title="Pendampingan Personal" iconColor="rose">
                    Mentoring 1-on-1 untuk peserta kelas privat dan platinum.
                </x-benefit-card>
                <x-benefit-card icon="trending-up" title="Hasil Nyata" iconColor="amber">
                    Banyak alumni yang berhasil naik level karier, bisnis, dan kehidupan.
                </x-benefit-card>
            </div>
        </section>

        {{-- Product cards --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Product Cards</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-product-card
                    title="Kelas Alpha Mind Control Reguler"
                    category="Kelas"
                    :price="1500000"
                    :originalPrice="2000000"
                    badge="Populer"
                    href="#"
                />
                <x-product-card
                    title="Buku 10 Keajaiban Pikiran"
                    category="Buku"
                    categoryVariant="info"
                    :price="120000"
                    href="#"
                />
                <x-product-card
                    title="Kelas Privat AMC + Mentoring 30 Hari"
                    category="Privat"
                    categoryVariant="success"
                    :price="7500000"
                    href="#"
                />
                <x-product-card
                    title="Kitab 101 Kalimat Sugesti Ajaib"
                    category="Buku"
                    categoryVariant="info"
                    :price="95000"
                    :originalPrice="150000"
                    href="#"
                />
            </div>
        </section>

        {{-- Media coverage --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Media Coverage</h2>
            <div class="rounded-2xl overflow-hidden border border-slate-100">
                <x-media-coverage />
            </div>
        </section>

        {{-- Layout demo: route placeholders --}}
        <section>
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Route Placeholders (M1)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <a href="{{ route('home') }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/</code> · home</a>
                <a href="{{ route('products.index') }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/produk</code> · products.index</a>
                <a href="{{ route('products.show', ['slug' => 'kelas-amc-reguler']) }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/produk/{slug}</code> · products.show</a>
                <a href="{{ route('cart.index') }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/cart</code> · cart.index</a>
                <a href="{{ route('checkout.index') }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/checkout</code> · checkout.index</a>
                <a href="{{ route('checkout.success', ['order' => 'MFP-DEMO-001']) }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/checkout/success/{order}</code></a>
                <a href="{{ route('upload.show', ['order_number' => 'MFP-DEMO-001']) }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/upload/{order_number}</code></a>
                <a href="{{ route('track.show', ['order_number' => 'MFP-DEMO-001']) }}" class="px-4 py-3 rounded-lg bg-white border border-slate-100 hover:border-primary-300 hover:text-primary-600 transition-colors"><code>/track/{order_number}</code></a>
            </div>
        </section>

    </div>
</x-layouts.store>
