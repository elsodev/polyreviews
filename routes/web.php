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


Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/getStartingPins', 'HomeController@getStartingPins');
Route::post('/sync', 'DataController@sync');
Route::get('/get/google', 'DataController@getGoogleData');


Route::group(['middleware' => 'auth'], function(){
});
