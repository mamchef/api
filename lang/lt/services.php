<?php

return [
    // Auth Services
    'auth' => [
        'admin_invalid_credentials' => 'Slaptažodis ar el. paštas neteisingas',
        'user_invalid_credentials' => 'Slaptažodis ar telefono numeris neteisingas',
        'user_invalid_phone' => 'Telefono numeris neteisingas',
        'chef_invalid_credentials' => 'Slaptažodis ar el. paštas neteisingas',
    ],

    // Profile Services
    'profile' => [
        'same_password' => 'Naujas slaptažodis turi skirtis nuo dabartinio',
        'current_password_not_match' => 'Dabartinis slaptažodis neteisingas',
    ],

    // Order Services
    'order' => [
        'insufficient_credit' => 'Nepakanka kreditų mokėjimui.',
        'payment_service_error' => 'Mokėjimo sistemos klaida',
        'order_already_processed' => 'Užsakymas jau apdorotas',
        'pickup_not_supported' => 'Virtuvė nepalaiko pasiėmimo',
        'cannot_rate_order' => 'Negalite įvertinti šio užsakymo',
        'already_rated' => 'Jau įvertinote šį užsakymą',
        'invalid_order_status' => 'Netinkama užsakymo būsena šiai operacijai',
        'delivery_not_supported' => 'Virtuvė nepalaiko pristatymo',
        'order_not_found' => 'Užsakymas nerastas',
        'unauthorized_access' => 'Neturite teisės pasiekti šį užsakymą',
    ],

    // Food Services
    'food' => [
        'max_tags_exceeded' => 'Maksimaliai 3 žymės',
        'food_not_found' => 'Maistas nerastas',
        'invalid_food_status' => 'Netinkama maisto būsena',
    ],

    // Rate Limit Services
    'rate_limit' => [
        'too_many_attempts' => 'Per daug bandymų. Galima po :seconds sekundžių',
        'exceeded_limit' => 'Viršytas užklausų limitas',
    ],

    // Payment Services
    'payment' => [
        'insufficient_funds' => 'Nepakanka lėšų',
        'payment_failed' => 'Mokėjimas nepavyko',
        'invalid_payment_method' => 'Neteisingas mokėjimo būdas',
        'payment_already_processed' => 'Mokėjimas jau apdorotas',
    ],

    // General
    'general' => [
        'operation_denied' => 'Operacija atmesta',
        'invalid_request' => 'Netinkama užklausa',
        'server_error' => 'Įvyko vidaus serverio klaida',
        'not_found' => 'Išteklius nerastas',
        'unauthorized' => 'Neautorizuotas prieigos',
        'forbidden' => 'Prieiga uždrausta',
    ],
];