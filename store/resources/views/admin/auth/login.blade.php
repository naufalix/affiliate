@php
    $title ??= 'Login Admin · MasFirmanPratama.com';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-gradient-to-br from-slate-50 via-white to-primary-50 antialiased text-slate-800">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-primary-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Kembali ke beranda
            </a>

            <div class="rounded-3xl border border-slate-100 bg-white/85 backdrop-blur-md shadow-xl p-8 sm:p-10">
                <div class="mb-6">
                    <div class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                        Admin Panel
                    </div>
                    <h1 class="mt-3 text-2xl font-semibold tracking-tight">Masuk ke Admin</h1>
                    <p class="mt-1.5 text-sm text-slate-500">Kelola produk, pesanan, verifikasi pembayaran, dan resi pengiriman.</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-secondary-200 bg-secondary-50 px-4 py-3 text-sm text-secondary-900">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="mt-1.5 block w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 @error('email') border-rose-300 ring-1 ring-rose-300 @enderror"
                            placeholder="admin@masfirmanpratama.com">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="mt-1.5 block w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 @error('password') border-rose-300 ring-1 ring-rose-300 @enderror"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600 select-none cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30">
                        Ingat saya di perangkat ini
                    </label>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary-600 px-5 py-3 text-sm font-medium text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition">
                        Masuk
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Akun admin dikelola manual oleh tim. Hubungi tech-lead bila butuh akses.
            </p>
        </div>
    </main>
</body>
</html>
