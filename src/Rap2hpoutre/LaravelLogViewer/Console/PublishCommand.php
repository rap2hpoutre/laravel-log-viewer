<?php
namespace Rap2hpoutre\LaravelLogViewer\Console;

use Illuminate\Console\Command;

/**
 * Class PublishCommand
 * @package Rap2hpoutre\LaravelLogViewer
 */
class PublishCommand extends Command
{
    protected $name = 'logviewer:publish';

    protected $description = 'Generates and publishes configuration for Laravel Log Viewer.';

    public function fire()
    {
        $randomStr = \Str::random(10);
		\File::put(__DIR__ .'/../../../config/config.php', "<?php

return [
    
	/*
	|--------------------------------------------------------------------------
	| Laravel Log Viewer Config
	|--------------------------------------------------------------------------
	*/
    'access_key' => '$randomStr',
    
];        
        
        ");
        $this->info('Log Access Key Generated: '. $randomStr);
        $this->call('config:publish', array('package' => 'rap2hpoutre/laravel-log-viewer'));
    }
    
}
