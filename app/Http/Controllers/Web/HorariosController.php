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
use App\Materia;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NotificacionGeneral;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use OneSignal;
use App\Horario;

class HorariosController extends Controller
{
    public function all(Request $request)
    {
        return view('horarios/horarios', ['horarios' => Horario::all()]);
    }
    public function add(Request $request)
    {
        $input = $request->only(
            'entidad',
            'idEntidadProfesor',
            'idEntidadMateria',
            'descripcion',
            'dia',
            'inicio',
            'fin',
            'grado',
            'lugar'
        );
        $validator = Validator::make($input, [
            'entidad' => 'required|string|in:MATERIA,PROFESOR,GENERAL',
            'idEntidadProfesor' => 'required_if:entidad,PROFESOR',
            'idEntidadMateria' => 'required_if:entidad,MATERIA',
            'descripcion' => 'required_if:entidad,GENERAL',
            'dia' => 'required|string',
            'inicio' => 'required|string',
            'fin' => 'required|string',
            'grado' => 'required_if:entidad,GENERAL|integer',
            'lugar' => 'required|string'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        if($input['entidad'] === 'MATERIA' || $input['entidad'] === 'PROFESOR')
        {
            switch ($input['entidad']) {
                case 'MATERIA':
                    $validator = Validator::make($input, [
                        'idEntidadMateria' => 'required|integer|exists:materias,id'
                    ]);
                    break;
                case 'PROFESOR':
                    $validator = Validator::make($input, [
                        'idEntidadProfesor' => 'required|integer|exists:users,id'
                    ]);
                    break;
            }
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return back()->withErrors($validator)->withInput();
            }

            if($input['entidad']=== 'MATERIA'){
                $input['nombreEntidad']=Materia::find($input['idEntidadMateria'])->nombre;
                $input['idEntidad'] = $input['idEntidadMateria'];
            }

            elseif($input['entidad']=== 'PROFESOR'){
                $input['nombreEntidad']=User::find($input['idEntidadProfesor'])->nombre;
                $input['idEntidad'] = $input['idEntidadProfesor'];
            }
            unset($input['descripcion']);

        }

        if($input['entidad'] === 'GENERAL'){
            unset($input['idEntidadMateria']);
            unset($input['idEntidadProfesor']);
            unset($input['idEntidad']);
            unset($input['nombreEntidad ']);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Horario::create($input);

        return back()->with('message', 'Horario Creado!');
    }
    public function edit(Request $request,$id)
    {
        $request['id']= $id;
        $input = $request->only(
            'id',
            'entidad',
            'idEntidadProfesor',
            'idEntidadMateria',
            'descripcion',
            'dia',
            'inicio',
            'fin',
            'grado',
            'lugar'
        );
        //"input":{"entidad":"GENERAL","idEntidad":null,"descripcion":"<sasasdasd","dia":"LUNES","inicio":"20:30","fin":"20:25","grado":null,"lugar":"dddd"}

        $validator = Validator::make($input, [
            'id' => 'required|integer|exists:horarios,id',
            'entidad' => 'required|string|in:MATERIA,PROFESOR,GENERAL',
            'idEntidadProfesor' => 'required_if:entidad,PROFESOR',
            'idEntidadMateria' => 'required_if:entidad,MATERIA',
            'descripcion' => 'required_if:entidad,GENERAL',
            'dia' => 'required|string',
            'inicio' => 'required|string',
            'fin' => 'required|string',
            'grado' => 'required_if:entidad,GENERAL|integer',
            'lugar' => 'required|string'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        if($input['entidad'] === 'MATERIA' || $input['entidad'] === 'PROFESOR')
        {
            switch ($input['entidad']) {
                case 'MATERIA':
                    $validator = Validator::make($input, [
                        'idEntidadMateria' => 'required|integer|exists:materias,id'
                    ]);
                    break;
                case 'PROFESOR':
                    $validator = Validator::make($input, [
                        'idEntidadProfesor' => 'required|integer|exists:users,id'
                    ]);
                    break;
            }
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return back()->withErrors($validator)->withInput();
            }

            if($input['entidad']=== 'MATERIA'){
                $input['nombreEntidad']=Materia::find($input['idEntidadMateria'])->nombre;
                $input['idEntidad'] = $input['idEntidadMateria'];
            }

            elseif($input['entidad']=== 'PROFESOR'){
                $input['nombreEntidad']=User::find($input['idEntidadProfesor'])->nombre;
                $input['idEntidad'] = $input['idEntidadProfesor'];
            }
            unset($input['descripcion']);

        }

        if($input['entidad'] === 'GENERAL'){
            unset($input['idEntidadMateria']);
            unset($input['idEntidadProfesor']);
            unset($input['idEntidad']);
            unset($input['nombreEntidad ']);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $horario = Horario::find($input['id']);
        $horario->fill($input);
        $horario->save();
//        Horario::create($input);

        return back()->with('message', 'Horario Editado!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:horarios,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $horario = Horario::find($input['id']);

        $horario->delete();

        return redirect("horarios")->with('message', 'Horario Eliminado!');

    }
}
