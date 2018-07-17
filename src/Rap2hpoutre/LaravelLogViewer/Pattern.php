<?php

namespace Rap2hpoutre\LaravelLogViewer;

/**
 * Class Pattern
 * @property array patterns
 * @package Rap2hpoutre\LaravelLogViewer
 */

class Pattern
{

    /**
     * @var array
     */
    private static $patterns = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            ': (.*?)( in .*?:[0-9]+)?$/i'
        ],
        'files' => '/\{.*?\,.*?\}/i',
    ];

    /**
     * @return array
     */
    public function all()
    {
        return array_keys($this->patterns);
    }

}