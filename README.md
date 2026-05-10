Laravel log viewer
==================

[![Packagist](https://img.shields.io/packagist/v/rap2hpoutre/laravel-log-viewer.svg)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer)
[![Packagist](https://img.shields.io/packagist/l/rap2hpoutre/laravel-log-viewer.svg)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer) 
[![Packagist](https://img.shields.io/packagist/dm/rap2hpoutre/laravel-log-viewer.svg)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/?branch=master) 
[![Author](https://img.shields.io/badge/author-@rap2h-blue.svg)](https://twitter.com/rap2h)


## TL;DR
Log Viewer for Laravel 12 & 13. **Install with composer, create a route to `LogViewerController`**. 
For older Laravel versions and Lumen, please refer to the [older releases](https://github.com/rap2hpoutre/laravel-log-viewer/releases). Please note that those Laravel versions, as well as Lumen, are no longer maintained.
No public assets, no vendor routes, works with and/or without log rotate. Inspired by Micheal Mand's [Laravel 4 log viewer](https://github.com/mikemand/logviewer) (works only with laravel 4.1)

## What ?
Small log viewer for laravel. Looks like this:

![capture d ecran 2014-12-01 a 10 37 18](https://cloud.githubusercontent.com/assets/1575946/5243642/8a00b83a-7946-11e4-8bad-5c705f328bcc.png)

## Install (Laravel)
Install via composer
```bash
composer require rap2hpoutre/laravel-log-viewer
```

Add a route in your web routes file:
```php 
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
```

Go to `http://myapp/logs` or some other route

## Advanced usage
### Customize view
Publish `log.blade.php` into `/resources/views/vendor/laravel-log-viewer/` for view customization:

```bash
php artisan vendor:publish \
  --provider="Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider" \
  --tag=views
``` 

### Edit configuration
Publish `logviewer.php` configuration file into `/config/` for configuration customization:

```bash
php artisan vendor:publish \
  --provider="Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider"
``` 

### Troubleshooting
If you got a `InvalidArgumentException in FileViewFinder.php` error, it may be a problem with config caching. Double check installation, then run `php artisan config:clear`.

