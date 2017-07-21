<?php

namespace App\Http\Controllers\Api\Calificaciones;

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
class CalificacionesController extends Controller
{
    public function all(Request $request)
    {
        $Calificaciones = Calificacion::all();
        return $Calificaciones;
    }
    public function add(Request $request)
    {
        $input = $request->only('idProfesor','idEstudiante','idMateria','periodo','evaluaciones');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idEstudiante' => 'required|numeric|exists:estudiantes,id',
            'idMateria' => 'required|numeric|exists:materias,id',
            'periodo' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'evaluaciones' => 'required|array'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $calificacion = Calificacion::where('idProfesor',$input['idProfesor'])->where('idMateria',$input['idMateria'])->first();

        if($calificacion)
            return response()->json(['KEY'=>'ALREADY_EXIST','MESSAGE'=>'Ya existe un objeto Calificacion para esa Materia,Profesor y Periodo.'],400);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Calificacion::create($input);
        return response()->json(['success'=>true]);
    }
    public function edit(Request $request)
    {
        $input = $request->only('idProfesor','idEstudiante','idMateria','periodo','evaluaciones');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idEstudiante' => 'required|numeric|exists:estudiantes,id',
            'idMateria' => 'required|numeric|exists:materias,id',
            'periodo' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'evaluaciones' => 'required|array'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $calificacion = Calificacion::where('idProfesor',$input['idProfesor'])->where('idMateria',$input['idMateria'])->first();
        if(!$calificacion)
            return response()->json(['KEY'=>'NOTFOUND','MESSAGE'=>'No existe un objeto Calificacion para esa Materia,Profesor y Periodo.'],400);

        $calificacion->evaluaciones = $input['evaluaciones'];
        $calificacion->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $calificacion->save();
        return response()->json(['success'=>true]);
    }
    public function byEstudiante(Request $request,$idEstudiante)
    {
        $request['id'] = $idEstudiante;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:estudiantes,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        return Calificacion::where('idEstudiante',$idEstudiante)
            ->get()
            ->groupBy('idMateria');
    }
    public function byEstudianteByMateria(Request $request,$idEstudiante,$idMateria)
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
        return Calificacion::where('idEstudiante',$idEstudiante)->where('idMateria',$idMateria)->first();
    }
    public function byProfesor(Request $request,$idProfesor)
    {
        $request['id'] = $idProfesor;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return Calificacion::where('idProfesor',$idProfesor)->get()
            ->groupBy('idEstudiante');
    }
    public function byProfesorByMateria(Request $request,$idProfesor,$idMateria)
    {
        $request['idProfesor'] = $idProfesor;
        $request['idMateria'] = $idMateria;
        $input = $request->only('idProfesor','idMateria');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idMateria' => 'required|numeric|exists:materias,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return Calificacion::where('idProfesor',$idProfesor)->where('idMateria',$idMateria)->first();
    }
}
