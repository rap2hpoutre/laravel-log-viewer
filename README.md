Laravel 5 log viewer
======================

[![Packagist](https://img.shields.io/packagist/v/rap2hpoutre/laravel-log-viewer.svg)]()
[![Packagist](https://img.shields.io/packagist/l/rap2hpoutre/laravel-log-viewer.svg)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer) [![Packagist](https://img.shields.io/packagist/dm/rap2hpoutre/laravel-log-viewer.svg)]() [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/rap2hpoutre/laravel-log-viewer/build-status/master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2974f007-ff84-41a7-8954-7cda41ca5f84/mini.png)](https://insight.sensiolabs.com/projects/2974f007-ff84-41a7-8954-7cda41ca5f84)

TL;DR
-----
The best (IMO) Log Viewer for Laravel 5 (compatible with 4.2 too). **Install with composer, create a route to `LogViewerController`**. No public assets, no vendor routes, works with and/or without log rotate. Inspired by Micheal Mand's [Laravel 4 log viewer](https://github.com/mikemand/logviewer) (works only with laravel 4.1)

What ?
------
Small log viewer for laravel. Looks like this:

![capture d ecran 2014-12-01 a 10 37 18](https://cloud.githubusercontent.com/assets/1575946/5243642/8a00b83a-7946-11e4-8bad-5c705f328bcc.png)

Install
-------
Install via composer
```
composer require rap2hpoutre/laravel-log-viewer
```

Add Service Provider to `config/app.php` in `providers` section
```php
Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
```

Add a route in `app/Http/routes.php` (or choose another route): 
```php 
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
``` 

Go to `http://myapp/logs` or some other route
