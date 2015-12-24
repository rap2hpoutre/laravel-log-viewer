<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;


class LogViewerController extends Controller
{

    public function index()
    {
        if (request()->input('l')) {
            LaravelLogViewer::setFile(base64_decode(request()->input('l')));
        }

        if (request()->input('dl')) {
            return Response::download(storage_path() . '/logs/' . base64_decode(request()->input('dl')));
        } elseif (Input::has('del')) {
            File::delete(storage_path() . '/logs/' . base64_decode(request()->input('del')));
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
