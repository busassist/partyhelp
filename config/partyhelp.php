<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Occasion Types
    |--------------------------------------------------------------------------
    */
    'occasion_types' => [
        '21st_birthday' => '21st Birthday',
        '30th_birthday' => '30th Birthday',
        '40th_birthday' => '40th Birthday',
        '50th_birthday' => '50th Birthday',
        '60th_birthday' => '60th Birthday',
        'other_birthday' => 'Other Birthday',
        'engagement_party' => 'Engagement Party',
        'wedding_reception' => 'Wedding Reception',
        'corporate_function' => 'Corporate Function',
        'christmas_party' => 'Christmas Party',
        'farewell_party' => 'Farewell Party',
        'baby_shower' => 'Baby Shower',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Room Styles
    |--------------------------------------------------------------------------
    */
    'room_styles' => [
        'bar' => 'Bar',
        'function_room' => 'Function Room',
        'pub' => 'Pub',
        'club' => 'Club',
        'semi_outdoor' => 'Semi-Outdoor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Settings
    |--------------------------------------------------------------------------
    */
    'lead' => [
        'max_matches' => 30,
        'fulfilment_threshold' => 3,
        'expiry_hours' => 72,
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit Settings
    |--------------------------------------------------------------------------
    */
    'credits' => [
        'minimum_balance' => 100.00,
        'default_topup_threshold' => 75.00,
        'default_topup_amount' => 50.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Email
    |--------------------------------------------------------------------------
    */
    'admin_email' => env('PARTYHELP_ADMIN_EMAIL', 'admin@partyhelp.com.au'),
    'admin_phone' => env('PARTYHELP_ADMIN_PHONE'),

    /*
    |--------------------------------------------------------------------------
    | Media Upload Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'max_width' => (int) env('MEDIA_MAX_WIDTH', 1920),
        'output_format' => env('MEDIA_OUTPUT_FORMAT', 'webp'),
    ],
];
