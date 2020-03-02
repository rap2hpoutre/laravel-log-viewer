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
     * @var \Illuminate\Foundation\Application | \Laravel\Lumen\Application
     */
    private $app;

    /**
     * @var array
     */
    private $patterns = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            '.+ \{.*"exception":"\[object\] \(([^ ]+)\(code: (\d)\): *(.*?) at (.*?):([0-9]+)\) *\r*\n*$/i',
            ': (.+) *((\{.+\}))? *\r*\n*$/i',
        ],
        'current_log_string' => '/^([^ ]+): *(.*?) in (.*?):([0-9]+)$/',
        'stack_init_section' => '/^\n\[stacktrace\]\n/',
        'stack' => [
            '/^(.+)(->|::)([^\(]+)\((.*)\)$/',
            '/^([^\(]+)\((.*)\)$/',
            '/^(.+)\(([0-9]+)\): (.+)$/',
        ],
        'stack_startWith' => '/^ ?\#? ?[0-9]+ ?/',
        'files' => '/\{.*?\,.*?\}/i',
    ];


    /**
     * @var array
     */
    private $patternsLumen = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            ': ([^ ]+):(\d)? *(.*?) in (.*?):([0-9]+)* *\r*\n*/i',
            ': (.+) *((\{.+\}))? *\r*\n*$/i',
        ],
        'current_log_string' => '/^([^ ]+): *(.*?) in (.*?):([0-9]+)$/',
        'stack_init_section' => '/^\nStack trace:\n/',
        'stack' => [
            '/^(.+)(->|::)([^\(]+)\((.*)\)$/',
            '/^([^\(]+)\((.*)\)$/',
            '/^(.+)\(([0-9]+)\): (.+)$/',
        ],
        'stack_startWith' => '/^ ?\#? ?[0-9]+ ?/',
        'files' => '/\{.*?\,.*?\}/i',
    ];

    /**
     * Pattern constructor.
     */
    public function __construct()
    {
        if (function_exists('app')) {
            $this->app = app();
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        return array_keys($this->patterns);
    }

    /**
     * @param $pattern
     * @param null $position
     * @return string pattern
     */
    public function getPattern($pattern, $position = null)
    {
        $patternVersion = $this->patterns;
        if ($this->isLumen()) {
            $patternVersion = $this->patternsLumen;
        }
        if ($position !== null) {
            return $patternVersion[$pattern][$position];
        }
        return $patternVersion[$pattern];

    }

    /**
     * @return bool
     */
    public function isLaravel()
    {
        if (is_a($this->app, '\Illuminate\Foundation\Application')) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isLumen()
    {
        if (is_a($this->app, '\Laravel\Lumen\Application')) {
            return true;
        }
        return false;
    }

}
