<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
*/
/*
Route::group(['middleware' => ['api','cors'],'prefix' => 'api'], function () {
    Route::post('register', 'Api\ApiAuthController@register');
    Route::post('login', 'Api\ApiAuthController@login');
    Route::group(['middleware' => 'jwt-auth'], function () {
    	Route::post('get_user_details', 'Api\ApiAuthController@get_user_details');
    });
});
*/

Route::group(['middleware' => ['api','cors']], function ()
{
    Route::post('/testpayu1', 'Api\User\UserController@testConsultaPayu');

    Route::post('register', 'Api\AuthController@register');
    Route::post('login', 'Api\AuthController@login');
    Route::post('refreshToken', 'Api\AuthController@refreshToken');

    //Este Grupo Necesita Token (usa el middleware jwt-auth)
    Route::group(['middleware' => 'jwt-auth'], function () {

        Route::get('me', 'Api\AuthController@me');

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'Api\User\UserController@all');
            Route::get('{id}', 'Api\User\UserController@find');
            Route::get('{id}/estudiantes', 'Api\User\UserController@estudiantes');
            Route::get('{id}/materias', 'Api\User\UserController@materias');
            Route::post('{id}/estudiantes', 'Api\User\UserController@addEstudiantes');
            Route::put('{idProfesor}/addMateria/{idMateria}', 'Api\User\UserController@addMateria');
        });

        Route::group(['prefix' => 'noticias'], function () {
            Route::get('/', 'Api\Noticias\NoticiasController@all');
            Route::post('add', 'Api\Noticias\NoticiasController@add');
            Route::get('{id}', 'Api\Noticias\NoticiasController@find');
            Route::delete('{id}', 'Api\Noticias\NoticiasController@delete');
        });

        Route::group(['prefix' => 'estudiantes'], function () {
            Route::get('/', 'Api\Estudiante\EstudianteController@all');
            Route::post('add', 'Api\Estudiante\EstudianteController@add');
            Route::get('{id}/materias', 'Api\Estudiante\EstudianteController@materias');
        });
        Route::group(['prefix' => 'materias'], function () {
            Route::get('/', 'Api\Materia\MateriaController@all');
            Route::post('add', 'Api\Materia\MateriaController@add');
            Route::get('{id}', 'Api\Materia\MateriaController@find');
            Route::get('grado/{grado}', 'Api\Materia\MateriaController@byGrado');
            Route::get('{id}/material', 'Api\Materia\MateriaController@materiales');
        });
        Route::group(['prefix' => 'calificaciones'], function () {
            Route::get('/', 'Api\Calificaciones\CalificacionesController@all');
            Route::post('add', 'Api\Calificaciones\CalificacionesController@add');
            Route::put('edit', 'Api\Calificaciones\CalificacionesController@edit');
            Route::get('/estudiante/{idEstudiante}', 'Api\Calificaciones\CalificacionesController@byEstudiante');
            Route::get('/estudiante/{idEstudiante}/materia/{idMateria}', 'Api\Calificaciones\CalificacionesController@byEstudianteByMateria');
            Route::get('/profesor/{idProfesor}', 'Api\Calificaciones\CalificacionesController@byProfesor');
            Route::put('/profesor/{idProfesor}', 'Api\Calificaciones\CalificacionesController@editByProfesor');
            Route::get('/profesor/{idProfesor}/materia/{idMateria}', 'Api\Calificaciones\CalificacionesController@byProfesorByMateria');
        });

        Route::group(['prefix' => 'horarios'], function () {
            Route::get('/', 'Api\Horario\HorariosController@all');
            Route::get('horario', 'Api\Horario\HorariosController@horario');
            Route::post('add', 'Api\Horario\HorariosController@add');
        });

        Route::group(['prefix' => 'materiales'], function () {
            Route::get('/', 'Api\Material\MaterialController@all');
            Route::post('add', 'Api\Material\MaterialController@add');
            Route::get('{id}', 'Api\Material\MaterialController@find');
            Route::put('{id}', 'Api\Material\MaterialController@edit');
            Route::delete('{id}', 'Api\Material\MaterialController@delete');
        });

        Route::group(['prefix' => 'carrito'], function () {
            Route::post('add', 'Api\Carrito\CarritoController@add');
            Route::get('actual', 'Api\Carrito\CarritoController@carrito');
            Route::put('edit', 'Api\Carrito\CarritoController@edit');
            Route::put('vaciar', 'Api\Carrito\CarritoController@vaciar');
        });

        Route::group(['prefix' => 'articulo'], function () {
            Route::post('add', 'Api\Articulo\ArticuloController@add');
            Route::get('{id}', 'Api\Articulo\ArticuloController@find');
        });
    });


});