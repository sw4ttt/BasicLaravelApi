<?php

namespace App\Http\Controllers\Web\Configuracion;

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

class WebResourcesController extends Controller
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
            'principal' => 'required|image|mimes:png|dimensions:min_width=400,min_height=700,max_width=500,max_height=900|max:1000',
            'noticias' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20',
            'calificaciones' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20',
            'materiales' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20',
            'horario' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20',
            'pagos' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20',
            'tienda' => 'required|image|mimes:png|dimensions:width=160,min_height=160|max:20'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        if (!$request->file('principal')->isValid()) {
            return back()->withErrors(['principal'=>['error with principal file.']])->withInput();
        }
        if (!$request->file('noticias')->isValid()) {
            return back()->withErrors(['noticias'=>['error with noticias file.']])->withInput();
        }
        if (!$request->file('calificaciones')->isValid()) {
            return back()->withErrors(['calificaciones'=>['error with calificaciones file.']])->withInput();
        }
        if (!$request->file('materiales')->isValid()) {
            return back()->withErrors(['materiales'=>['error with materiales file.']])->withInput();
        }
        if (!$request->file('horario')->isValid()) {
            return back()->withErrors(['horario'=>['error with horario file.']])->withInput();
        }
        if (!$request->file('pagos')->isValid()) {
            return back()->withErrors(['pagos'=>['error with pagos file.']])->withInput();
        }
        if (!$request->file('tienda')->isValid()) {
            return back()->withErrors(['tienda'=>['error with tienda file.']])->withInput();
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


        return back()->with('message', 'Imagenes Cargadas!');
    }
}
