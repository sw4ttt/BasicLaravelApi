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

    //$flight = App\Flight::find(1);

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
        $input = $request->only('idUser','title','content');
        $validator = Validator::make($input, [
            'idUser' => 'required|numeric|exists:users,id',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
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
        Noticia::destroy($id);
        return response()->json(['success'=>true]);
    }
}
