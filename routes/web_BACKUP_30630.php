<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

<<<<<<< HEAD
Route::get('/', 'HomeController@index');
Route::get('/getStartingPins', 'HomeController@getStartingPins');
Route::post('/sync', 'DataController@sync');
=======
Route::group(['middleware' => 'auth'], function(){
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/getStartingPins', 'HomeController@getStartingPins');
});
>>>>>>> 2b7cb8fefaadfdde9b2baa0e1f293a76e8b8d79d
