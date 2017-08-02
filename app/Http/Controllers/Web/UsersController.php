<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NotificacionGeneral;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use OneSignal;
class UsersController extends Controller
{
    public function all(Request $request)
    {
        $users = User::all();

        OneSignal::sendNotificationToAll("Some Message", $url = null, $data = null, $buttons = null, $schedule = null);

        return view('users/users', ['users' => $users]);
    }

    public function add(Request $request)
    {
        $UserTypes = array("ADMIN","PROFESOR","REPRESENTANTE");

        $input = $request->only(
            'tipoIdPersonal',
            'idPersonal',
            'tlfDomicilio',
            'tlfCelular',
            'direccion',
            'nombre',
            'email',
            'image',
            'password',
            'type');
        $validator = Validator::make($input, [
            'tipoIdPersonal' => 'required|string',
            'idPersonal' => 'required|numeric|unique:users,idPersonal',
            'nombre' => 'required|string',
            'tlfDomicilio' => 'required|string',
            'tlfCelular' => 'required|string',
            'direccion' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'image' => 'required|image',
            'password' => 'required|min:4',
            'type' => 'required|string'
        ]);

//        if(array_search(strtoupper($input['type']),$UserTypes) == false)
//            return back()->withErrors(["type"=>"Wrong User Type.","valid types"=>$UserTypes])->withInput();
//
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['email'] = strtolower($input['email']);
        $input['image'] = Storage::put('images', $request->image);
        $input['image'] = str_replace('public','storage',$input['image'],$i);
        $input['image'] = url('/')."/".$input['image'];
        $input['type'] = strtoupper($input['type']);
        $input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        User::create($input);
        $users = User::all();
        return view('users/users', ['users' => $users]);
    }

}
