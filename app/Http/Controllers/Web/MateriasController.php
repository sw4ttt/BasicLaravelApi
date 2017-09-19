<?php

namespace App\Http\Controllers\Web;

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
use App\Material;

class MateriasController extends Controller
{
    public function all(Request $request)
    {
        $materias = Materia::all();
        return view('materias/materias', ['materias' => $materias]);
    }

    public function add(Request $request)
    {
        $input = $request->only('nombre','grado','idProfesor');
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'nombre' => 'required|string|unique:materias,nombre',
            'idProfesor' => 'required|string|exists:users,nombre'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $materia = Materia::create($input);
//        $materia->profesores()->attach($input['idProfesor']);
//
//        $estudiantes = Estudiante::where('grado',$input['grado'])->get();
//
//        foreach ($estudiantes as $estudiante) {
//            Calificacion::create([
//                'idProfesor'=>$input['idProfesor'],
//                'idEstudiante'=>$estudiante->id,
//                'idMateria'=>$materia->id,
//                'periodo'=>'2017-2018',
//                'evaluaciones'=>[],
//                'created_at'=>$input['created_at'],
//                'updated_at'=>$input['updated_at'],
//            ]);
//        }
//        return view('materias/add', ['message'=>'Materia Creada!']);

        return back()->with('message', 'Materia Creada!');
    }

    public function editGet(Request $request,$id)
    {
        $materia = Materia::where('nombre',$id)->first();
        if(is_null($materia))
            return back()->with('message', 'Materia No Existe!');

        $profesor = User::find($materia->idProfesor);

        $materia->profesor = $profesor->nombre;

        return view('materias/edit', ['materia' => $materia]);
    }

    public function edit(Request $request)
    {
        $input = $request->only('nombre','grado','idProfesor');
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'nombre' => 'required|string|unique:materias,nombre',
            'idProfesor' => 'required|string|exists:users,nombre'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $order->update([
            'state' => "APPROVED",
            'payu_order_id' => $response->transactionResponse->orderId,
            'transaction_id' => $response->transactionResponse->transactionId
        ]);



        return back()->with('message', 'Materia Editada!');
    }

    public function delete(Request $request)
    {
        $input = $request->only(
            'id'
        );
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materias,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $materia = Materia::find($input['id']);

        $materia->profesores()->detach();

        Calificacion::where('idMateria',$materia->id)->delete();

        $materia->delete();

        return back();
    }
}
