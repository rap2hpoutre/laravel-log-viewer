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

        $logs = LaravelLogViewer::all();

        if (\Input::get('n')) {
          $offset = \Input::get('n');
          $logs = array_slice($logs, 0, count($logs) - $offset);
        }

        if (\Request::ajax()) {
          $tmpLogs = $logs;
          $logs = [];

          // Add log['key'] to the array.
          foreach($tmpLogs as $key => $log) {
            $log['key'] = $key;
            $logs[] = $log;
          }
          return $logs;
        }

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

}
