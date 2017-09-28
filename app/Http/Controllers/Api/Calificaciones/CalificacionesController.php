<?php

namespace App\Http\Controllers\Api\Calificaciones;

use App\Materia;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        $input = $request->only('idProfesor','idEstudiante','idMateria','periodo','evaluaciones','acumulado');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idEstudiante' => 'required|numeric|exists:estudiantes,id',
            'idMateria' => 'required|numeric|exists:materias,id',
            'periodo' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'evaluaciones' => 'required|array',
            'acumulado' => 'required|numeric'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $calificacion = Calificacion::where('idProfesor',$input['idProfesor'])
            ->where('idMateria',$input['idMateria'])
            ->where('idEstudiante',$input['idEstudiante'])
            ->first();

        if($calificacion)
            return response()->json(['KEY'=>'ALREADY_EXIST','MESSAGE'=>'Ya existe un objeto Calificacion para esa Materia,Profesor y Periodo.'],400);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Calificacion::create($input);
        return response()->json(['success'=>true]);
    }
    public function edit(Request $request)
    {
        $input = $request->only('idProfesor','idEstudiante','idMateria','periodo','evaluaciones','acumulado');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idEstudiante' => 'required|numeric|exists:estudiantes,id',
            'idMateria' => 'required|numeric|exists:materias,id',
            'periodo' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'evaluaciones' => 'required|array',
            'acumulado' => 'required|numeric'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $calificacion = Calificacion::where('idProfesor',$input['idProfesor'])->where('idMateria',$input['idMateria'])->first();

        if(!$calificacion)
            return response()->json(['KEY'=>'NOTFOUND','MESSAGE'=>'No existe un objeto Calificacion para esa Materia,Profesor y Periodo.'],400);

        $calificacion->evaluaciones = $input['evaluaciones'];
        $calificacion->acumulado = $input['acumulado'];
        $calificacion->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $calificacion->save();
        return response()->json(['success'=>true]);
    }
    public function byEstudiante(Request $request,$idEstudiante)
    {
        $request['id'] = $idEstudiante;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        
        $estudiante = User::find($input['id'])->estudiantes->first();

        return Calificacion::where('idEstudiante',$estudiante->id)
            ->join('estudiantes', 'calificaciones.idEstudiante','=', 'estudiantes.id')
            ->join('materias', 'calificaciones.idMateria','=', 'materias.id')
            ->select('calificaciones.*', 'estudiantes.nombre as nombreEstudiante','materias.nombre as nombreMateria')
            ->get();
            
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
        return Calificacion::where('idEstudiante',$idEstudiante)->where('idMateria',$idMateria)
            ->join('estudiantes', 'calificaciones.idEstudiante','=', 'estudiantes.id')
            ->join('materias', 'calificaciones.idMateria','=', 'materias.id')
            ->select('calificaciones.*', 'estudiantes.nombre as nombreEstudiante','materias.nombre as nombreMateria')
            ->get();
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

        $calificaciones =
            Calificacion::where('idProfesor',$idProfesor)
                ->join('estudiantes', 'calificaciones.idEstudiante','=', 'estudiantes.id')
                ->join('materias', 'calificaciones.idMateria','=', 'materias.id')
                ->select('calificaciones.*', 'estudiantes.nombre as nombreEstudiante','materias.nombre as nombreMateria')
                ->get();

        return $calificaciones;
    }
    public function editByProfesor(Request $request,$idProfesor)
    {
        $list = json_decode($request->getContent());
        if(is_null($list) || count($list)==0)
            return response()->json(["error"=>"Request Body invalid or empty."],400);
        foreach ($list as $calificacion) {
            Calificacion::find($calificacion->id)
                ->update([
                    'idProfesor' => $calificacion->idProfesor,
                    'idEstudiante' => $calificacion->idEstudiante,
                    'idMateria' => $calificacion->idMateria,
                    'periodo' => $calificacion->periodo,
                    'evaluaciones' => $calificacion->evaluaciones,
                    'acumulado' => $calificacion->acumulado
                ]);
        }
        return response()->json(['success'=>true],201);
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
        return Calificacion::where('idProfesor',$idProfesor)->where('idMateria',$idMateria)
            ->join('estudiantes', 'calificaciones.idEstudiante','=', 'estudiantes.id')
            ->join('materias', 'calificaciones.idMateria','=', 'materias.id')
            ->select('calificaciones.*', 'estudiantes.nombre as nombreEstudiante','materias.nombre as nombreMateria')
            ->get();
    }

    public function addEvaluacion(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired'],401);
            }else{
                return response()->json(['error'=>'Token Missing'],400);
            }
        }

        if($user->type != 'PROFESOR')
            return response()->json(['error'=>'Usuario no es profesor.'],400);


        $input = $request->only('idMateria','nombre','mensaje');
        $validator = Validator::make($input, [
            'idMateria' => 'required|numeric|exists:materias,id',
            'nombre' => 'required|string',
            'mensaje'=> 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $materia = $user->materias()->where('id',$input['idMateria'])->first();

        if(is_null($materia))
            return response()->json(['error'=>'La materia seleccionada no corresponde al profesor indicado o no existe.'],400);

        $estudiantes = Estudiante::where('grado',$materia->grado)->get();

        foreach ($estudiantes as $estudiante) {
            $calififacion = Calificacion::
                where([
                    ['idProfesor', '=', $user->id],
                    ['idEstudiante', '=', $estudiante->id],
                    ['idMateria', '=', $input['idMateria']]
                ])
                ->first();
            if(!is_null($calififacion))
            {
                $evaluacion = new \stdClass;
                $evaluacion->nombre = $input['nombre'];
                $evaluacion->nota = 0;
                $evaluacion->mensaje = $input['mensaje'];

                $tempEvaluaciones = $calififacion->evaluaciones;
                if(is_array($tempEvaluaciones))
                {
                    array_push($tempEvaluaciones,$evaluacion);
                    $calififacion->evaluaciones = $tempEvaluaciones;
                    $calififacion->save();
                }
            }
        }



        return response()->json(['success'=>true]);
    }

    public function fixCalificaciones(Request $request)
    {
        DB::table('calificaciones')->delete();

        $profesores = User::where('type','PROFESOR')->get();

//        echo "profesores->count()=".$profesores->count();
        if($profesores->count()>0)
        {

            foreach ($profesores as $profesor){
//                echo "\n profesor=".$profesor->nombre;
                foreach ($profesor->materias as $materia) {
                    $estudiantes = Estudiante::where('grado',$materia->grado)->get();
//                    echo "\n  estudiantes->count()=".$estudiantes->count();
                    if($estudiantes->count()>0){
                        foreach ($estudiantes as $estudiante){

                            $calificacion = new Calificacion;
                            $calificacion->idProfesor = $profesor->id;
                            $calificacion->idEstudiante = $estudiante->id;
                            $calificacion->idMateria = $materia->id;
                            $calificacion->periodo = "2017-2018";
                            $calificacion->evaluaciones = [];
                            $calificacion->acumulado = 0;
                            $calificacion->save();

                        }
                    }
                }

            }
        }
        return response()->json(['success'=>true]);

    }
}