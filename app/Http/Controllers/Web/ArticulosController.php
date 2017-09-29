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

        if($input['categoria'] === 'MATRICULA'){
            $input['cantidad'] = null;
            $input['image'] = null;
        }

        Articulo::create($input);

        return back()->with('message', 'Articulo Creado!');
    }

    public function edit(Request $request,$id)
    {
        $articulo = Articulo::find($id);
        if(!$articulo)
            back()->withErrors(['invalid'=>['El id de articulo seleccionado no es valido.']]);

        $input = $request->only('nombre','cantidad','estado','precio','image','categoria','descripcion');

        if(!isset($input['categoria']))
            $input['categoria'] = $articulo->categoria;
        if(!isset($input['cantidad']))
            $input['cantidad'] = 1;



        $validator = Validator::make($input, [
            'nombre' => 'required|string|unique:articulos,nombre',
            'cantidad' => 'required_unless:categoria,MATRICULA|integer|between:1,999999',
            'estado' => 'required|string|in:HABILITADO,DESHABILITADO',
            'precio' => 'required|integer|between:1,1000000',
            'categoria' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());

            return back()->withErrors($validator)->withInput();
        }

        $articulo->nombre = $input['nombre'];
        $articulo->cantidad = $input['cantidad'];
        $articulo->estado = $input['estado'];
        $articulo->precio = $input['precio'];
        $articulo->categoria = $input['categoria'];
        $articulo->descripcion = $input['descripcion'];
        $articulo->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        if($input['categoria'] != 'MATRICULA')
        {
            $validator = Validator::make($input, [
                'image' => 'required|image',
            ]);
            if($validator->fails()) {
                //throw new ValidationHttpException($validator->errors()->all());
                return back()->withErrors($validator)->withInput();
            }

            $auxPath = explode("images",$articulo->image);

            if(Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);

            if (!$request->file('image')->isValid()) {
                return response()->json(["error"=>"error with image file."],400);
            }
            $input['image'] = Storage::put('images', $input['image']);
            $articulo->image = asset($input['image']);
        }

        if($input['categoria'] === 'MATRICULA'){
            $articulo->cantidad = null;
            $articulo->image= null;
        }

        $articulo->save();

        return back()->with('message', 'Articulo/Pagable Editado!');
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

        if(isset($articulo->image)){
            $auxPath = explode("images",$articulo->image);

            if(Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);
        }
        $articulo->delete();

        return redirect("articulos")->with('message', 'Articulo/Pagable Eliminado!');

    }
}
