<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\File;
use Psr\Log\LogLevel;
use ReflectionClass;

/**
 * Class LaravelLogViewer
 * @package Rap2hpoutre\LaravelLogViewer
 */
class LaravelLogViewer
{

    /**
     * @var string file
     */
    private static $file;

    /**
     * @param string $file
     */
    public static function setFile($file)
    {
        if (File::exists(storage_path() . '/logs/' . $file)) {
            self::$file = storage_path() . '/logs/' . $file;
        }
    }

    /**
     * @return string
     */
    public static function getFileName()
    {
        return basename(self::$file);
    }

    /**
     * @return array
     */
    public static function all()
    {
        $log = array();

        $class = new ReflectionClass(new LogLevel);
        $log_levels = $class->getConstants();

        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';

        $file = self::getCurrentFile();

        preg_match_all($pattern, $file, $headings);

        $log_data = preg_split($pattern, $file);

        if ($log_data[0] < 1) {
            $trash = array_shift($log_data);
            unset($trash);
        }

        $levels_classes = [
            'debug' => 'info',
            'info' => 'info',
            'notice' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'danger',
            'alert' => 'danger',
        ];
        $levels_imgs = [
            'debug' => 'info',
            'info' => 'info',
            'notice' => 'info',
            'warning' => 'warning',
            'error' => 'warning',
            'critical' => 'warning',
            'alert' => 'warning',
        ];
        
        
        if (is_array($headings)) {
            foreach ($headings as $h) {
                for ($i=0, $j = count($h); $i < $j; $i++) {
                    foreach ($log_levels as $ll) {
                        if (strpos(strtolower($h[$i]), strtolower('.'.$ll))) {
    
                            $level = strtoupper($ll);
    
                            preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.' . $level . ': (.*?)( in .*?:[0-9]+)?$/', $h[$i], $current);

                            $log[] = array(
                                'level' => $ll,
                                'level_class' => $levels_classes[$ll],
                                'level_img' => $levels_imgs[$ll],
                                'date' => isset($current[1]) ? $current[1] : null,
                                'text' => isset($current[2]) ? $current[2] : null,
                                'in_file' => isset($current[3]) ? $current[3] : null,
                                'stack' => preg_replace("/^\n*/", '', $log_data[$i])
                            );
                        }
                    }
                }
            }
        }

        return array_reverse($log);
    }

    /**
     * @param bool $basename
     * @return array
     */
    public static function getFiles($basename = false)
    {
        $files = glob(storage_path() . '/logs/*');
        $files = array_reverse($files);
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return $files;
    }

    /**
     * @return array
     */
    private static function getCurrentFile()
    {
        if (!self::$file) {
            $log_file = self::getFiles();
            if(!count($log_file)) {
                return [];
            }
            self::$file = $log_file[0];
        }

        return File::get(self::$file);
    }
}
