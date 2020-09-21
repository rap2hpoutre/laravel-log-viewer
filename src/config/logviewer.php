<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pattern and storage path settings
    |--------------------------------------------------------------------------
    |
    | The env key for pattern and storage path with a default value
    |
    */
    'max_file_size' => 52428800, // size in Byte
    'pattern'       => env('LOGVIEWER_PATTERN', '*.log'),
    'storage_path'  => env('LOGVIEWER_STORAGE_PATH', storage_path('logs')),

    // should package register uri for logs
    // NB! This is a potential security risk if enabled in production
    'register_route' => env('LOGVIEWER_REGISTER_ROUTE', false),
    'uri'            => 'logs',
];
