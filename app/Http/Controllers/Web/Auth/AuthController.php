<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use Exception;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $input = $request->only('email', 'password');

        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());

            return back()->withErrors($validator)->withInput();
//            return response()->json($validator->errors(),400);
        }


        if (Auth::attempt(['email' => strtolower($input['email']), 'password' => $input['password']])) {
            // Authentication passed...
            if(Auth::user()->type === 'ADMIN')
                return redirect('home');
            else
            {
                Auth::logout();
                return back()->withErrors(['userType'=>['Debes ser ADMIN para ingresar a la parte web.']])->withInput();
            }
        }
        return back()->withErrors(['password'=>['Email o Password Invalido.']])->withInput();
    }
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('login');
    }
}
