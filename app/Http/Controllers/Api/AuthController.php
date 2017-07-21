<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use Exception;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $UserTypes = array("ADMIN","PROFESOR","REPRESENTANTE");

    	$input = $request->only('idPersonal','nombre','email', 'password','type');
        $validator = Validator::make($input, [
            'idPersonal' => 'required|numeric|unique:users,idPersonal',
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'type' => 'required|string'
        ]);
        if(array_search(strtoupper($input['type']),$UserTypes) == false)
            return response()->json(["type"=>"Wrong User Type.","valid types"=>$UserTypes],400);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $input['type'] = strtoupper($input['type']);
    	$input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    	User::create($input);
        return response()->json(['success'=>true]);
    }
    
    public function login(Request $request)
    {
    	$input = $request->only('email', 'password');
        
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            
            return response()->json($validator->errors(),400);
        }

    	if (!$token = JWTAuth::attempt($input)) {
            return response()->json(['result' => 'wrong email or password.'],400);
        }
        $user = JWTAuth::toUser($token);

        return response()->json(['user'=>$user,'token' => $token]);
    }

    public function me(Request $request)
    {
//        $input = $request->only('token');
        try {
//            $user = JWTAuth::toUser($input['token']);
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
        return $user;
    }

    
    public function refreshToken(Request $request)
    {
        try {
            $newToken = JWTAuth::refresh($request->bearerToken());
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired'],401);
            }else{
                return response()->json(['error'=>'Token Missing2'],400);
            }
        }
        return response()->json(['token' => $newToken]);
    }   
}
