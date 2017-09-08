<?php

namespace App\Http\Controllers\Api\Horario;

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

class HorariosController extends Controller
{
    public function all(Request $request)
    {
        $Horarios = Horario::all();
        return $Horarios;
    }

    public function horario(Request $request)
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
//        ADMIN,PROFESOR,REPRESENTANTE
        $horario = [];
        switch ($user->type) {
            case 'PROFESOR':
            {
                $userMaterias = $user->materias()->pluck('id');
                $horario = Horario::where('idEntidad',$user->id)->get();

                foreach ($userMaterias as $idMateria) {
                    $horario = $horario->union(Horario::where([
                        ['idEntidad', '=', $idMateria]
                    ])->get());
                }
                return $horario;
            }
                break;
            case 'REPRESENTANTE':
            {
                return $horario;
            }
                break;
            case 'ADMIN':
            {
                return response()->json(['error'=>'user is ADMIN, no maneja horario'],400);
            }
                break;
        }
    }

    public function add(Request $request)
    {
        $input = $request->only(
            'entidad',
            'idEntidad',
            'descripcion',
            'dia',
            'inicio',
            'fin',
            'grado',
            'lugar'
        );

        $validator = Validator::make($input, [
            'entidad' => 'required|string|in:MATERIA,PROFESOR,GENERAL',
            'idEntidad' => 'required_if:entidad,MATERIA,PROFESOR',
            'descripcion' => 'required|string',
            'dia' => 'required|string',
            'inicio' => 'required|string',
            'fin' => 'required|string',
            'grado' => 'required|integer',
            'lugar' => 'required|string'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if($input['entidad'] === 'MATERIA' || $input['entidad'] === 'PROFESOR')
        {
            switch ($input['entidad']) {
                case 'MATERIA':
                    $validator = Validator::make($input, [
                        'idEntidad' => 'required|integer|exists:materias,id'
                    ]);
                    break;
                case 'PROFESOR':
                    $validator = Validator::make($input, [
                        'idEntidad' => 'required|integer|exists:users,id'
                    ]);
                    break;
            }
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return response()->json($validator->errors(),400);
            }
        }

        if($input['entidad'] === 'GENERAL')
            unset($input['idEntidad']);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Horario::create($input);
        return response()->json(['success'=>true]);
    }
}
