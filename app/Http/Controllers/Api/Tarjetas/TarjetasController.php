<?php

namespace App\Http\Controllers\Api\Tarjetas;

use Illuminate\Http\Request;
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
use App\Tarjeta;

class TarjetasController extends Controller
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
        $tarjetas = $user->tarjetas()->get();
        return $tarjetas;
    }

    public function add(Request $request)
    {
        $input = $request->only(
            'tipo',
            'numero',
            'token',
            'customerId'
        );

        $input['tipo'] = strtoupper($input['tipo']);

        $validator = Validator::make($input, [
            'tipo' => 'required|string',
            'numero' => 'required|digits:4',
            'token' => 'required|string',
            'customerId' => 'required|string'

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

        $input['idUsuario'] = $user->id;

        $tarjeta = Tarjeta::create($input);

        return response()->json(['success'=>true,'tarjeta'=>$tarjeta]);
    }


    public function eliminar(Request $request)
    {
        $input = $request->only(
            'idTarjeta'
        );

        $validator = Validator::make($input, [
            'idTarjeta' => 'required|numeric|exists:tarjetas,id',
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

        $tarjeta = Tarjeta::find($input['idTarjeta']);

        if($tarjeta->idUsuario !== $user->id)
            return response()->json(['error'=>'La tarjeta no corresponde al usuario actual.'],400);

        $tarjeta->delete();

        return response()->json(['success'=>true]);
    }
}
