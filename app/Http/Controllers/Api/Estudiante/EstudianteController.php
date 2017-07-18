<?php

namespace App\Http\Controllers\Api\Estudiante;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;

class EstudianteController extends Controller
{
    public function all(Request $request)
    {
        $Estudiantes = Estudiante::all();
        return $Estudiantes;
    }
    public function add(Request $request)
    {
        $input = $request->only('idUser','idPersonal','nombre');
        $validator = Validator::make($input, [
            'idUser' => 'required|numeric|exists:users,id',
            'idPersonal' => 'required|numeric|unique:estudiantes,idPersonal',
            'nombre' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Estudiante::create($input);
        return response()->json(['success'=>true]);
    }
    public function materias(Request $request, $idEstudiante)
    {
        $request['idEstudiante'] = $idEstudiante;
        $input = $request->only('idEstudiante');
        $validator = Validator::make($input, [
            'idEstudiante' => 'required|numeric|exists:estudiantes,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $estudiante = Estudiante::find($idEstudiante);
        return $estudiante->materias()->get();
    }
    public function addMateria(Request $request, $idEstudiante,$idMateria)
    {
        $request['idEstudiante'] = $idEstudiante;
        $request['idMateria'] = $idMateria;
        $input = $request->only('idEstudiante','idMateria');
        $validator = Validator::make($input, [
            'idEstudiante' => 'required|numeric|exists:estudiantes,id',
            'idMateria' => 'required|numeric|exists:materias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $estudiante = Estudiante::find($idEstudiante);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $estudiante->materias()
            ->attach($input['idMateria'],
                [
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);
        return response()->json(['success'=>true]);
    }
}
