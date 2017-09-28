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
            $enviados = Mensaje::where('idEmisor', $user->id)->get();
            $recibidos = Mensaje::where('idReceptor', $user->id)->get();

            return response()->json(['enviados'=>$enviados,'recibidos'=>$recibidos]);
        }
        else{
            $estudiante = $user->estudiantes()->first();
            $user->grado = $estudiante->grado;

            $enviados = Mensaje::where('idEmisor', $user->id)->get();
            $recibidos = Mensaje::where('idReceptor', $user->id)
                ->orWhere('grado', $user->grado)
                ->get();

            return response()->json(['enviados'=>$enviados,'recibidos'=>$recibidos]);
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
                
                $grados = array_unique($grados);

                $data = Estudiante::whereIn('grado', $grados)
                    ->join('users', 'users.id','=', 'estudiantes.idUser')
                    ->select('users.id as idEntidad','estudiantes.grado','users.nombre as nombre')
                    ->get();
                
                $data = $data->unique('idEntidad')->values();

                return response()->json(['data'=>$data,'materias'=>$materias]);
            }
            else
                return response()->json(['error'=>'Profesor no tiene materias asignadas'],400);
        }
        if(($user->type === 'REPRESENTANTE'))
        {
            $estudiantes = $user->estudiantes()->get();

            $grados = array_pluck($estudiantes, 'grado');
            
            $grados = array_unique($grados);

            $data = Materia::whereIn('grado', $grados)
                ->join('profesores_materias', 'profesores_materias.idMateria','=', 'materias.id')
                ->join('users', 'users.id','=', 'profesores_materias.idProfesor')
                ->select('profesores_materias.idProfesor as idEntidad','profesores_materias.idMateria','users.nombre')
                ->get();
                
            $data = $data->unique('idEntidad')->values();

            return response()->json(['data'=>$data]);

        }
        return response()->json(['error'=>'EL usuario debe ser PROFESOR o REPRESENTANTE.'],400);
    }

    public function enviar(Request $request)
    {
        $input = $request->only(
            'idUsuario',
            'idMateria',
            'asunto',
            'mensaje'
        );

        $validator = Validator::make($input, [
            'idUsuario' => 'required_without:idMateria|numeric|exists:users,id',
            'idMateria' => 'required_without:idUsuario|numeric|exists:materias,id',
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
        
        //return response()->json(['success'=>true]);

        //if(($user->type === 'REPRESENTANTE'))
            //return response()->json(['error'=>'Usuario no puede mandar mensajes porque no es PROFESOR o ADMIN'],400);

        //$materia = $user->materias()->where('id', $input['idMateria'])->first();

        //if($materia === null)
        //    return response()->json(['error'=>'El idMateria especificado no corresponde a las materias del usuario.'],400);
        
        $key = "";
        $value = "";

//        echo "idMateria=".$input['idMateria']."\n";
//        echo "idUsuario=".$input['idUsuario']."\n";
        
        if($input['idMateria'] != ""){
            $materia = Materia::find($input['idMateria']);
            
            $key = "grado";
            $value = $materia->grado;

            $input['materia'] = $materia->nombre;
            $input['grado'] = $materia->grado;
            
            $input['idEmisor'] = $user->id;
            $input['idReceptor'] = 0;
            $input['nombre'] = $user->nombre;

            unset($input['idUsuario']);
            
        }
        else{
            $input['materia'] = "";
            $input['idMateria'] = 0;
            $input['grado'] = 0;
            
            $key = "userId";
            $value = $input['idUsuario'];

            $input['idEmisor'] = $user->id;
            $input['idReceptor'] = $input['idUsuario'];
            $input['nombre'] = $user->nombre;

            unset($input['idUsuario']);
            
        }
        
        
        $tag = new \stdClass;
        $tag->key = $key;
        $tag->relation = "=";
        $tag->value = $value;

        $tags = array();

        array_push($tags,$tag);

//        echo $input['idEmisor'];

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
                "idEmisor"=>$input['idEmisor'],
                "idReceptor"=>$input['idReceptor'],
                "nombre"=>$input['nombre'],
                "asunto"=>$input['asunto'],
                "mensaje"=>$input['mensaje'],
                "created_at"=>$mensaje->created_at->format('Y-m-d H:i:s')
            ],
            $buttons = null,
            $schedule = null
        );

        return response()->json(['success'=>true,'mensaje'=>$mensaje]);
    }
}
