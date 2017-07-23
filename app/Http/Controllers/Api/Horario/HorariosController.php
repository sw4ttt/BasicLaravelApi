<?php

namespace App\Http\Controllers\Api\Horario;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Materia;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Horario;

class HorariosController extends Controller
{
    //$table->string('tipo');
    //$table->string('idMateria');
    //$table->string('nombre');
    //$table->string('dia');
    //$table->integer('grado');
    //$table->string('lugar');

    public function all(Request $request)
    {
        $Horarios = Horario::all();
        return $Horarios;
    }
    public function add(Request $request)
    {
        $input = $request->only('tipo','idMateria','nombre','dia','grado','lugar');

        $validator = Validator::make($input, [
            'tipo' => 'required|string',
            'dia' => 'required|string'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $input['tipo'] = strtoupper($input['tipo']);
        $input['dia'] = strtoupper($input['dia']);

        $validator = Validator::make($input, [
            'tipo' => 'required|string',
            'idMateria' => 'required_if:tipo,MATERIA|string|exists:materias,id',
            'nombre' => 'required_if:tipo,EXTRA|string',
            'dia' => 'required|string',
            'grado' => 'required|numeric',
            'lugar' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

//        Materia::create($input);
        return response()->json(['success'=>true]);
    }
}
