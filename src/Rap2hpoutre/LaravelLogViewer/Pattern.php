<?php

namespace Rap2hpoutre\LaravelLogViewer;

/**
 * Class Pattern
 * @package Rap2hpoutre\LaravelLogViewer
 */

class Pattern
{

    /**
     * @return array
     */
    public function all()
    {
        return array_keys($this->patterns());
    }

    /**
     * @param array $options Override default `date` and/or `loglevels` regexes.
     * @return string[]
     */
    protected function patterns($options = [])
    {
        $options = array_merge([
            'date' => '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:[\+-]\d{4})?',
            'loglevels' => '',
        ], $options);

        return [
            // Separate log file into log entries.
            'logs' => '/\[' . $options['date'] . '\].*(?:\R(?!\[' . $options['date'] . '\]).*)*/',

            // Capture 1: date, 2: context, 3: loglevel 4: message, 5: file.
            'heading' => '/^\[('. $options['date'] . ')\](?:.*?(\w+)\.|.*?)(' . $options['loglevels'] . '): (.*?)( in .*?:[0-9]+)?$/i',

            'files' => '/\{.*?\,.*?\}/i',
        ];
    }

    /**
     * @param $pattern
     * @param array $options
     * @return string pattern
     */
    public function getPattern($pattern, $options = [])
    {
        return $this->patterns($options)[$pattern];

    }
}
