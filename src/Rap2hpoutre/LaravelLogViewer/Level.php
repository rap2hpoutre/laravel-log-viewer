<?php

namespace Rap2hpoutre\LaravelLogViewer;

/**
 * Class Level
 * @package Rap2hpoutre\LaravelLogViewer
 */
class Level
{
    /**
     * @var array<string, string>
     */
    private $levelsClasses = [
        'debug' => 'info',
        'info' => 'info',
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'critical' => 'danger',
        'alert' => 'danger',
        'emergency' => 'danger',
        'processed' => 'info',
        'failed' => 'warning',
    ];

    /**
     * @var array<string, string>
     */
    private $icons = [
        'debug' => 'info-circle',
        'info' => 'info-circle',
        'notice' => 'info-circle',
        'warning' => 'exclamation-triangle',
        'error' => 'exclamation-triangle',
        'critical' => 'exclamation-triangle',
        'alert' => 'exclamation-triangle',
        'emergency' => 'exclamation-triangle',
        'processed' => 'info-circle',
        'failed' => 'exclamation-triangle'
    ];

    /**
     * @return string[]
     */
    public function all()
    {
        return array_keys($this->icons);
    }

    /**
     * @param  string  $level
     * @return string
     */
    public function img($level)
    {
        return $this->icons[$level];
    }

    /**
     * @param  string  $level
     * @return string
     */
    public function cssClass($level)
    {
        return $this->levelsClasses[$level];
    }
}
