<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\Log;
use Config;

if (class_exists("\\Illuminate\\Routing\\Controller")) {
    class BaseController extends \Illuminate\Routing\Controller
    {
    }
} elseif (class_exists("Laravel\\Lumen\\Routing\\Controller")) {
    class BaseController extends \Laravel\Lumen\Routing\Controller
    {
    }
}

class LogViewerController extends BaseController
{
    protected $request;

    public function __construct()
    {
        $this->request = app('request');
        $log_level = app('config')['app']['log_viewer_show'];
        if (strlen($log_level)>0) {
            $log_level=explode(',', $log_level);
            LaravelLogViewer::setLogLevel($log_level);
        }
    }

    public function index()
    {
        if ($this->request->input('l')) {
            LaravelLogViewer::setFile(base64_decode($this->request->input('l')));
        }

        if ($this->request->input('dl')) {
            return $this->download(LaravelLogViewer::pathToLogFile(base64_decode($this->request->input('dl'))));
        } elseif ($this->request->has('del')) {
            app('files')->delete(LaravelLogViewer::pathToLogFile(base64_decode($this->request->input('del'))));
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('delall')) {
            foreach (LaravelLogViewer::getFiles(true) as $file) {
                app('files')->delete(LaravelLogViewer::pathToLogFile($file));
            }
            return $this->redirect($this->request->url());
        }

        return app('view')->make('laravel-log-viewer::log', [
            'logs' => LaravelLogViewer::all(),
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }

        return app('redirect')->to($to);
    }

    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }

        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }
}
