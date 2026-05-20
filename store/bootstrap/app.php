<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Untuk guard 'admin', redirect unauthenticated ke /admin/login
        // (default Laravel pakai route('login') yang tidak terdefinisi di app ini).
        $middleware->redirectGuestsTo(function ($request) {
            // Hanya admin routes yang dilindungi auth:admin saat ini.
            // Web guard tidak dipakai — kalau dipakai later, tambahin guard-aware logic di sini.
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }

            return route('admin.login');
        });

        // Authenticated admin yang nyentuh guest-only routes (login page) → dashboard
        $middleware->redirectUsersTo(function ($request) {
            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom render untuk InvalidSignatureException (task t_8a063559).
        //
        // Laravel default: 403 Forbidden tanpa konteks. Kita render view
        // 'pages.signed-url-error' dengan pesan Indonesian-friendly + CTA
        // hubungi admin. Status code:
        //   - 403 (default) untuk signature mismatch (URL di-tweak / hilang)
        //   - 410 (Gone) untuk URL expired (kita ngga bisa bedakan langsung
        //     dari Laravel — semua dilemparin sebagai 403, jadi pakai 403
        //     dengan view yang sebut kemungkinan expired juga).
        $exceptions->render(function (InvalidSignatureException $e, $request) {
            return response()
                ->view('pages.signed-url-error', [
                    'requestPath' => $request->path(),
                ], 403);
        });
    })->create();
