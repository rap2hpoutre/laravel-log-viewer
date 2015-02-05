Laravel >=4.2 log viewer
======================

[![Latest Stable Version](https://poser.pugx.org/rap2hpoutre/laravel-log-viewer/v/stable.svg)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer) [![Monthly Downloads](https://poser.pugx.org/rap2hpoutre/laravel-log-viewer/d/monthly.png)](https://packagist.org/packages/rap2hpoutre/laravel-log-viewer)

TL;DR
-----
The best (IMO) Log Viewer for Laravel >=4.2. **Install with composer, create a route to `LogViewerController`**. No public assets, no vendor routes, works with and/or without log rotate. Inspired by Micheal Mand's [Laravel 4 log viewer](https://github.com/mikemand/logviewer) (works only with laravel 4.1)

What ?
------
Small log viewer for laravel. Looks like this:

![capture d ecran 2014-12-01 a 10 37 18](https://cloud.githubusercontent.com/assets/1575946/5243642/8a00b83a-7946-11e4-8bad-5c705f328bcc.png)

Install
-------
Install via composer

`composer require rap2hpoutre/laravel-log-viewer:0.1.*`

Update via composer

`composer update`

Add a route in `app/route.php` (or choose another route): 
```php 
Route::get('logs', 'Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
``` 

Go to `http://myapp/logs` or some other route
