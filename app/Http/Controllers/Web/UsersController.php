<?php

namespace App\Http\Controllers\Web;

use App\Tarjeta;
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
use App\Materia;
use OneSignal;
use App\Mail\OlvidoPassword;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
/**
 * Class UsersController
 * @package App\Http\Controllers\Web
 */
class UsersController extends Controller
{
    public function all(Request $request)
    {
        $users = User::all();

//        Mail::to('oscar.marquez.to@gmail.com')->send(new OlvidoPassword);

//        OneSignal::sendNotificationToAll("Some Message", $url = 'do_not_open', $data = ["dataTest"=>'test value'], $buttons = null, $schedule = null);

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
            'type' => 'required|string|in:ADMIN,PROFESOR,REPRESENTANTE'
        ]);

//        if(array_search(strtoupper($input['type']),$UserTypes) == false)
//            return back()->withErrors(["type"=>"Wrong User Type.","valid types"=>$UserTypes])->withInput();
//
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }
        $userType = $input['type'];

        if($userType === 'REPRESENTANTE')
        {
            $input['nombreEstudiante'] = $request['nombreEstudiante'];
            $input['idPersonalEstudiante'] = $request['idPersonalEstudiante'];
            $input['grado'] = $request['grado'];

            $validator = Validator::make($input, [
                'nombreEstudiante' => 'required|string',
                'idPersonalEstudiante' => 'required|numeric|unique:estudiantes,idPersonal',
                'grado' => 'required|integer'
            ]);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }


        $input['email'] = strtolower($input['email']);
        $input['image'] = Storage::put('images', $request->image);
        $input['image'] = str_replace('public','storage',$input['image'],$i);
        $input['image'] = url('/')."/".$input['image'];
        $input['type'] = strtoupper($input['type']);
        $input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $user = User::create($input);

        if($userType === 'REPRESENTANTE')
        {
            $input['idUser'] = $user->id;
            $input['idPersonal'] = $input['idPersonalEstudiante'];
            $input['nombre'] = $input['nombreEstudiante'];

            $estudiante = Estudiante::create($input);

            $materias = Materia::where('grado',$input['grado'])->get();
            foreach ($materias as $materia) {
                $profesor = $materia->profesores()->get()->first();
                if(!is_null($profesor))
                {
                    Calificacion::create([
                        'idProfesor'=>$profesor->id,
                        'idEstudiante'=>$estudiante->id,
                        'idMateria'=>$materia->id,
                        'periodo'=>'2017-2018',
                        'evaluaciones'=>[],
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        return back()->with('message', 'Usuario Creado!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only('nombre', 'email');
        $input['id'] = $id;

        $validator = Validator::make($input, [
            'email' => 'required|string',
            'nombre' => 'required|string',
            'id' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $usuario = User::find($id);

        if($usuario->email !== $input['email']){
            $validator = Validator::make($input, [
                'email' => 'required|string|unique:users,email',
            ]);
            if ($validator->fails())
                return back()->withErrors($validator)->withInput();
        }

        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $usuario->nombre = $input['nombre'];
        $usuario->email = $input['email'];
        $usuario->updated_at = $input['updated_at'];

        $usuario->save();

        return back()->with('message', 'Usuario Editado!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $usuario = User::find($input['id']);

        if(isset($usuario->image)){
            $auxPath = explode("images",$usuario->image);
            if($auxPath[1] !== 'default-profile.jpg' && Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);
        }


        $tarjetas = $usuario->tarjetas();
        if(is_array($tarjetas) && count($tarjetas) > 0)
            $usuario->tarjetas()->detach();

        Tarjeta::where("idUsuario",$usuario->id)->delete();

        $carrito = $usuario->carrito();
        if(is_array($carrito) && count($carrito) > 0)
            $usuario->carrito()->detach();


        $estudiante = $usuario->estudiantes()->first();


        if(!is_null($estudiante)){
            Calificacion::where("idEstudiante",$estudiante->id)->delete();
            $estudiante->delete();
        }



        $materias = $usuario->materias()->get();

        $usuario->materias()->detach();


        if(is_array($materias) && count($materias) > 0){
            $usuario->materias()->detach();
            Calificacion::where("idProfesor",$usuario->id)->delete();
        }


        foreach ($materias as $materia){
            $materia->delete();
        }

        $usuario->delete();

        return redirect("users")->with('message', 'Usuario Eliminado!');

    }

    public function addBulk(Request $request)
    {
        $input = $request->only('usersFile');

        $validator = Validator::make($input, [
            'usersFile' => 'required|file'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $results = [];
        $rejected = [];
        $filtered = [];
        $valid = true;

        Excel::load($input['usersFile'], function($reader) use (&$results,&$filtered,&$valid) {
            $results = $reader->takeRows(3)->get();

            if($results->count()===0){
                $valid = false;
            }
            else
                foreach ($results as $row){
                    $check = array_has($row, [
                        'tipoidpersonal',
                        'idpersonal',
                        'tlfdomicilio',
                        'tlfcelular',
                        'direccion',
                        'nombre',
                        'email',
                        'type',
                        'nombreestudiante',
                        'idpersonalestudiante',
                        'grado'
                    ]);
                    if(!$check)
                        $valid = false;
                }
        });

        if(!$valid)
            return back()->withErrors(['usersFile'=>['El archivo contiene un estructura invalida. utiliza el archivo de ejemplo.']])->withInput();


        $index = 1;
        foreach ($results as $row){

            $rowValidation = $row->toArray();

            $rowValidation['password'] = 123456;
            $rowValidation['image'] = url('/')."/images/default-profile.jpg";

            $rowValidation['tipoidpersonal'] = strval($rowValidation['tipoidpersonal']);
            $rowValidation['idpersonal'] = strval($rowValidation['idpersonal']);
            $rowValidation['nombre'] = strval($rowValidation['nombre']);
            $rowValidation['tlfdomicilio'] = "+".strval($rowValidation['tlfdomicilio']);
            $rowValidation['tlfcelular'] = "+".strval($rowValidation['tlfcelular']);
            $rowValidation['direccion'] = strval($rowValidation['direccion']);
            $rowValidation['email'] = strval($rowValidation['email']);
            $rowValidation['password'] = strval($rowValidation['password']);
            $rowValidation['type'] = strval($rowValidation['type']);

            $rowValidation['nombreestudiante'] = strval($rowValidation['nombreestudiante']);
            $rowValidation['idpersonalestudiante'] = strval($rowValidation['idpersonalestudiante']);
            $rowValidation['grado'] = strval($rowValidation['grado']);

            $validator = Validator::make($rowValidation, [
                'tipoidpersonal' => 'required|string',
                'idpersonal' => 'required|numeric|unique:users,idPersonal',
                'nombre' => 'required|string',
                'tlfdomicilio' => 'required|string|regex:/^((\+57)(\d){6,8})$/',
                'tlfcelular' => 'required|string|regex:/^((\+57)(\d){6,8})$/',
                'direccion' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'type' => 'required|string|in:ADMIN,PROFESOR,REPRESENTANTE'
            ]);

            if(!$validator->fails()) {
                if(strtoupper($rowValidation['type']) === 'REPRESENTANTE'){
                    $validator = Validator::make($rowValidation, [
                        'nombreestudiante' => 'required|string',
                        'idpersonalestudiante' => 'required|numeric|unique:estudiantes,idPersonal',
                        'grado' => 'required|numeric|in:1,2,3,4,5,6',
                    ]);
                    if(!$validator->fails()){
                        array_push($filtered,$rowValidation);
                    }
                    else {
                        $rowValidation['index'] = $index;
                        array_push($rejected,$rowValidation);
                    }
                }
                else{
                    array_push($filtered,$rowValidation);
                }
            }
            else{
                $rowValidation['index'] = $index;
                array_push($rejected,$rowValidation);
            }
            $index++;
        }

        if(count($filtered)===0){
            return back()->withErrors(['usersFile'=>['El archivo contiene un estructura invalida. utiliza el archivo de ejemplo.']])->with('rejected',$rejected);
        }

        foreach ($filtered as $account){

            $newAccount = [];

            $newAccount['tipoIdPersonal'] = $account['tipoidpersonal'] ;
            $newAccount['idPersonal'] = $account['idpersonal'] ;
            $newAccount['nombre'] = $account['nombre'] ;
            $newAccount['tlfDomicilio'] = $account['tlfdomicilio'] ;
            $newAccount['tlfCelular'] = $account['tlfcelular'] ;
            $newAccount['direccion'] = $account['direccion'] ;
            $newAccount['email'] = $account['email'] ;
            $newAccount['password'] = Hash::make($account['password']);
            $newAccount['image'] = $account['image'] ;
            $newAccount['type'] = $account['type'] ;

            $user = User::create($newAccount);

            if($user->type === 'REPRESENTANTE')
            {
                $auxEstudiante = [];
                $auxEstudiante['idUser'] = $user->id;
                $auxEstudiante['idPersonal'] = $account['idpersonalestudiante'];
                $auxEstudiante['nombre'] = $account['nombreestudiante'];
                $auxEstudiante['grado'] = $account['grado'];

                $estudiante = Estudiante::create($auxEstudiante);

                $materias = Materia::where('grado',$account['grado'])->get();
                foreach ($materias as $materia) {
                    $profesor = $materia->profesores()->get()->first();
                    if(!is_null($profesor))
                    {
                        Calificacion::create([
                            'idProfesor'=>$profesor->id,
                            'idEstudiante'=>$estudiante->id,
                            'idMateria'=>$materia->id,
                            'periodo'=>'2017-2018',
                            'evaluaciones'=>[],
                            'acumulado'=>0,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

        }
        return redirect("users/add/masivo")->with(['message'=>'Usuarios Agregados!','rejected'=>$rejected]);
    }

}
