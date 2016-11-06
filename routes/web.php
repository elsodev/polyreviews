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
Route::post('/sync', 'DataController@sync');
Route::get('/search', 'HomeController@search');

Route::get('/get/start', 'HomeController@getStartingPins');
Route::get('/get/loc', 'HomeController@changeLocation');

Route::get('/get/google', 'DataController@getGoogleData');
Route::get('/get/facebook', 'DataController@getFacebookData');


Route::group(['middleware' => 'auth'], function(){
    Route::post('vote', 'DataController@vote');
});
