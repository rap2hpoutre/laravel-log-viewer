<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
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
        }

		if (\Input::get('del')) {
			$file = storage_path() . '/logs/' . base64_decode(\Input::get('del'));

			try
			{
				if (file_exists($file))	unlink($file);
			}
			catch (\Exception $ex)
			{
				// DO NOTHING
			}

			$url = parse_url(\Request::getRequestUri(), PHP_URL_HOST) . parse_url(\Request::getRequestUri(), PHP_URL_PATH);

			return \Redirect::to($url);
		}

        $logs = LaravelLogViewer::all();

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

}
