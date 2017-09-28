<?php

namespace App\Http\Controllers\Api\Configuracion;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Materia;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Horario;
use App\Articulo;

class ResourcesController extends Controller
{
    public function getImagenesApp(Request $request)
    {
        $imagenesApp = new \stdClass;

        if(!Storage::exists('images-app/principal.png'))
            $imagenesApp->principal = asset('images-app-default/PRINCIPAL.png');
        else
            $imagenesApp->principal = asset('images-app/principal.png');

        if(!Storage::exists('images-app/noticias.png'))
            $imagenesApp->noticias = asset('images-app-default/NOTICIAS.png');
        else
            $imagenesApp->noticias = asset('images-app/noticias.png');

        if(!Storage::exists('images-app/calificaciones.png'))
            $imagenesApp->calificaciones = asset('images-app-default/CALIFICACIONES.png');
        else
            $imagenesApp->calificaciones = asset('images-app/calificaciones.png');

        if(!Storage::exists('images-app/materiales.png'))
            $imagenesApp->materiales = asset('images-app-default/MATERIALES.png');
        else
            $imagenesApp->materiales = asset('images-app/materiales.png');

        if(!Storage::exists('images-app/horario.png'))
            $imagenesApp->horario = asset('images-app-default/HORARIO.png');
        else
            $imagenesApp->horario = asset('images-app/horario.png');

        if(!Storage::exists('images-app/pagos.png'))
            $imagenesApp->pagos = asset('images-app-default/PAGOS.png');
        else
            $imagenesApp->pagos = asset('images-app/pagos.png');

        if(!Storage::exists('images-app/tienda.png'))
            $imagenesApp->tienda = asset('images-app-default/TIENDA.png');
        else
            $imagenesApp->tienda = asset('images-app/tienda.png');

        return response()->json($imagenesApp);
    }

    public function addImagenesApp(Request $request)
    {
        //principal,  noticias, calificaciones, materiales, horario, pagos, tienda
        $input = $request->only(
            'principal',
            'noticias',
            'calificaciones',
            'materiales',
            'horario',
            'pagos',
            'tienda'
        );
        $validator = Validator::make($input, [
            'principal' => 'required|image|mimes:png|dimensions:min_width=400,min_height=400',
            'noticias' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100',
            'calificaciones' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100',
            'materiales' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100',
            'horario' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100',
            'pagos' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100',
            'tienda' => 'required|image|mimes:png|dimensions:min_width=100,min_height=100'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if (!$request->file('principal')->isValid()) {
            return response()->json(["error"=>"error with principal file."],400);
        }
        if (!$request->file('noticias')->isValid()) {
            return response()->json(["error"=>"error with noticias file."],400);
        }
        if (!$request->file('calificaciones')->isValid()) {
            return response()->json(["error"=>"error with calificaciones file."],400);
        }
        if (!$request->file('materiales')->isValid()) {
            return response()->json(["error"=>"error with materiales file."],400);
        }
        if (!$request->file('horario')->isValid()) {
            return response()->json(["error"=>"error with horario file."],400);
        }
        if (!$request->file('pagos')->isValid()) {
            return response()->json(["error"=>"error with pagos file."],400);
        }
        if (!$request->file('tienda')->isValid()) {
            return response()->json(["error"=>"error with tienda file."],400);
        }

        $imagenesApp = new \stdClass;

        $input['principal'] = Storage::putFileAs('images-app', $input['principal'], 'principal.png');
        $imagenesApp->principal = asset($input['principal']);

        $input['noticias'] = Storage::putFileAs('images-app', $input['noticias'], 'noticias.png');
        $imagenesApp->noticias = asset($input['noticias']);

        $input['calificaciones'] = Storage::putFileAs('images-app', $input['calificaciones'], 'calificaciones.png');
        $imagenesApp->calificaciones = asset($input['calificaciones']);

        $input['materiales'] = Storage::putFileAs('images-app', $input['materiales'], 'materiales.png');
        $imagenesApp->materiales = asset($input['materiales']);

        $input['horario'] = Storage::putFileAs('images-app', $input['horario'], 'horario.png');
        $imagenesApp->horario = asset($input['horario']);

        $input['pagos'] = Storage::putFileAs('images-app', $input['pagos'], 'pagos.png');
        $imagenesApp->pagos = asset($input['pagos']);

        $input['tienda'] = Storage::putFileAs('images-app', $input['tienda'], 'tienda.png');
        $imagenesApp->tienda = asset($input['tienda']);


        return response()->json(['success'=>true,'imagenesApp'=>$imagenesApp]);
    }
}
