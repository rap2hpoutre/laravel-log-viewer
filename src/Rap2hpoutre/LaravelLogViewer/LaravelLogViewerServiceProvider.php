<?php

namespace Rap2hpoutre\LaravelEpilog;

use Illuminate\Support\ServiceProvider;

class LaravelEpilogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(array(
            __DIR__.'/../../config/config.php' => config_path('epilog.php')
        ));

        $logger = \Log::getMonolog();

        $logger->pushProcessor(function ($record) {
            $info = "\n---\n";
            if (\Auth::check()) {
                $info .= 'User #' . Auth::user()->id . ' (' . Auth::user()->email . ') - ';
            }
            if (isset($_SERVER['REMOTE_ADDR'])) $info .= 'IP: ' . $_SERVER['REMOTE_ADDR'];
            if (isset($_SERVER['REQUEST_URI'])) $info .= "\n" . $_SERVER['REQUEST_METHOD'] . " " . url($_SERVER['REQUEST_URI']);
            if (isset($_SERVER['HTTP_REFERER'])) $info .= "\nReferer: " . $_SERVER['HTTP_REFERER'];
            $info .= "\n---";
            if (strpos($record['message'], "\n")) {
                $record['message'] = preg_replace("/\n/", $info . "\n", $record['message'], 1);
            } else {
                $record['message'] .= $info . "\n";
            }
            return $record;
        });

        if (app()->environment('TODO')) {
            $slackHandler = new \Monolog\Handler\SlackHandler(
                config('epilog.slack.token'),
                config('epilog.slack.channel'),
                config('epilog.slack.username'),
                true,
                ':skull:',
                \Monolog\Logger::ERROR
            );
            $logger->pushHandler($slackHandler);
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
