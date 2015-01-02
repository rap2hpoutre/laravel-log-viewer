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
     * @var  file
     */
    private static $file;

    /**
     * @param $file
     */
    public static function setFile($file)
    {
        if (File::exists(storage_path() . '/logs/' . $file)) {
            self::$file = storage_path() . '/logs/' . $file;
        }
    }

    /**
     * @return file
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


        if (!self::$file) {
            $log_file = self::getFiles();
            self::$file = $log_file[0];
        }

        $file = File::get(self::$file);

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
                            'date' => $current[1],
                            'text' => $current[2],
                            'in_file' => isset($current[3]) ? $current[3] : null,
                            'stack' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }
                }
            }
        }

        $log = array_reverse($log);
        return $log;
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
}
