<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\View;

class LogViewerController extends \BaseController
{

    public function index()
    {
        if (\Input::get('l')) {
            LaravelLogViewer::setFile(\Crypt::decrypt(\Input::get('l')));
        }

        $logs = LaravelLogViewer::all();

        View::addNamespace('laravel-log-viewer', __DIR__.'/../views');

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

}
