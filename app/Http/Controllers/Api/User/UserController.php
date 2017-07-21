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
        $input = $request->only('id','idPersonal','nombre');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id',
            'idPersonal' => 'required|numeric|unique:estudiantes,idPersonal',
            'nombre' => 'required|string'
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
        $profesor = User::find($id);
        return $profesor->materias()->get();
    }
}
