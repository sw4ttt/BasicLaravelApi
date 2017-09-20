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
use App\User;
use App\Materia;

Route::get('/', function () {
    if (Auth::check())return view('/home');
    else return view('/auth/login');
});
Route::get('/login', function () {
    if (Auth::check())return view('/home');
    else return view('/auth/login');
});
Route::post('/login', 'Web\Auth\AuthController@authenticate');
//Route::get('/register', function () {
//    if (Auth::check())return view('/home');
//    else return view('/auth/register');
//});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', function () {   return view('/home');});
    Route::post('/logout', 'Web\Auth\AuthController@logout');

    Route::get('/users', 'Web\UsersController@all');
    Route::get('/users/add', function () {  return view('/users/add');});
    Route::post('/users/add', 'Web\UsersController@add');

    Route::get('/orders', 'Web\OrdersController@all');

    Route::get('/materias', 'Web\MateriasController@all');
    Route::get('/materias/add', function () {
        return view('/materias/add',['profesores'=>User::where('type','PROFESOR')->get()]);
    });
    Route::post('/materias/add', 'Web\MateriasController@add');
    Route::get('/materias/edit/{id}', function ($id) {
        $materia = Materia::find($id);
        if(!is_null($materia))
            return view('/materias/edit',[
                'materia'=>Materia::find($id),
                'profesores'=>User::where('type','PROFESOR')->get()
            ]);
        return back()->withErrors(['invalid'=>['El id de materia seleccionado no es valido.']]);
    });
    Route::post('/materias/edit/{id}', 'Web\MateriasController@edit');
});