<?php

namespace App\Http\Controllers\Api\Carrito;

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
use App\Articulo;

class CarritoController extends Controller
{
    //
    public function add(Request $request)
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

        $input = $request->only('articulos');

        $validator = Validator::make($input, [
            'articulos' => 'required|array',
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $encontrados = Articulo::find(array_pluck($input['articulos'], 'id'))->count();

        if($encontrados !== count($input['articulos']))
            return response()->json(['error'=>'articulos con ids invalidos. Ids Enviados: '.count($input['articulos']).", Ids Encontrados en BD: ".$encontrados],400);

        $arrlength = count($input['articulos']);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        for($x = 0; $x < $arrlength; $x++) {

            $user->carrito()->syncWithoutDetaching(
                [
                    ($input['articulos'][$x]['id']) => [
                        'cantidad' => $input['articulos'][$x]['cantidad'],
                        'created_at' => $input['created_at'],
                        'updated_at' => $input['updated_at']
                    ]
                ]
            );
        }
        return response()->json(['success'=>'true','carrito'=>$user->carrito()->withPivot('cantidad')->get()],201);
    }

    public function carrito(Request $request)
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
        return $user->carrito()->withPivot('cantidad')->get();
    }

    public function edit(Request $request)
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
        $input = $request->only('articulos');

        $validator = Validator::make($input, [
            'articulos' => 'required|array',
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $encontrados = Articulo::find(array_pluck($input['articulos'], 'id'))->count();

        if($encontrados !== count($input['articulos']))
            return response()->json(['error'=>'articulos con ids invalidos. Ids Enviados: '.count($input['articulos']).", Ids Encontrados en BD: ".$encontrados],400);

        $arrlength = count($input['articulos']);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        for($x = 0; $x < $arrlength; $x++) {

            $user->carrito()->syncWithoutDetaching(
                [
                    ($input['articulos'][$x]['id']) => [
                        'cantidad' => $input['articulos'][$x]['cantidad'],
                        'created_at' => $input['created_at'],
                        'updated_at' => $input['updated_at']
                    ]
                ]
            );
        }
        return response()->json(['success'=>'true','carrito'=>$user->carrito()->get()],201);
    }

    public function quitar(Request $request)
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
        $input = $request->only('articulosIds');

        $validator = Validator::make($input, [
            'articulosIds' => 'required|array',
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $encontrados = Articulo::find($input['articulosIds'])->count();

        if($encontrados !== count($input['articulosIds']))
            return response()->json(['error'=>'articulos con ids invalidos. Ids Enviados: '.count($input['articulosIds']).", Ids Encontrados en BD: ".$encontrados],400);


        $user->carrito()->detach($input['articulosIds']);

        return response()->json(['success'=>'true','carrito'=>$user->carrito()->get()],201);
    }

    public function vaciar(Request $request)
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
        return response()->json(['success'=>'true','borrados'=>$user->carrito()->detach()],201);
    }
}
