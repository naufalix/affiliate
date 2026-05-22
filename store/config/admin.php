<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel — General Config
    |--------------------------------------------------------------------------
    |
    | Settings global untuk admin panel UI yang ngga cocok di env-only.
    |
    */

    /*
    | Logo initial — single character/letter rendered di sidebar logo badge,
    | mobile header, dan drawer panel header. Override via env saat client
    | brand initial-nya beda (mis. ADMIN_LOGO_INITIAL=A buat klien lain).
    */
    'logo_initial' => env('ADMIN_LOGO_INITIAL', 'F'),
];
