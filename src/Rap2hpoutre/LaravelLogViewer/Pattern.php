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
    private $patterns = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            '.+ \{"exception":"\[object\] \(([^ ]+)\(code: (\d)\): *(.*?) at (.*?):([0-9]+)\)$/i',
            ': (.+) ((\{.+\}))? $/i',
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
        if ($position !== null) {
            return $patternVersion[$pattern][$position];
        }
        return $patternVersion[$pattern];

    }
}
