<?php

return [
    'default' => env('CACHE_DRIVER', 'file'),
    'stores' => [
        'array' => ['driver' => 'array', 'serialize' => false],
        'file' => ['driver' => 'file', 'path' => storage_path('framework/cache/data')],
    ],
    'prefix' => 'cherry_pay_demo_cache',
];
