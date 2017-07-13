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

    	Route::post('noticias', 'Api\Noticias\NoticiasController@all');
        Route::post('noticias/add', 'Api\Noticias\NoticiasController@add');
        Route::get('noticias/{id}', 'Api\Noticias\NoticiasController@find');
        Route::delete('noticias/{id}', 'Api\Noticias\NoticiasController@delete');
    });
});