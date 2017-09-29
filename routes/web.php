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
use App\Horario;
use Illuminate\Support\Facades\Storage;
use App\Material;
use App\Articulo;

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

Route::get('images/{filename}', function ($filename)
{
    if(!Storage::exists('images/'.$filename))  return response('Imagen no existe.',404);
    $contents = Storage::get('images/'.$filename);
    $response = Response::make($contents, 200);
    return $response->header("Content-Type", Storage::mimeType('images/'.$filename));
});
Route::get('images-app/{filename}', function ($filename)
{
    if(!Storage::exists('images-app/'.$filename))  return response('Imagen no existe.',404);
    $contents = Storage::get('images-app/'.$filename);
    $response = Response::make($contents, 200);
    return $response->header("Content-Type", Storage::mimeType('images-app/'.$filename));
});
Route::get('images-app-default/{filename}', function ($filename)
{
    if(!Storage::exists('images-app-default/'.$filename))  return response('Imagen no existe.',404);
    $contents = Storage::get('images-app-default/'.$filename);
    $response = Response::make($contents, 200);
    return $response->header("Content-Type", Storage::mimeType('images-app-default/'.$filename));
});

Route::get('files/{filename}', function ($filename)
{
    if(!Storage::exists('files/'.$filename))  return response('Archivo no existe.',404);
    $contents = Storage::get('files/'.$filename);
    $response = Response::make($contents, 200);
    return $response->header("Content-Type", Storage::mimeType('files/'.$filename));
});

Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', function () {   return view('/home');});
    Route::post('/logout', 'Web\Auth\AuthController@logout');

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'Web\UsersController@all');
        Route::get('/add', function () {  return view('/users/add');});
        Route::post('/add', 'Web\UsersController@add');
    });

    Route::group(['prefix' => 'materias'], function () {
        Route::get('/', 'Web\MateriasController@all');
        Route::get('/add', function () {
            return view('/materias/add',['profesores'=>User::where('type','PROFESOR')->get()]);
        });
        Route::post('/add', 'Web\MateriasController@add');
        Route::get('/edit/{id}', function ($id) {
            $materia = Materia::find($id);
            $profesor = $materia->profesores()->first();

            $materia->profesor = $profesor->nombre;

            if(!is_null($materia))
                return view('/materias/edit',[
                    'materia'=>Materia::find($id),
                    'profesores'=>User::where('type','PROFESOR')->get(),
                ]);
            return back()->withErrors(['invalid'=>['El id de materia seleccionado no es valido.']]);
        });
        Route::post('/edit/{id}', 'Web\MateriasController@edit');
        Route::post('/delete/{id}', 'Web\MateriasController@delete');
    });

    Route::get('/orders', 'Web\OrdersController@all');

    Route::group(['prefix' => 'articulos'], function () {
        Route::get('/', 'Web\ArticulosController@all');
        Route::get('/add', function () {
            return view('/articulos/add');
        });
        Route::post('/add', 'Web\ArticulosController@add');
        Route::get('/edit/{id}', function ($id) {
            $articulo = Articulo::find($id);
            return !is_null($articulo)
                ?view('/articulos/edit',['articulo'=>$articulo])
                :back()->withErrors(['invalid'=>['El id de materia seleccionado no es valido.']]);
        });
        Route::post('/edit/{id}', 'Web\ArticulosController@edit');
        Route::post('/delete/{id}', 'Web\ArticulosController@delete');
    });

    Route::group(['prefix' => 'horarios'], function () {
        Route::get('/', 'Web\HorariosController@all');
        Route::get('/add', function () {
            return view('/horarios/add',['horarios'=>Horario::all()]);
        });
    });

    Route::group(['prefix' => 'materiales'], function () {
        Route::get('/', 'Web\MaterialesController@all');
        Route::get('/add', function () {
            return view('/materiales/add',['materias'=>Materia::all()]);
        });
        Route::post('/add', 'Web\MaterialesController@add');
        Route::get('/edit/{id}', function ($id) {
            $material = Material::find($id);
            return view('/materiales/edit',[
                'material'=>$material,
                'materias'=>Materia::all(),
            ]);
        });
        Route::post('/edit/{id}', 'Web\MaterialesController@edit');
        Route::post('/delete/{id}', 'Web\MaterialesController@delete');
    });

    Route::group(['prefix' => 'noticias'], function () {
        Route::get('/', 'Web\NoticiasController@all');
        Route::get('/add', function () {
            return view('/noticias/add');
        });
        Route::post('/add', 'Web\NoticiasController@add');
    });

    Route::group(['prefix' => 'notificaciones'], function () {
//        Route::get('/', 'Web\HorariosController@all');
        Route::get('/add', function () {
            return view('/notificaciones/add');
        });
        Route::post('/add', 'Web\NotificacionesController@add');
    });

    Route::group(['prefix' => 'config'], function () {
//        Route::get('/', 'Web\HorariosController@all');
        Route::get('/images-app/add', function () {
            return view('/config/images-app/add');
        });
        Route::post('/images-app/add', 'Web\Configuracion\WebResourcesController@addImagenesApp');
    });


});