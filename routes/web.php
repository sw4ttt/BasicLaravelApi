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
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Curso;

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
        Route::get('/add', function () {
            $cursos = Curso::all();
            $cursos->transform(function ($item, $key) {
                switch ($item->grado) {
                    case 1:
                        $item->gradoTexto = "Primero";
                        break;
                    case 2:
                        $item->gradoTexto = "Segundo";
                        break;
                    case 3:
                        $item->gradoTexto = "Tercero";
                        break;
                    case 4:
                        $item->gradoTexto = "Cuarto";
                        break;
                    case 5:
                        $item->gradoTexto = "Quinto";
                        break;
                    case 6:
                        $item->gradoTexto = "Sexto";
                        break;
                    case 7:
                        $item->gradoTexto = "Septimo";
                        break;
                    case 8:
                        $item->gradoTexto = "Octavo";
                        break;
                    case 9:
                        $item->gradoTexto = "Noveno";
                        break;
                    case 10:
                        $item->gradoTexto = "Decimo";
                        break;
                    case 11:
                        $item->gradoTexto = "Pre-Jardin";
                        break;
                    case 12:
                        $item->gradoTexto = "Jardin";
                        break;
                    case 13:
                        $item->gradoTexto = "Transicion";
                        break;
                    case 14:
                        $item->gradoTexto = "Parvulo";
                        break;
                    default:
                        $item->gradoTexto = "Otro";
                }
                return $item;
            });

            return view('/users/add',["cursos"=>$cursos]);
        });
        Route::get('/add/masivo', function () {  return view('/users/addbulk');});
        Route::post('/add/masivo', 'Web\UsersController@addBulk');
        Route::post('/add', 'Web\UsersController@add');
        Route::get('/edit/{id}', function ($id) {
            $usuario = User::find($id);
            if(!is_null($usuario)){
                return view('/users/edit',['usuario'=>$usuario]);
            }
            else
                return redirect("users");
        });
        Route::post('/edit/{id}', 'Web\UsersController@edit');
        Route::post('/delete/{id}', 'Web\UsersController@delete');
    });

    Route::group(['prefix' => 'materias'], function () {
        Route::get('/', 'Web\MateriasController@all');
        Route::get('/add', function () {

            $cursos = Curso::all();
            $cursos->transform(function ($item, $key) {
                switch ($item->grado) {
                    case 1:
                        $item->gradoTexto = "Primero";
                        break;
                    case 2:
                        $item->gradoTexto = "Segundo";
                        break;
                    case 3:
                        $item->gradoTexto = "Tercero";
                        break;
                    case 4:
                        $item->gradoTexto = "Cuarto";
                        break;
                    case 5:
                        $item->gradoTexto = "Quinto";
                        break;
                    case 6:
                        $item->gradoTexto = "Sexto";
                        break;
                    case 7:
                        $item->gradoTexto = "Septimo";
                        break;
                    case 8:
                        $item->gradoTexto = "Octavo";
                        break;
                    case 9:
                        $item->gradoTexto = "Noveno";
                        break;
                    case 10:
                        $item->gradoTexto = "Decimo";
                        break;
                    case 11:
                        $item->gradoTexto = "Pre-Jardin";
                        break;
                    case 12:
                        $item->gradoTexto = "Jardin";
                        break;
                    case 13:
                        $item->gradoTexto = "Transicion";
                        break;
                    case 14:
                        $item->gradoTexto = "Parvulo";
                        break;
                    default:
                        $item->gradoTexto = "Otro";
                }
                return $item;
            });

            return view('/materias/add',['profesores'=>User::where('type','PROFESOR')->get(),'cursos'=>$cursos]);
        });
        Route::post('/add', 'Web\MateriasController@add');
        Route::get('/edit/{id}', function ($id) {
            $materia = Materia::find($id);

            if(is_null($materia))
                return back()->withErrors(['invalid'=>['El id de materia seleccionado no es valido.']]);

            $profesor = $materia->profesores()->first();

            $materia->profesor = $profesor->nombre;

            $cursos = Curso::all();
            $cursos->transform(function ($item, $key) {
                switch ($item->grado) {
                    case 1:
                        $item->gradoTexto = "Primero";
                        break;
                    case 2:
                        $item->gradoTexto = "Segundo";
                        break;
                    case 3:
                        $item->gradoTexto = "Tercero";
                        break;
                    case 4:
                        $item->gradoTexto = "Cuarto";
                        break;
                    case 5:
                        $item->gradoTexto = "Quinto";
                        break;
                    case 6:
                        $item->gradoTexto = "Sexto";
                        break;
                    case 7:
                        $item->gradoTexto = "Septimo";
                        break;
                    case 8:
                        $item->gradoTexto = "Octavo";
                        break;
                    case 9:
                        $item->gradoTexto = "Noveno";
                        break;
                    case 10:
                        $item->gradoTexto = "Decimo";
                        break;
                    case 11:
                        $item->gradoTexto = "Pre-Jardin";
                        break;
                    case 12:
                        $item->gradoTexto = "Jardin";
                        break;
                    case 13:
                        $item->gradoTexto = "Transicion";
                        break;
                    case 14:
                        $item->gradoTexto = "Parvulo";
                        break;
                    default:
                        $item->gradoTexto = "Otro";
                }
                return $item;
            });

            return view('/materias/edit',[
                'materia'=>$materia,
                'profesores'=>User::where('type','PROFESOR')->get(),
                'cursos'=>$cursos
            ]);
        });
        Route::post('/edit/{id}', 'Web\MateriasController@edit');
        Route::post('/delete/{id}', 'Web\MateriasController@delete');
    });

    Route::group(['prefix' => 'cursos'], function () {
        Route::get('/', 'Web\CursosController@all');
        Route::get('/add', function () {
            return view('/cursos/add');
        });
        Route::post('/add', 'Web\CursosController@add');
        Route::get('/edit/{id}', function ($id) {
            $curso = Curso::find($id);

            if(is_null($curso))
                return back()->withErrors(['invalid'=>['El id de Curso seleccionado no es valido.']]);

            return view('/cursos/edit',[
                'curso'=>$curso
            ]);
        });
        Route::post('/edit/{id}', 'Web\CursosController@edit');
        Route::post('/delete/{id}', 'Web\CursosController@delete');
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
                :redirect("articulos");
        });
        Route::post('/edit/{id}', 'Web\ArticulosController@edit');
        Route::post('/delete/{id}', 'Web\ArticulosController@delete');
    });

    Route::group(['prefix' => 'horarios'], function () {
        Route::get('/', 'Web\HorariosController@all');
        Route::get('/add', function () {
            return view('/horarios/add',[
                'horarios'=>Horario::all(),
                'materias'=>Materia::all(),
                'profesores'=>User::where('type','PROFESOR')->get()
            ]);
        });
        Route::get('/edit/{id}', function ($id) {
            $horario = Horario::find($id);
            return !is_null($horario)
                ?view('/horarios/edit',[
                    'horario'=>$horario,
                    'materias'=>Materia::all(),
                    'profesores'=>User::where('type','PROFESOR')->get()
                ])
                :redirect("horarios");
        });
        Route::post('/edit/{id}', 'Web\HorariosController@edit');
        Route::post('/add', 'Web\HorariosController@add');
        Route::post('/delete/{id}', 'Web\HorariosController@delete');
    });

    Route::group(['prefix' => 'materiales'], function () {
        Route::get('/', 'Web\MaterialesController@all');
        Route::get('/add', function () {
            return view('/materiales/add',['materias'=>Materia::all()]);
        });
        Route::post('/add', 'Web\MaterialesController@add');
        Route::get('/edit/{id}', function ($id) {
            $material = Material::find($id);

            return !is_null($material)
                ?view('/materiales/edit',[
                    'material'=>$material,
                    'materias'=>Materia::all(),
                ])
                :redirect("materiales");
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
        Route::get('/edit/{id}', function ($id) {
            $noticia = Noticia::find($id);
            return !is_null($noticia)
                ?view('/noticias/edit',['noticia'=>$noticia])
                :redirect("noticias");
        });
        Route::post('/edit/{id}', 'Web\NoticiasController@edit');
        Route::post('/delete/{id}', 'Web\NoticiasController@delete');
    });

    Route::group(['prefix' => 'notificaciones'], function () {
//        Route::get('/', 'Web\HorariosController@all');
        Route::get('/add', function () {
            return view('/notificaciones/add');
        });
        Route::post('/add', 'Web\NotificacionesController@add');
    });

    Route::group(['prefix' => 'calificaciones'], function () {
        Route::get('/', 'Web\CalificacionesController@all');
        Route::get('/materia/{id}', 'Web\CalificacionesController@getForMateria');
        Route::post('/evaluacion/add/materia/{id}', 'Web\CalificacionesController@addEvaluacion');
//        /calificaciones/'.$calificacion->id.'/evaluacion/'.$evaluacion['nombre']
        Route::post('/{id}/evaluacion/{nombreEvaluacion}', 'Web\CalificacionesController@editarEvaluacion');
        Route::post('/{id}/acumulado', 'Web\CalificacionesController@editarAcumulado');
        Route::get('/data', function () {
            return response()->json(['success'=>true,'materias'=>Materia::all()]);
        });
//        Route::get('/add', function () {
//            return view('/notificaciones/add');
//        });
//        Route::post('/add', 'Web\NotificacionesController@add');
    });

    Route::group(['prefix' => 'config'], function () {
//        Route::get('/', 'Web\HorariosController@all');
        Route::get('/datos', function () {
            return view('/config/datos/datos');
        });
        Route::get('/images-app/add', function () {
            return view('/config/images-app/add');
        });
        Route::post('/images-app/add', 'Web\Configuracion\WebResourcesController@addImagenesApp');
    });


});
