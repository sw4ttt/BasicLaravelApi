<?php

namespace App\Http\Controllers\Api\Noticias;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use OneSignal;

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
        $input = $request->only('idUser','image','title','content');
        $validator = Validator::make($input, [
            'idUser' => 'required|numeric|exists:users,id',
            'image' => 'required|image',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if (!$request->file('image')->isValid()) {
            return response()->json(["error"=>"error with image file."],400);
        }
        $input['image'] = Storage::put('images', $request->image);
//        $path = $request->image->store('public/images');
//        $path = str_replace('public','storage',$path,$i);
//        $input['image'] = url('/')."/".$path;
//        $input['image'] = Storage::url('BLRUBLRU.jpg');
        $input['image'] = str_replace('public','storage',$input['image'],$i);
        $input['image'] = url('/')."/".$input['image'];
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $noticia = Noticia::create($input);

        OneSignal::sendNotificationToAll(
            "Nueva Noticia: ".$noticia->title,
            $url = null,
            [
                "key"=>"NOTICIA",
                "id"=>$noticia->id,
                "title"=>$noticia->title,
                "content"=>$noticia->content,
                "image"=>$noticia->image,
                "fecha"=>$noticia->created_at
            ],
            $buttons = null,
            $schedule = null
        );

        return response()->json(['success'=>true,'image'=>$input['image']]);
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
        $noticia->image = str_replace('public','storage',$noticia->image,$i);
        $path = realpath('./../public/').'/'.$noticia->image;
        $path = str_replace('\\', '/', $path);

        if( file_exists($path) )
            unlink($path);
        Noticia::destroy($id);
        return response()->json(['success'=>true]);
    }
}
