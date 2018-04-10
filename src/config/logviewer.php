<?php

return [
    'pattern'      => env('LOGVIEWER_PATTERN', '*.log'),
    
    /*
    |--------------------------------------------------------------------------
    | Clickable table rows
    |--------------------------------------------------------------------------
    |
    | When this value is set to true the whole <tr> is clickable and will open/close the stacktrace.
    |
    */
    'tr_clickable' => env('LOGVIEWER_TR_CLICKABLE', true),
];
