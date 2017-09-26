<?php

namespace App\Http\Controllers\Api\User;

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
use App\Materia;

use GuzzleHttp\Client;

$UserTypes = array("ADMIN","PROFESOR","REPRESENTANTE");

class UserController extends Controller
{
    public function all(Request $request)
    {
        $users = User::all();
        return $users;
    }
    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);
        return $user;
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

        $input = $request->only(
            'tlfDomicilio',
            'tlfCelular',
            'email',
            'password'
        );
        $validator = Validator::make($input, [
            'tlfDomicilio' => 'string',
            'tlfCelular' => 'string',
            'email' => 'email|unique:users,email',
            'password' => 'numeric|min:4'
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(),400);
        }

        $editable = false;

        if(strlen($input['tlfDomicilio'])>0)
        {
            $user->tlfDomicilio = $input['tlfDomicilio'];
            $editable = true;
        }

        if(strlen($input['tlfCelular'])>0)
        {
            $user->tlfCelular = $input['tlfCelular'];
            $editable = true;
        }

        if(strlen($input['email'])>0)
        {
            $user->email = $input['email'];
            $editable = true;
        }

        if(strlen($input['password'])>0)
        {
            $user->password = Hash::make($input['password']);
            $editable = true;
        }

        if($editable === true)
            $user->save();

        return response()->json(['success'=>true,'message'=>($editable===true?'editado':'nada que editar'),'user'=>$user]);

    }
    public function estudiantes(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return User::find($id)->estudiantes;
    }
    public function addEstudiantes(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id','idPersonal','nombre','grado');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id',
            'idPersonal' => 'required|numeric|unique:estudiantes,idPersonal',
            'nombre' => 'required|string',
            'grado' => 'required|numeric'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);

        if($user->type != 'REPRESENTANTE')
            return response()->json(['KEY'=>'WRONG_USERTYPE','MESSAGE'=>'Usuario no es tipo REPRESENTANTE'],400);
        $estudiante = new Estudiante([
            'idPersonal' => $input['idPersonal'],
            'nombre' => $input['nombre'],
            'grado' => $input['grado'],
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        return $user->estudiantes()->save($estudiante);
    }
    public function addMateria(Request $request, $idProfesor,$idMateria)
    {
        $request['idProfesor'] = $idProfesor;
        $request['idMateria'] = $idMateria;
        $input = $request->only('idProfesor','idMateria');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idMateria' => 'required|numeric|exists:materias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $profesor = User::find($idProfesor);

        if($profesor->type != 'PROFESOR')
            return response()->json(['KEY'=>'WRONG_USERTYPE','MESSAGE'=>'Usuario no es tipo PROFESOR'],400);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');


        $profesor->materias()
            ->attach($input['idMateria'],
                [
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);
        return response()->json(['success'=>true]);
    }
    public function materias(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);

        if ($user->type === 'PROFESOR')
            return $user->materias()->get();
        else
        {
            $estudiante = User::find($id)->estudiantes->first();
            if (is_null($estudiante))
                return [];
            return Materia::where('grado', $estudiante->grado)->get();
        }
    }
}
