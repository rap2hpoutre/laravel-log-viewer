<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class LogViewerController extends Controller
{
    /**
     * Config filename.
     * @var string
     */
    protected static $configFile = 'laravel-log-viewer';

    public function index()
    {

        if (Request::input('l')) {
            LaravelLogViewer::setFile(base64_decode(Request::input('l')));
        }

        if (Request::input('dl')) {
            return Response::download(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('dl'))));
        } elseif (Request::has('del')) {
            File::delete(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('del'))));
            return Redirect::to(Request::url());
        }

        $logs = LaravelLogViewer::all();

        return View::make(self::cfgHas('view.log') ? self::cfg('view.log') : 'laravel-log-viewer::log', [
            'layout' => self::cfgHas('view.layout') ? self::cfg('view.layout') : 'laravel-log-viewer::layout',
            'yieldName' => self::cfgHas('view.yieldName') ? self::cfg('view.yieldName') : 'content',
            'container_fluid' => self::cfgHas('view.container-fluid') ? self::cfg('view.container-fluid') : true,
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

    /**
     * Retrieve a configuration value for the log viewer.
     * @param $key
     * @return mixed
     */
    private static function cfg($key)
    {
        return config()->get(self::$configFile . '.' . $key);
    }

    /**
     * Check if a configuration value for the log viewer exists.
     * @param $key
     * @return mixed
     */
    private static function cfgHas($key)
    {
        return config()->has(self::$configFile . '.' . $key);
    }
}
