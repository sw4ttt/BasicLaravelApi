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
        $estudiantes = User::find($id)->estudiantes;
        return $estudiantes;
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

        $estudiante = new Estudiante([
            'idPersonal' => $input['idPersonal'],
            'nombre' => $input['nombre'],
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        return $user->estudiantes()->save($estudiante);
    }
}
