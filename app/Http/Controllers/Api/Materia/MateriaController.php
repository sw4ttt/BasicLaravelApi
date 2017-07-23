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
use App\Material;
class MateriaController extends Controller
{
    public function all(Request $request)
    {
        $Materias = Materia::all();
        return $Materias;
    }
    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $materia = Materia::find($id);
        return $materia;
    }

    public function byGrado(Request $request, $grado)
    {
        $request['grado'] = $grado;
        $input = $request->only('grado');
        $validator = Validator::make($input, [
            'grado' => 'required|numeric'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return Materia::where('grado',$grado)->get();
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
    public function materiales(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materias,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $materia = Materia::find($id);
        return $materia->materiales()->get();
    }
}
