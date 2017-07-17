<?php

namespace App\Http\Controllers\Api\Materia;

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
class MateriaController extends Controller
{
    public function all(Request $request)
    {
        $Materias = Materia::all();
        return $Materias;
    }
    public function add(Request $request)
    {
        $input = $request->only('nombre','grado');
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric',
            'nombre' => 'required|string|unique:materias,nombre',
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Materia::create($input);
        return response()->json(['success'=>true]);
    }
}
