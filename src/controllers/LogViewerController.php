<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\View;

class LogViewerController extends \Illuminate\Routing\Controller
{

    public function index()
    {
        if (\Input::get('l')) {
            LaravelLogViewer::setFile(base64_decode(\Input::get('l')));
        }

        if (\Input::get('dl')) {
            return \Response::download(storage_path() . '/logs/' . base64_decode(\Input::get('dl')));
        } elseif (\Input::has('del')) {
            \File::delete(storage_path() . '/logs/' . base64_decode(\Input::get('del')));
            return \Redirect::to(\Request::url());
        }

        $logs = LaravelLogViewer::all();

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

}
