<?php

return [
    'read_only' => [
        'name' => 'Read Only Token',
        'abilities' => ['users:read', 'products:read'],
    ],

    'admin_token' => [
        'name' => 'Admin Token',
        'abilities' => [
            'users:read', 'users:create', 'users:update', 'users:delete',
            'products:read', 'products:create', 'products:update', 'products:delete',
        ],
    ],

    'user_manager' => [
        'name' => 'User Manager Token',
        'abilities' => ['users:read', 'users:create', 'users:update'],
    ],

    'product_manager' => [
        'name' => 'Product Manager Token',
        'abilities' => ['products:read', 'products:create', 'products:update'],
    ],

    'expired_token' => [
        'name' => 'Expired Token',
        'abilities' => ['users:read'],
        'expires_at' => '2023-01-01 00:00:00', // Past date
    ],
];