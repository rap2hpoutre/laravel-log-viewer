laravel-log-viewer
==================
TL;DR
-----
The best (IMO) Log Viewer for Laravel 4.2. **Install with composer, create a route to `LogViewerController`**. No public assets, no vendor routes, works with and/or without log rotate. Inspired by Micheal Mand's [Laravel 4 log viewer](https://github.com/mikemand/logviewer) (works only with laravel 4.1)

What ?
------
Small log viewer for laravel. Looks like this:


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
