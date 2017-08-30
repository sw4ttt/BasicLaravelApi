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
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check())return view('/home');
    else return view('/auth/login');
});
Route::get('/login', function () {
    if (Auth::check())return view('/home');
    else return view('/auth/login');
});
Route::post('/login', 'Web\Auth\AuthController@authenticate');
Route::get('/register', function () {
    if (Auth::check())return view('/home');
    else return view('/auth/register');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', function () {   return view('/home');});
    Route::post('/logout', 'Web\Auth\AuthController@logout');

    Route::get('/users', 'Web\UsersController@all');
    Route::get('/users/add', function () {  return view('/users/add');});
    Route::post('/users/add', 'Web\UsersController@add');
});