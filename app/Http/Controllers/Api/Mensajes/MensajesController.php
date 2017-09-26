<?php

namespace App\Http\Controllers\Api\Mensajes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hash;
use JWTAuth;
use phpDocumentor\Reflection\Types\This;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Materia;
use App\Material;
use OneSignal;
use App\Mensaje;
use Illuminate\Support\Facades\DB;

class MensajesController extends Controller
{
    public function all(Request $request)
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

        if(($user->type === 'PROFESOR' || $user->type === 'ADMIN'))
        {
            return Mensaje::where('idUsuario', $user->id)->get();
        }
        else{
            $estudiante = $user->estudiantes()->first();
            $user->grado = $estudiante->grado;
            return Mensaje::where('grado', $user->grado)->get();
        }
    }

    public function destinatarios(Request $request)
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

        if(($user->type === 'PROFESOR'))
        {
            $materias = $user->materias()->get();
            if(count($materias)>0)
            {
                $grados = array_pluck($materias, 'grado');

                $data = Estudiante::whereIn('grado', $grados)
                    ->join('users', 'users.id','=', 'estudiantes.idUser')
                    ->select('users.id as idUser','estudiantes.grado','estudiantes.id as idEstudiante')
                    ->get();

                return response()->json(['data'=>$data,'grados'=>$grados,'materias'=>$materias]);
            }
            else
                return response()->json(['error'=>'Profesor no tiene materias asignadas'],400);
        }
        if(($user->type === 'REPRESENTANTE'))
        {
            $estudiantes = $user->estudiantes()->get();

            $grados = array_pluck($estudiantes, 'grado');

            $data = Materia::whereIn('grado', $grados)
                ->join('profesores_materias', 'profesores_materias.idMateria','=', 'materias.id')
                ->select('profesores_materias.idProfesor','profesores_materias.idMateria')
                ->get();

            return response()->json(['data'=>$data,'grados'=>$grados,'estudiantes'=>$estudiantes]);

        }
        return response()->json(['error'=>'EL usuario debe ser PROFESOR o REPRESENTANTE.'],400);
    }

    public function enviar(Request $request)
    {
        $input = $request->only(
            'idMateria',
            'asunto',
            'mensaje'
        );

        $validator = Validator::make($input, [
            'idMateria' => 'required|numeric|exists:materias,id',
            'asunto' => 'required|string',
            'mensaje' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

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

        if(($user->type === 'REPRESENTANTE'))
            return response()->json(['error'=>'Usuario no puede mandar mensajes porque no es PROFESOR o ADMIN'],400);

        $input['idUsuario'] = $user->id;
        $input['nombre'] = $user->nombre;

        $materia = $user->materias()->where('id', $input['idMateria'])->first();

        if($materia === null)
            return response()->json(['error'=>'El idMateria especificado no corresponde a las materias del usuario.'],400);

        $input['materia'] = $materia->nombre;
        $input['grado'] = $materia->grado;

        $tag = new \stdClass;
        $tag->key = "grado";
        $tag->relation = "=";
        $tag->value = $materia->grado;

        $tags = array();

        array_push($tags,$tag);

        $mensaje = Mensaje::create($input);

        OneSignal::sendNotificationUsingTags(
            $input['asunto'],
            $tags,
            $url = null,
            [
                "key"=>"MENSAJE",
                "id"=>$mensaje->id,
                "idMateria"=>$input['idMateria'],
                "materia"=>$input['materia'],
                "idUsuario"=>$input['idUsuario'],
                "nombre"=>$input['nombre'],
                "asunto"=>$input['asunto'],
                "mensaje"=>$input['mensaje'],
                "created_at"=>$mensaje->created_at
            ],
            $buttons = null,
            $schedule = null
        );

        return response()->json(['success'=>true,'mensaje'=>$mensaje]);
    }
}
