<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Input;


class LogViewerController extends Controller
{

    public function index()
    {

        if (Input::get('l')) {
            LaravelLogViewer::setFile(base64_decode(Request::input('l')));
        }

        if (Input::get('dl')) {
            return Response::download(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('dl'))));
        } elseif (Input::has('del')) {
            File::delete(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('del'))));
            return Redirect::to(Request::url());
        }

        $logs = LaravelLogViewer::all();

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }
}
