<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Materia;
use Illuminate\Support\Facades\Storage;
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
use App\Articulo;

class ArticulosController extends Controller
{
    public function all(Request $request)
    {
        $articulos = Articulo::all();
        return view('articulos/articulos', ['articulos' => $articulos]);
    }

    public function add(Request $request)
    {
        $input = $request->only('nombre','cantidad','estado','precio','image','categoria','descripcion');
        $validator = Validator::make($input, [
            'nombre' => 'required|string|unique:articulos,nombre',
            'cantidad' => 'required_unless:categoria,MATRICULA|integer|between:1,999999',
            'estado' => 'required|string|in:HABILITADO,DESHABILITADO',
            'precio' => 'required|integer|between:1,1000000',
            'categoria' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        if($input['categoria'] != 'MATRICULA')
        {
            $validator = Validator::make($input, [
                'image' => 'required|image',
            ]);
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return back()->withErrors($validator)->withInput();
            }

            if (!$request->file('image')->isValid()) {
                return response()->json(["error"=>"error with image file."],400);
            }
            $input['image'] = Storage::put('images', $input['image']);
            $input['image'] = asset($input['image']);
        }

        if($input['categoria'] === 'MATRICULA')
            $input['cantidad'] = null;

        Articulo::create($input);

        return back()->with('message', 'Articulo Creado!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only('nombre', 'grado', 'idProfesor');
        $input['id'] = $id;
        $input['nombre'] = strtoupper($input['nombre']);
        $validator = Validator::make($input, [
            'grado' => 'required|numeric|between:1,15',
            'nombre' => 'required|string|unique:materias,nombre',
            'idProfesor' => 'required|string|not_in:VACIO|exists:users,nombre',
            'id' => 'required|numeric|exists:materias,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $materia = Materia::find($id);
        $profesor = User::where('nombre',$input['idProfesor'])->first();

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
            'id' => 'required|numeric|exists:articulos,id'
        ]);
        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $articulo = Articulo::find($input['id']);


        print_r (explode(" ",$articulo->image));

        return 1;




//        $articulo->delete();


//        return back()->with('message', 'Articulo Eliminada!');

//        if(!Storage::exists('images/'.$filename))  return response('Imagen no existe.',404);
//        $contents = Storage::get('images/'.$filename);
//        $response = Response::make($contents, 200);
//        return $response->header("Content-Type", Storage::mimeType('images/'.$filename));
    }
}
