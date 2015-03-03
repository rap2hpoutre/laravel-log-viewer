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

        if (\Input::get('d')) {
            $log_file = glob(storage_path().'/logs'.'/'.base64_decode(\Input::get('d')));
            \File::delete($log_file);
            return redirect()->to('logs');
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
