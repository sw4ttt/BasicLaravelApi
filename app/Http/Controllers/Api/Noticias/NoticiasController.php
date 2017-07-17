<?php

namespace App\Http\Controllers\Api\Noticias;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
class NoticiasController extends Controller
{
    public function all(Request $request)
    {
        $noticias = Noticia::all();
        return $noticias;
    }

    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:noticias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $noticia = Noticia::find($id);
        return $noticia;
    }

    public function add(Request $request)
    {
        $input = $request->only('idUser','imagen','titulo','contenido');
        $validator = Validator::make($input, [
            'idUser' => 'required|numeric|exists:users,id',
            'imagen' => 'required|image',
            'titulo' => 'required|string',
            'contenido' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if (!$request->file('image')->isValid()) {
            return response()->json(["error"=>"error with image file."],400);
        }
        $path = $request->imagen->store('public/images');
        $path = str_replace('public','storage',$path,$i);
        $input['imagen'] = $path;
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        Noticia::create($input);
        return response()->json(['success'=>true]);
    }

    public function delete(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:noticias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $noticia = Noticia::find($id);
        $noticia->imagen = str_replace('public','storage',$noticia->image,$i);
        $path = realpath('./../public/').'/'.$noticia->image;
        $path = str_replace('\\', '/', $path);
        unlink($path);
        Noticia::destroy($id);
        return response()->json(['success'=>true]);
    }
}
