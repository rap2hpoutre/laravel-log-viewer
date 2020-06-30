<?php

namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\Crypt;

if (class_exists("\\Illuminate\\Routing\\Controller")) {	
    class BaseController extends \Illuminate\Routing\Controller {}	
} elseif (class_exists("Laravel\\Lumen\\Routing\\Controller")) {	
    class BaseController extends \Laravel\Lumen\Routing\Controller {}	
}

/**
 * Class LogViewerController
 * @package Rap2hpoutre\LaravelLogViewer
 */
class LogViewerController extends BaseController
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var LaravelLogViewer
     */
    private $log_viewer;

    /**
     * @var Level
     */
    private $log_level;

    /**
     * @var string
     */
    protected $view_log = 'laravel-log-viewer::log';

    /**
     * LogViewerController constructor.
     */
    public function __construct()
    {
        $this->log_viewer = new LaravelLogViewer();
        $this->log_level = new Level();
        $this->request = app('request');
    }

    public function filterByLevel(array $logs, string $filter)
    {
        $c = count($logs);
        for ($i = 0; $i < $c; $i++) {
            if ($logs[$i]['level'] === $filter) {
                continue;
            }

            unset($logs[$i]);
        }

        return $logs;
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function index()
    {
        $logs = $this->log_viewer->all();
        $levels_counts = $this->log_level->getLevelsCounts($logs);
        $folderFiles = [];

        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
            $folderFiles = $this->log_viewer->getFolderFiles(true);
        }
        if ($this->request->input('l')) {
            $this->log_viewer->setFile(Crypt::decrypt($this->request->input('l')));
        }
        if ($filter = $this->request->input('filter')) {
            if (array_key_exists('level', $filter)) {
                $logs = $this->filterByLevel($logs, $filter['level']);
            }
        }

        if ($early_return = $this->earlyReturn()) {
            return $early_return;
        }

        $data = [
            'logs' => $logs,
            'folders' => $this->log_viewer->getFolders(),
            'current_folder' => $this->log_viewer->getFolderName(),
            'folder_files' => $folderFiles,
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $this->log_viewer->getFileName(),
            'standardFormat' => true,
            'levels_classes' => $this->log_level->getLevelsClasses(),
            'levels_counts' => $levels_counts
        ];

        if ($this->request->wantsJson()) {
            return $data;
        }

        if (is_array($data['logs']) && count($data['logs']) > 0) {
            $firstLog = reset($data['logs']);
            if (!$firstLog['context'] && !$firstLog['level']) {
                $data['standardFormat'] = false;
            }
        }

        return app('view')->make($this->view_log, $data);
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    private function earlyReturn()
    {
        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
        }

        if ($this->request->input('dl')) {
            return $this->download($this->pathFromInput('dl'));
        } elseif ($this->request->has('clean')) {
            app('files')->put($this->pathFromInput('clean'), '');
            return $this->redirect($this->request->headers->get('referer'));
        } elseif ($this->request->has('del')) {
            app('files')->delete($this->pathFromInput('del'));
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('delall')) {
            $files = ($this->log_viewer->getFolderName())
                        ? $this->log_viewer->getFolderFiles(true)
                        : $this->log_viewer->getFiles(true);
            foreach ($files as $file) {
                app('files')->delete($this->log_viewer->pathToLogFile($file));
            }
            return $this->redirect($this->request->url());
        }
        return false;
    }

    /**
     * @param string $input_string
     * @return string
     * @throws \Exception
     */
    private function pathFromInput($input_string)
    {
        return $this->log_viewer->pathToLogFile(Crypt::decrypt($this->request->input($input_string)));
    }

    /**
     * @param $to
     * @return mixed
     */
    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }

        return app('redirect')->to($to);
    }

    /**
     * @param string $data
     * @return mixed
     */
    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }

        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }
}
