<?php namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\ServiceProvider;

class LaravelLogViewerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		if (method_exists($this, 'package')) {
			$this->package('rap2hpoutre/laravel-log-viewer', 'laravel-log-viewer', __DIR__ . '/../../');
		}

		if (method_exists($this, 'loadViewsFrom')) {
			$this->loadViewsFrom(__DIR__.'/../../views', 'laravel-log-viewer');
		}
        
        include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        //register package commands
        $this->app['laravel-log-viewer::commands.publish'] = $this->app->share(function($app)
        {
            return new Console\PublishCommand;
        });
        $this->commands(
            'laravel-log-viewer::commands.publish'
        );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
