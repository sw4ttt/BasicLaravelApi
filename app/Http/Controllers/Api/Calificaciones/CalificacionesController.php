<?php

namespace App\Http\Controllers\Api\Calificaciones;

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

class CalificacionesController extends Controller
{
    public function all(Request $request)
    {
        $Calificaciones = Calificacion::all();
        return $Calificaciones;
    }
    public function add(Request $request)
    {
        $input = $request->only('idProfesor','idAlumno','periodo','data');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idAlumno' => 'required|numeric|exists:users,id',
            'periodo' => 'required|string',
            'data' => 'required|array'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        // SEGUN es asi. no se. por lo visto no es necesario.
//        $input['data'] = serialize($input['data']);
//        unserialize($score->data);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Calificacion::create($input);
        return response()->json(['success'=>true]);
    }
}
