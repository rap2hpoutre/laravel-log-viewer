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
		if (method_exists($this, 'package'))
		{
			$this->package('rap2hpoutre/laravel-log-viewer', 'laravel-log-viewer', __DIR__ . '/../../');
		}

		if (method_exists($this, 'loadViewsFrom'))
		{
			$this->loadViewsFrom(__DIR__.'/../../views', 'laravel-log-viewer');
		}

		if (method_exists($this,'loadTranslationsFrom'))
        {
            $this->loadTranslationsFrom(__DIR__.'/../../lang', 'laravel-log-viewer');
        }

        if (method_exists($this,'publishes'))
        {
            $this->publishes([

                __DIR__.'/../../lang' => resource_path('lang/vendor/laravel-log-viewer'), //Copy lang folder
                __DIR__.'/../../views' =>   resource_path('views/vendor/laravel-log-viewer'), //Copy views folder
                __DIR__.'/../../config/laravel-log-viewer.php' => config_path('laravel-log-viewer.php'), //Copy config file


            ]);
        }



	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-log-viewer.php', 'laravel-log-viewer'
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
