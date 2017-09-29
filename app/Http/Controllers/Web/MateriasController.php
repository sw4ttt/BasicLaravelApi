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
use Illuminate\Contracts\Encryption\DecryptException;

class MateriasController extends Controller
{
    public function all(Request $request)
    {
        $materias = Materia::all();
        return view('materias/materias', ['materias' => $materias]);
    }

    public function add(Request $request)
    {
        $input = $request->only('nombre', 'grado', 'idProfesor');
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'nombre' => 'required|string|unique:materias,nombre',
            'idProfesor' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $materia = Materia::create($input);
        $profesor = User::where('id',$input['idProfesor'])->first();

        $materia->profesores()->attach(
            [
                $profesor->id=>[
                    'created_at' => $input['created_at'],
                    'updated_at' => $input['updated_at']
                ]
            ]
        );

        $estudiantes = Estudiante::where('grado',$input['grado'])->get();

        foreach ($estudiantes as $estudiante) {
            Calificacion::create([
                'idProfesor'=>$profesor->id,
                'idEstudiante'=>$estudiante->id,
                'idMateria'=>$materia->id,
                'periodo'=>'2017-2018',
                'evaluaciones'=>[],
                'acumulado'=>0,
                'created_at'=>$input['created_at'],
                'updated_at'=>$input['updated_at'],
            ]);
        }

        return back()->with('message', 'Materia Creada!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only('nombre', 'grado', 'idProfesor');
        $input['id'] = $id;
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'nombre' => 'required|string|unique:materias,nombre',
            'idProfesor' => 'required|numeric|exists:users,id',
            'id' => 'required|numeric|exists:materias,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $materia = Materia::find($id);
        $profesor = User::where('id',$input['idProfesor'])->first();

        $materia->profesores()->sync([$profesor->id=>['updated_at' => $input['updated_at']]]);

        Calificacion::where('idMateria', $id)
            ->update([
                    'idProfesor' => $profesor->id,
                    'updated_at' => $input['updated_at']
                ]
            );

        $materia->nombre = $input['nombre'];
        $materia->grado = $input['grado'];
        $materia->updated_at = $input['updated_at'];

        $materia->save();

        return back()->with('message', 'Materia Editada!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materias,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $materia = Materia::find($input['id']);

        $materia->profesores()->detach();

        Calificacion::where('idMateria', $materia->id)->delete();

        $materia->delete();

        return redirect("materias")->with('message', 'Materia Eliminada!');

    }
}
