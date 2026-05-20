<x-layouts.store
    title="Link tidak valid — Masfirman Pratama"
    description="Link yang kamu klik tidak valid atau sudah kadaluarsa. Hubungi admin untuk dapatkan link baru."
    bodyClass="relative"
>
    <main class="mx-auto flex min-h-[60vh] w-full max-w-2xl flex-col items-center justify-center px-6 py-16 text-center" data-testid="signed-url-error">
        <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-8 w-8 text-amber-600"
                aria-hidden="true"
            >
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-slate-900 sm:text-3xl">
            Link tidak bisa dibuka
        </h1>

        <p class="mt-3 max-w-md text-sm text-slate-600 sm:text-base">
            Link yang kamu klik tidak valid atau sudah kadaluarsa. Ini bisa terjadi karena:
        </p>

        <ul class="mt-4 max-w-md space-y-2 text-left text-sm text-slate-600">
            <li class="flex items-start gap-2">
                <span class="mt-1 inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-slate-400"></span>
                <span>Link dipersingkat atau di-edit secara manual</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-1 inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-slate-400"></span>
                <span>Link sudah lewat masa berlaku (upload: 7 hari, track: 30 hari)</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="mt-1 inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-slate-400"></span>
                <span>Akses link via copy-paste yang ngga lengkap</span>
            </li>
        </ul>

        <div class="mt-8 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800"
            >
                Kembali ke beranda
            </a>
            <a
                href="https://wa.me/6281234567890"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-900 transition hover:bg-slate-50"
            >
                Hubungi admin
            </a>
        </div>

        @if (! empty($requestPath ?? null))
            <p class="mt-8 text-xs text-slate-400" data-testid="signed-url-error-path">
                Request path: <code class="font-mono">{{ $requestPath }}</code>
            </p>
        @endif
    </main>
</x-layouts.store>
