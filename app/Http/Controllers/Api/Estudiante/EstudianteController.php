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
use App\Materia;

class EstudianteController extends Controller
{
    public function all(Request $request)
    {
        $Estudiantes = Estudiante::all();
        return $Estudiantes;
    }
    public function add(Request $request)
    {
        $input = $request->only('idUser','idPersonal','nombre','grado');
        $validator = Validator::make($input, [
            'idUser' => 'required|numeric|exists:users,id',
            'idPersonal' => 'required|numeric|unique:estudiantes,idPersonal',
            'nombre' => 'required|string',
            'grado' => 'required|numeric',
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $estudiante = Estudiante::create($input);

//        'idProfesor',
//        'idEstudiante',
//        'idMateria',
//        'periodo',
//        'evaluaciones'

        $materias = Materia::where('grado',$input['grado'])->get();
        foreach ($materias as $materia) {
            $profesor = $materia->profesores()->get()->first();
            if(!is_null($profesor))
            {
                Calificacion::create([
                    'idProfesor'=>$profesor->id,
                    'idEstudiante'=>$estudiante->id,
                    'idMateria'=>$materia->id,
                    'periodo'=>'2017-2018',
                    'evaluaciones'=>[],
                    'acumulado'=>0,
                ]);
            }
        }
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        return response()->json(['success'=>true]);
    }
    public function materias(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:estudiantes,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $estudiante = Estudiante::find($id);
        return Materia::where('grado', $estudiante->grado)->where('seccion', $estudiante->seccion)->get();
    }
}
