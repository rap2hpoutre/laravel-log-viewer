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

    /*
    |--------------------------------------------------------------------------
    | Button visibility
    |--------------------------------------------------------------------------
    |
    | Toggle the buttons/controls displayed in the log viewer UI. Set any of
    | these to false to hide the corresponding control. All default to true
    | to preserve the original behaviour.
    |
    */
    'buttons' => [
        'download'   => env('LOGVIEWER_BTN_DOWNLOAD', true),
        'clean'      => env('LOGVIEWER_BTN_CLEAN', true),
        'delete'     => env('LOGVIEWER_BTN_DELETE', true),
        'delete_all' => env('LOGVIEWER_BTN_DELETE_ALL', true),
    ],
];
