<?php

namespace App\Http\Controllers\Api\Articulo;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
    public function add(Request $request)
    {
        $input = $request->only('nombre','cantidad','estado','precio');
        $validator = Validator::make($input, [
            'nombre' => 'required|string|unique:articulos,nombre',
            'cantidad' => 'required|numeric|between:1,9999',
            'estado' => 'required|string|in:HABILITADO,DESHABILITADO',
            'precio' => 'required|numeric|between:1,100000.00'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $nuevo = Articulo::create($input);

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