<?php

namespace App\Http\Controllers\Api\Articulo;

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

class ArticuloController extends Controller
{
//$table->string('nombre');
//$table->string('cantidad');
//$table->string('estado');
//$table->float('precio', 8, 2);

    public function all(Request $request)
    {
        return Articulo::where('estado','HABILITADO')->where('categoria','<>','MATRICULA')->get();
    }
    public function add(Request $request)
    {
        return response()->json(["error"=>"Los articulos se crean desde el admin."],400);
        $input = $request->only('nombre','cantidad','estado','precio','image','categoria','descripcion');
        $validator = Validator::make($input, [
            'nombre' => 'required|string|unique:articulos,nombre',
            'cantidad' => 'required_unless:categoria,MATRICULA|integer|between:1,999999',
            'estado' => 'required|string|in:HABILITADO,DESHABILITADO',
            'precio' => 'required|integer|between:1,1000000',
            'categoria' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if($input['categoria'] != 'MATRICULA')
        {
            $validator = Validator::make($input, [
                'image' => 'required|image',
            ]);
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return response()->json($validator->errors(),400);
            }

            if (!$request->file('image')->isValid()) {
                return response()->json(["error"=>"error with image file."],400);
            }
            $input['image'] = Storage::put('images', $input['image']);
            $input['image'] = asset($input['image']);
        }
//        $input['image'] = Storage::put('images', $request->image);
//        $input['image'] = str_replace('public','storage',$input['image'],$i);
//        $input['image'] = url('/')."/".$input['image'];


        if(!isset($input['cantidad']))
            $input['cantidad'] = 0;

//        $nuevo = Articulo::create($input);

        return response()->json(['success'=>true,'creado'=>$nuevo]);
    }
    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:articulos,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return Articulo::find($id);
    }
}
