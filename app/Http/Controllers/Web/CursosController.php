<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Materia;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Curso;

class CursosController extends Controller
{
    public function all(Request $request)
    {
        $cursos = Curso::all();
        $cursos->transform(function ($item, $key) {
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
        return view('cursos/cursos', ['cursos' => $cursos]);
    }

    public function add(Request $request)
    {
        $input = $request->only('grado', 'seccion','cupos');
        $input['seccion'] = strtoupper($input['seccion']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'cupos' => 'required|integer',
            'seccion' => 'required|string'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $curso = Curso::where('grado',$input['grado'])->where('seccion',$input['seccion'])->get()->first();

        if(!is_null($curso))
            return back()->withErrors([
                'seccion'=>['Ya existe un curso con la misma informacion. (Grado - Seccion)']
            ])->withInput();

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $curso = Curso::create($input);

        return back()->with('message', 'Curso Creado!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only('seccion', 'grado','cupos');
        $input['id'] = $id;
        $input['seccion'] = strtoupper($input['seccion']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'cupos' => 'required|integer',
            'seccion' => 'required|string',
            'id' => 'required|numeric|exists:cursos,id'
        ]);


        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $curso = Curso::find($id);

        $curso->seccion = $input['seccion'];
        $curso->grado = $input['grado'];
        $curso->cupos = $input['cupos'];
        $curso->updated_at = $input['updated_at'];

        $curso->save();

        return redirect("cursos/edit/".$id)->with('message', 'Curso Editado!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:cursos,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $curso = Curso::find($input['id']);

        $curso->delete();

        return redirect("cursos")->with('message', 'Curso Eliminado!');

    }
}
