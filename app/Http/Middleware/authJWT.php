<?php

namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Exception;
class authJWT
{
    public function handle($request, Closure $next)
    {
        try {
//            $user = JWTAuth::toUser($request->input('token'));
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
        return $next($request);
    }
}
