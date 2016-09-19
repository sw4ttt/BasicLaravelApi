<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Hash;
use JWTAuth;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {        
    	$input = $request->only('email', 'password');
    	$input['password'] = Hash::make($input['password']);
    	User::create($input);
        return response()->json(['result'=>true]);
    }
    
    public function login(Request $request)
    {
    	$input = $request->all();
    	if (!$token = JWTAuth::attempt($input)) {
            return response()->json(['result' => 'wrong email or password.']);
        }
        	return response()->json(['result' => $token]);
    }
    
    public function get_user_details(Request $request)
    {
    	$input = $request->all();
    	$user = JWTAuth::toUser($input['token']);
        return response()->json(['result' => $user]);
    }   
}
