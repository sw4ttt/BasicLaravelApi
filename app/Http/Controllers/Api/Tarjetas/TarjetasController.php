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
        foreach ($tarjetas as $tarjeta) {
            $tarjeta->numero = substr($tarjeta->numero,12);
            unset($tarjeta->nombre);
            unset($tarjeta->cod);
            unset($tarjeta->vencimiento);
            unset($tarjeta->street1);
            unset($tarjeta->street2);
            unset($tarjeta->city);
            unset($tarjeta->state);
            unset($tarjeta->country);
            unset($tarjeta->postalCode);
            unset($tarjeta->phone);

        }
        return $tarjetas;
    }

    public function add(Request $request)
    {
        $input = $request->only(
            'tipo',
            'numero',
            'nombre',
            'cod',
            'vencimiento',
            'street1',
            'street2',
            'city',
            'state',
            'country',
            'postalCode',
            'phone'
        );

        $input['tipo'] = strtoupper($input['tipo']);

        $validator = Validator::make($input, [
            'vencimiento' => 'required|string|regex:(^([0-9]{4}\/[0-9]{2})$)',
            'tipo' => 'required|string|in:VISA,MASTERCARD',
            'numero' => 'required|digits:16',
            'nombre' => 'required|string',
            'cod' => 'required|string',
            'street1' => 'required|string',
            'street2' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postalCode' => 'required|string',
            'phone' => 'required|string'
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

        Tarjeta::create($input);

        return response()->json(['success'=>true]);
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only(
            'idUsuario',
            'tipo',
            'numero',
            'nombre',
            'cod',
            'vencimiento',
            'street1',
            'street2',
            'city',
            'state',
            'country',
            'postalCode',
            'phone'
        );
        $validator = Validator::make($input, [
            'idUsuario'=>'required|numeric|exists:users,id',
            'tipo' => 'required|string',
            'numero' => 'required|string',
            'nombre' => 'required|string',
            'cod' => 'required|string',
            'vencimiento' => 'required|string',
            'street1' => 'required|string',
            'street2' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postalCode' => 'required|string',
            'phone' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        Tarjeta::create($input);

        return response()->json(['success'=>true]);
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
