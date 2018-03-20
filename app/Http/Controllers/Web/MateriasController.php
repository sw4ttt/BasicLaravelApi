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
use App\Curso;
use Illuminate\Contracts\Encryption\DecryptException;

class MateriasController extends Controller
{
    public function all(Request $request)
    {
        $materias = Materia::all();
        $materias->transform(function ($item, $key) {
            switch ($item->grado) {
                case 1:
                    $item->gradoTexto = "Primero";
                    break;
                case 2:
                    $item->gradoTexto = "Segundo";
                    break;
                case 3:
                    $item->gradoTexto = "Tercero";
                    break;
                case 4:
                    $item->gradoTexto = "Cuarto";
                    break;
                case 5:
                    $item->gradoTexto = "Quinto";
                    break;
                case 6:
                    $item->gradoTexto = "Sexto";
                    break;
                case 7:
                    $item->gradoTexto = "Septimo";
                    break;
                case 8:
                    $item->gradoTexto = "Octavo";
                    break;
                case 9:
                    $item->gradoTexto = "Noveno";
                    break;
                case 10:
                    $item->gradoTexto = "Decimo";
                    break;
                case 11:
                    $item->gradoTexto = "Pre-Jardin";
                    break;
                case 12:
                    $item->gradoTexto = "Jardin";
                    break;
                case 13:
                    $item->gradoTexto = "Transicion";
                    break;
                case 14:
                    $item->gradoTexto = "Parvulo";
                    break;
                default:
                    $item->gradoTexto = "Otro";
            }
            return $item;
        });
        return view('materias/materias', ['materias' => $materias]);
    }

    public function add(Request $request)
    {
        $input = $request->only('nombre', 'curso', 'idProfesor');
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'curso' => 'required|numeric|exists:cursos,id',
            'nombre' => 'required|string',
            'idProfesor' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $curso = Curso::find($input['curso']);

        $materiaExistente = Materia::where('nombre',$input['nombre'])->where('grado',$curso->grado)->where('seccion',$curso->seccion)->first();


        if(!is_null($materiaExistente)) {
            return back()->withErrors([
                'curso'=>['Ya existe una materia con ese nombre, grado y seccion']
            ])->withInput();
        }

        $input['grado'] = $curso->grado;
        $input['seccion'] = $curso->seccion;


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

        $estudiantes = Estudiante::where('grado',$input['grado'])->where('seccion',$input['seccion'])->get();

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

        return redirect("materias/add")->with('message', 'Materia Creada!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only('nombre', 'curso', 'idProfesor');
        $input['id'] = $id;
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'curso' => 'required|numeric|exists:cursos,id',
            'nombre' => 'required|string',
            'idProfesor' => 'required|numeric|exists:users,id',
            'id' => 'required|numeric|exists:materias,id'
        ]);

        $curso = Curso::find($input['curso']);

        $materiaExistente = Materia::where('nombre',$input['nombre'])->where('grado',$curso->grado)->where('seccion',$curso->grado)->first();

        if(!is_null($materiaExistente) && ($materiaExistente->id != $input['id'])) {
            return back()->withErrors([
                'curso'=>['Ya existe una materia con ese nombre, grado y seccion']
            ])->withInput();
        }

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $materia = Materia::find($id);
        $profesor = User::where('id',$input['idProfesor'])->first();

        $materia->profesores()->sync([$profesor->id=>['updated_at' => $input['updated_at']]]);

//        Calificacion::where('idMateria', $id)
//            ->update([
//                    'idProfesor' => $profesor->id,
//                    'updated_at' => $input['updated_at']
//                ]
//            );

        if(($materia->grado !== $curso->grado) && ($materia->seccion !== $curso->seccion)){

            Calificacion::where('idMateria', $id)->delete();

            $estudiantes = Estudiante::where('grado',$curso->grado)->where('seccion',$curso->seccion)->get();

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
        }

        $materia->nombre = $input['nombre'];
        $materia->grado = $curso->grado;
        $materia->seccion = $curso->seccion;
        $materia->updated_at = $input['updated_at'];

        $materia->save();

        return redirect("materias/edit/".$id)->with('message', 'Materia Editada!');
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
