<?php

return [
    'admin' => [
        'middleware' => [
            'auth',
            'web',
        ],
        'prefix' => 'admin',
        'name' => 'admin.',
    ]
];