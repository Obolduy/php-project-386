<?php

return [
    'timezone' => env('OWNER_TIMEZONE', 'Europe/Moscow'),

    'working_hours' => [
        'start' => env('OWNER_WORKING_HOURS_START', '09:00'),
        'end' => env('OWNER_WORKING_HOURS_END', '18:00'),
    ],

    'booking_window_days' => (int) env('OWNER_BOOKING_WINDOW_DAYS', 14),
];
