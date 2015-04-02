<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\View;

class LogViewerController extends \Illuminate\Routing\Controller
{

    public function index()
    {
        if(\Input::get('access_key') !== \Config::get('laravel-log-viewer::config.access_key'))
        {
            return \Response::make('Access key missing or invalid', 403);
        }
        
        if (\Input::get('l')) {
            LaravelLogViewer::setFile(base64_decode(\Input::get('l')));
        }

        $logs = LaravelLogViewer::all();

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

}
