<?php

namespace Rap2hpoutre\LaravelLogViewer;

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
        } else if (is_array($this->storage_path)) {

            foreach ($this->storage_path as $value) {

                $logsPath = $value . '/' . $folder;

                if (app('files')->exists($logsPath)) {
                    $this->folder = $folder;
                    break;
                }
            }
        } else {

            $logsPath = $this->storage_path . '/' . $folder;
            if (app('files')->exists($logsPath)) {
                $this->folder = $folder;
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
            throw new \Exception('No such log file: ' . $file);
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
    public function all()
    {
        $log = [];

        if (!$this->file) {
            $log_file = (!$this->folder) ? $this->getFiles() : $this->getFolderFiles();
            if (!count($log_file)) {
                return [];
            }
            $this->file = $log_file[0];
        }

        $max_file_size = function_exists('config') ? config('logviewer.max_file_size', self::MAX_FILE_SIZE) : self::MAX_FILE_SIZE;
        if (app('files')->size($this->file) > $max_file_size) {
            return null;
        }

        if (!is_readable($this->file)) {
            return [[
                'context' => '',
                'level' => '',
                'date' => null,
                'text' => 'Log file "' . $this->file . '" not readable',
                'stack' => '',
            ]];
        }

        $file = app('files')->get($this->file);

        // Divide log file into an array of logs.
        preg_match_all($this->pattern->getPattern('logs'), $file, $parts);

        foreach($parts[0] ?: [] as $part) {
            // Divide the log part into a heading and a stack trace.
            preg_match('/([^\n]*)(.*)/s', $part, $divide);
            $heading = $divide[1];
            $stack = $divide[2];

            preg_match($this->pattern->getPattern('heading', ['loglevels' => join('|', $this->level->all())]), $heading, $current);

            $level = strtolower($current[3]);
            if(empty($level)) {
                continue;
            }

            $log[] = [
                'context' => $current[2],
                'level' => $level,
                'folder' => $this->folder,
                'level_class' => $this->level->cssClass($level),
                'level_img' => $this->level->img($level),
                'date' => $current[1],
                'text' => $current[4],
                'in_file' => isset($current[5]) ? $current[5] : null,
                'stack' => $stack,
            ];
        }

        if (empty($log)) {

            $lines = explode(PHP_EOL, $file);
            $log = [];

            foreach ($lines as $key => $line) {
                $log[] = [
                    'context' => '',
                    'level' => '',
                    'folder' => '',
                    'level_class' => '',
                    'level_img' => '',
                    'date' => $key + 1,
                    'text' => $line,
                    'in_file' => null,
                    'stack' => '',
                ];
            }
        }

        return array_reverse($log);
    }

    /**Creates a multidimensional array
     * of subdirectories and files
     *
     * @param null $path
     *
     * @return array
     */
    public function foldersAndFiles($path = null)
    {
        $contents = array();
        $dir = $path ? $path : $this->storage_path;
        foreach (scandir($dir) as $node) {
            if ($node == '.' || $node == '..') continue;
            $path = $dir . '\\' . $node;
            if (is_dir($path)) {
                $contents[$path] = $this->foldersAndFiles($path);
            } else {
                $contents[] = $path;
            }
        }

        return $contents;
    }

    /**Returns an array of
     * all subdirectories of specified directory
     *
     * @param string $folder
     *
     * @return array
     */
    public function getFolders($folder = '')
    {
        $folders = [];
        $listObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->storage_path . '/' . $folder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($listObject as $fileinfo) {
            if ($fileinfo->isDir()) $folders[] = $fileinfo->getRealPath();
        }
        return $folders;
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
        $files = [];
        $pattern = function_exists('config') ? config('logviewer.pattern', '*.log') : '*.log';
        $fullPath = $this->storage_path . '/' . $folder;

        $listObject = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($listObject as $fileinfo) {
            if (!$fileinfo->isDir() && strtolower(pathinfo($fileinfo->getRealPath(), PATHINFO_EXTENSION)) == explode('.', $pattern)[1])
                $files[] = $basename ? basename($fileinfo->getRealPath()) : $fileinfo->getRealPath();
        }

        arsort($files);

        return array_values($files);
    }

    /**
     * @return string
     */
    public function getStoragePath()
    {
        return $this->storage_path;
    }

    /**
     * @param $path
     *
     * @return void
     */
    public function setStoragePath($path)
    {
        $this->storage_path = $path;
    }

    public static function directoryTreeStructure($storage_path, array $array)
    {
        foreach ($array as $k => $v) {
            if (is_dir($k)) {

                $exploded = explode("\\", $k);
                $show = last($exploded);

                echo '<div class="list-group folder">
				    <a href="?f=' . \Illuminate\Support\Facades\Crypt::encrypt($k) . '">
					    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span
						    class="fa fa-folder"></span> ' . $show . '
				    </a>
			    </div>';

                if (is_array($v)) {
                    self::directoryTreeStructure($storage_path, $v);
                }

            } else {

                $exploded = explode("\\", $v);
                $show2 = last($exploded);
                $folder = str_replace($storage_path, "", rtrim(str_replace($show2, "", $v), "\\"));
                $file = $v;


                echo '<div class="list-group">
				    <a href="?l=' . \Illuminate\Support\Facades\Crypt::encrypt($file) . '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($folder) . '">
					    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span
						    class="fa fa-file"></span> ' . $show2 . '
				    </a>
			    </div>';

            }
        }

        return;
    }


}
