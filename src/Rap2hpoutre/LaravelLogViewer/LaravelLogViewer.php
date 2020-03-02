<?php

namespace Rap2hpoutre\LaravelLogViewer;

use Rap2hpoutre\LaravelLogViewer\Level;
use Rap2hpoutre\LaravelLogViewer\Pattern;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class LaravelLogViewer
 * @package Rap2hpoutre\LaravelLogViewer
 */
class LaravelLogViewer
{
    /**
     * @var string file
     */
    private $file;

    /**
     * @var string folder
     */
    private $folder;

    /**
     * @var string storage_path
     */
    private $storage_path;

    /**
     * Why? Uh... Sorry
     */
    const MAX_FILE_SIZE = 52428800;

    /**
     * @var Level level
     */
    private $level;

    /**
     * @var Pattern pattern
     */
    private $pattern;

    /**
     * LaravelLogViewer constructor.
     */
    public function __construct()
    {
        $this->level = new Level();
        $this->pattern = new Pattern();
        $this->storage_path = function_exists('config') ? config('logviewer.storage_path', storage_path('logs')) : storage_path('logs');

    }

    /**
     * @param string $folder
     */
    public function setFolder($folder)
    {
        if (app('files')->exists($folder)) {
            $this->folder = $folder;
        }
        if(is_array($this->storage_path)) {
            foreach ($this->storage_path as $value) {
                $logsPath = $value . '/' . $folder;
                if (app('files')->exists($logsPath)) {
                    $this->folder = $folder;
                    break;
                }
            }
        } else {
            if ($this->storage_path) {
                $logsPath = $this->storage_path . '/' . $folder;
                if (app('files')->exists($logsPath)) {
                    $this->folder = $folder;
                }
            }
        }
    }

    /**
     * @param string $file
     * @throws \Exception
     */
    public function setFile($file)
    {
        $file = $this->pathToLogFile($file);

        if (app('files')->exists($file)) {
            $this->file = $file;
        }
    }

    /**
     * @param string $file
     * @return string
     * @throws \Exception
     */
    public function pathToLogFile($file)
    {

        if (app('files')->exists($file)) { // try the absolute path
            return $file;
        }
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $folder) {
                if (app('files')->exists($folder . '/' . $file)) { // try the absolute path
                    $file = $folder . '/' . $file;
                    break;
                }
            }
            return $file;
        }

        $logsPath = $this->storage_path;
        $logsPath .= ($this->folder) ? '/' . $this->folder : '';
        $file = $logsPath . '/' . $file;
        // check if requested file is really in the logs directory
        if (dirname($file) !== $logsPath) {
            throw new \Exception('No such log file');
        }
        return $file;
    }

    /**
     * @return string
     */
    public function getFolderName()
    {
        return $this->folder;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return basename($this->file);
    }

    /**
     * @return array
     */
    public function all($parseStack = false)
    {
        $log = array();

        if (!$this->file) {
            $log_file = (!$this->folder) ? $this->getFiles() : $this->getFolderFiles();
            if (!count($log_file)) {
                return [];
            }
            $this->file = $log_file[0];
        }

        $max_file_size = function_exists('config') ? config('logviewer.max_file_size', LaravelLogViewer::MAX_FILE_SIZE) : LaravelLogViewer::MAX_FILE_SIZE;
        if (app('files')->size($this->file) > $max_file_size) {
            return null;
        }

        $file = app('files')->get($this->file);


        return [];

    }

    /**
     * @return array
     */
    public function getFolders()
    {
        $folders = glob($this->storage_path . '/*', GLOB_ONLYDIR);
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $value) {
                $folders = array_merge(
                    $folders,
                    glob($value . '/*', GLOB_ONLYDIR)
                );
            }
        }

        if (is_array($folders)) {
            foreach ($folders as $k => $folder) {
                $folders[$k] = basename($folder);
            }
        }
        return array_values($folders);
    }

    /**
     * @param bool $basename
     * @return array
     */
    public function getFolderFiles($basename = false)
    {
        return $this->getFiles($basename, $this->folder);
    }

    /**
     * @param bool $basename
     * @param string $folder
     * @return array
     */
    public function getFiles($basename = false, $folder = '')
    {
        $pattern = function_exists('config') ? config('logviewer.pattern', '*.log') : '*.log';
        $files = glob(
            $this->storage_path . '/' . $folder . '/' . $pattern,
            preg_match($this->pattern->getPattern('files'), $pattern) ? GLOB_BRACE : 0
        );
        if (is_array($this->storage_path)) {
            foreach ($this->storage_path as $value) {
                $files = array_merge(
                  $files,
                  glob(
                      $value . '/' . $folder . '/' . $pattern,
                      preg_match($this->pattern->getPattern('files'), $pattern) ? GLOB_BRACE : 0
                  )
                );
            }
        }

        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }
}
