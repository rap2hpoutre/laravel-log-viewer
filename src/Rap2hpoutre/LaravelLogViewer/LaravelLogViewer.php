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


        return $this->convertStringExceptionLogToArray($file, $parseStack);

    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception  $e
     * @return array
     */
    //https://laravel.com/api/[x.x]/Illuminate/Foundation/Exceptions/Handler.html#method_convertExceptionToArray
    public function convertExceptionToArray(\Exception $e)
    {
        return
            // config('app.debug') ?
        [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        // ] : [
        //     'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }

    /**
     *
     * @param  String  $e
     * @return array
     */
    public function convertStringExceptionLogToArray(String $e, $parseStack = false)
    {
        $log = array();

        preg_match_all($this->pattern->getPattern('logs'), $e, $headings);

        if (!is_array($headings)) {
            return $log;
        }

        $log_data = preg_split($this->pattern->getPattern('logs'), $e);

        if ($log_data[0] < 1) {
            array_shift($log_data);
        }
        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {
                foreach ($this->level->all() as $level) {
                    if (strpos(strtolower($h[$i]), '.' . $level) || strpos(strtolower($h[$i]), $level . ':')) {
                        // $h[$i] = str_replace('\\\\', '\\', $h[$i]);
                        $current = [];
                        preg_match($this->pattern->getPattern('current_log', 0) . $level . $this->pattern->getPattern('current_log', 1), $h[$i], $current);
                        if (!isset($current[4])) {
                            preg_match($this->pattern->getPattern('current_log', 0) . $level . $this->pattern->getPattern('current_log', 2), $h[$i], $current);
                            if (!$current) {
                                continue;
                            }
                            $log[] = array(
                                'context' => $current[3],
                                'level' => $level,
                                'folder' => $this->folder,
                                'level_class' => $this->level->cssClass($level),
                                'level_img' => $this->level->img($level),
                                'date' => $current[1],
                                'message' => $current[4]." ".(isset($current[5]) ? $current[5] : ""). " ",
                                // 'exception' => null,
                                // 'code' => null,
                                // 'file' => null,
                                // 'line' => null,
                                // 'trace' => null,
                            );
                        } else {
                            // $log_data[$i] = str_replace('\\\\', '\\', $log_data[$i]);
                            $trace = $log_data[$i];
                            if ($parseStack) {
                                $trace = $this->convertStringExceptionStackToArray($log_data[$i]);
                            }
                            $log[] = array(
                                'context' => $current[3],
                                'level' => $level,
                                'folder' => $this->folder,
                                'level_class' => $this->level->cssClass($level),
                                'level_img' => $this->level->img($level),
                                'date' => $current[1],
                                'message' => $current[6],
                                'exception' => $current[4],
                                // 'code' => $current[5],
                                'file' => isset($current[7]) ? $current[7] : null,
                                'line' => isset($current[8]) ? intval($current[8]) : null,
                                'trace' => $trace,
                            );
                        }
                    }
                }
            }
        }

        return array_reverse($log);
    }

    /**
     *
     * @param  String  $e
     * @return array
     */
    public function convertStringExceptionToArray(String $e)
    {
        $log = array();

        $eArray = preg_split("/\n/", $e);
        preg_match($this->pattern->getPattern('current_log_string'), $eArray[0], $current);

        $log['message'] = $current[2];
        $log['exception'] = $current[1];
        $log['file'] = isset($current[3]) ? $current[3] : null;
        $log['line'] = isset($current[4]) ? intval($current[4]) : null;
        $log['trace'] = $this->convertArrayExceptionStackStringToArray(array_slice($eArray, 2));

        return $log;
    }

    /**
     *
     * @param  String  $eStack
     * @return array
     */
    public function convertStringExceptionStackToArray(String $eStack)
    {
        $eStack = preg_replace($this->pattern->getPattern('stack_init_section'), '', $eStack);
        $eStackArray = preg_split("/\\n *\#/", $eStack);
        return $this->convertArrayExceptionStackStringToArray($eStackArray);
    }

    /**
     *
     * @param  array  $eStackArray
     * @return array
     */
    public function convertArrayExceptionStackStringToArray(array $eStackArray)
    {
        $eStackArrayReturn = [];

        foreach ($eStackArray as $s) {
            $s = preg_replace($this->pattern->getPattern('stack_startWith'), '', $s);
            if (Str::startsWith($s, "{main}")) {
                break;
            }
            if (Str::startsWith($s, "[internal function]: ")) {
                $s = str_replace("[internal function]: ", "", $s);
                $call = new \StdClass();
                preg_match($this->pattern->getPattern('stack', 0), $s, $matchs);
                if ($matchs) {
                    $call->function = $matchs[3];
                    $call->class = $matchs[1];
                    $call->type = $matchs[2];
                    // $call->args = $matchs[4];
                    $eStackArrayReturn[] = $call;
                } else {
                    preg_match($this->pattern->getPattern('stack', 1), $s, $matchs);
                    $call->function = $matchs[1];
                    // $call->args = $matchs[2];
                    $eStackArrayReturn[] = $call;
                }
            } else {
                preg_match($this->pattern->getPattern('stack', 2), $s, $matchs);
                if ($matchs) {
                    $call = new \StdClass();
                    $call->file = $matchs[1];
                    $call->line = intval($matchs[2]);
                    $tmp = $matchs[3];
                    preg_match($this->pattern->getPattern('stack', 0), $tmp, $matchs);
                    if ($matchs) {
                        $call->function = $matchs[3];
                        $call->class = $matchs[1];
                        $call->type = $matchs[2];
                        if (isset($matchs[4])) {
                            // $call->args = $matchs[4];
                        }
                    } else {
                        preg_match($this->pattern->getPattern('stack', 1), $tmp, $matchs);
                        if ($matchs) {
                            $call->function = $matchs[1];
                        }
                        if (isset($matchs[2])) {
                            // $call->args = $matchs[2];
                        }
                    }
                    $eStackArrayReturn[] = $call;
                } else {
                    $eStackArrayReturn[] = $s;
                }
            }
        }
        return $eStackArrayReturn;
    }

    /**
     *
     * @param  array  $stackArray
     * @return String
     */
    public static function convertArrayExceptionStacArrayToString(array $stackArray)
    {
        $stack = "";
        $stack .= "[stacktrace]";
        $stack .= "\n";
        foreach ($stackArray as $index=>$data) {
            $dataArray = (array)$data;
            $stack .= "#$index ";
            if (isset($dataArray['file'])) {
                $stack .= "{$dataArray['file']} ({$dataArray['line']}): ";
            }
            $strClass = "";
            if (isset($dataArray['class'])) {
                $strClass = $dataArray['class'];
            }
            $strType = "";
            if (isset($dataArray['type'])) {
                $strType = $dataArray['type'];
            }
            $strFunction = "";
            if (isset($dataArray['function'])) {
                 $strFunction = $dataArray['function'];
            }
            $strArgs = "";
            if (isset($dataArray['args'])) {
                $strArgs = $dataArray['args'];
            }
            $stack .= "{$strClass}{$strType}{$strFunction}({$strArgs})";
            $stack .= "\n";
        }
        return $stack;
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
