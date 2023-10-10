<?php

namespace Rap2hpoutre\LaravelLogViewer;

class Pattern
{
    /**
     * @var array<string, string>
     */
    private $patterns = [
        'logs' => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            ': (.*?)( in .*?:[0-9]+)?$/i'
        ],
        'files' => '/\{.*?\,.*?\}/i',
    ];

    /**
     * @return string[]
     */
    public function all()
    {
        return array_keys($this->patterns);
    }

    /**
     * @param  string  $pattern
     * @param  null|string  $position
     * @return string pattern
     */
    public function getPattern($pattern, $position = null)
    {
        if ($position !== null) {
            return $this->patterns[$pattern][$position];
        }

        return $this->patterns[$pattern];
    }
}
