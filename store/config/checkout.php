<?php

/**
 * Checkout & customer-facing signed URL config.
 *
 * Override via .env:
 *   CHECKOUT_UPLOAD_URL_TTL_DAYS=7
 *   CHECKOUT_TRACK_URL_TTL_DAYS=30
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Upload signed URL TTL
    |--------------------------------------------------------------------------
    |
    | TTL untuk URL signed yang dikirim ke customer setelah checkout. Customer
    | pakai URL ini buat upload bukti bayar (lunas / DP). Default 7 hari supaya
    | customer bisa transfer dalam window normal — kalau lewat, customer
    | hubungi admin buat regenerate URL.
    */
    'upload_url_ttl_days' => (int) env('CHECKOUT_UPLOAD_URL_TTL_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Track signed URL TTL
    |--------------------------------------------------------------------------
    |
    | TTL untuk URL track order. Lebih panjang dari upload URL karena customer
    | masih perlu cek status setelah lunas (sampai delivered). Default 30 hari.
    */
    'track_url_ttl_days' => (int) env('CHECKOUT_TRACK_URL_TTL_DAYS', 30),

];
