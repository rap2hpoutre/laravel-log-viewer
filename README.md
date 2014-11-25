laravel-log-viewer
==================
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
