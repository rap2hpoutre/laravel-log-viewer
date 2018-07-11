<?php

return [
    /**
     * Pattern of log files
     */
    'pattern' => env('LOGVIEWER_PATTERN', '*.log'),

    /**
     * Options: line | json
     * See document for more information
     */
    'log_mode' => 'line',
];
