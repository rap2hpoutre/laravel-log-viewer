<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\View;

class LogViewerController extends \BaseController
{

    public function index()
    {
        $logs = LaravelLogViewer::all();
        View::addNamespace('laravel-log-viewer', __DIR__.'/../views');
        return View::make('laravel-log-viewer::log', ['logs' => $logs]);
    }

}
