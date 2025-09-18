<?php

use Netauratech\CoreCms\Http\Middlewares\BackupSessionForEsi;
use Netauratech\CoreCms\Http\Middlewares\SmartCacheControlMiddleware;

return [
    'admin' => [
        'middleware' => [
            'auth',
            'web',
            'lscache:no-cache',
            BackupSessionForEsi::class,
            SmartCacheControlMiddleware::class
        ],
        'prefix' => 'admin',
        'name' => 'admin.',
    ]
];