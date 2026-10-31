<?php

return [
    // Set these in .env when the official HM CARS number is ready.
    // Store numbers in international format, e.g. 2547XXXXXXXX (no +, spaces, or dashes).
    'phone' => env('HM_CARS_PHONE', '254717955425'),
    'whatsapp' => env('HM_CARS_WHATSAPP', '254717955425'),
    'tagline' => 'The Right Car. The Right Connection.',
    'admin' => [
        'name' => env('HM_CARS_ADMIN_NAME', 'HM CARS Admin'),
        'email' => env('HM_CARS_ADMIN_EMAIL'),
        'password' => env('HM_CARS_ADMIN_PASSWORD'),
    ],
];
