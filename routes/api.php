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

//https://moviecrew.herokuapp.com/api/login?email=&password=boner123

Route::group(['middleware' => ['api','cors']], function () 
{
    // Grupo Normal
    Route::post('register', 'Api\AuthController@register');
    Route::post('login', 'Api\AuthController@login');

    //Este Grupo Necesita Token (usa el middleware jwt-auth)
    Route::group(['middleware' => 'jwt-auth'], function () {

        Route::get('user/{id}', 'Api\User\UserController@find');

        Route::group(['prefix' => 'noticias'], function () {
            Route::get('/', 'Api\Noticias\NoticiasController@all');
            Route::post('add', 'Api\Noticias\NoticiasController@add');
            Route::get('{id}', 'Api\Noticias\NoticiasController@find');
            Route::delete('{id}', 'Api\Noticias\NoticiasController@delete');
        });
    });

    Route::group(['middleware' => 'jwt-refresh'], function () {
        Route::get('noticias-test', 'Api\Noticias\NoticiasController@all');
    });

    //TESTING.
    Route::get('testing', 'Api\Calificaciones\CalificacionesController@all');
    Route::post('testing/add', 'Api\Calificaciones\CalificacionesController@add');

});