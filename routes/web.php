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

Route::get('/', function () {
    return view('welcome');
});

Route::get('movie', 'MovieController@index');
Route::get('upload-movie', 'MovieController@uploadMovie');
Route::get('update-movie', 'MovieController@update');
Route::get('delete-movie/{id}', 'MovieController@delete');

Route::get('movie/{id}', 'AudioController@index');
Route::get('audios', 'AudioController@getAudios');
Route::get('upload-audio', 'AudioController@uploadAudio');
Route::get('update-audio', 'AudioController@update');
Route::get('delete-audio', 'AudioController@delete');

Route::get('recorder', 'UploadController@index');
Route::post('upload-record', array('as' => 'upload-record', 
    'uses' => 'UploadController@uploadRecord'));
