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
use OneSignal;

class NoticiasController extends Controller
{
    public function all(Request $request)
    {
        $noticias = Noticia::all();
        return view('noticias/noticias', ['noticias' => $noticias]);
    }

    public function add(Request $request)
    {
        $input = $request->only('image','title','content');
        $validator = Validator::make($input, [
            'image' => 'required|image',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        if (!$request->file('image')->isValid()) {
            return back()->withErrors(['image'=>['error with image file.']])->withInput();
        }

        $input['image'] = Storage::put('images', $input['image']);
        $input['image'] = asset($input['image']);


        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $input['idUser'] = 1;
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

        return back()->with('message', 'Noticia Creada!');
    }

    public function edit(Request $request,$id)
    {
        $noticia = Noticia::find($id);
        if(!$noticia)
            back()->withErrors(['invalid'=>['El id de Noticia seleccionado no es valido.']]);

        $input = $request->only('image','title','content');

        $validator = Validator::make($input, [
            'image' => 'required|image',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }
        if (!$request->file('image')->isValid()) {
            return response()->json(["error"=>"error with image file."],400);
        }

        $auxPath = explode("images",$noticia->image);

        if(Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);

        $input['image'] = Storage::put('images', $input['image']);

        $noticia->image = asset($input['image']);

        $noticia->title = $input['title'];
        $noticia->content = $input['content'];

        $noticia->save();

        return back()->with('message', 'Noticia Editada!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:noticias,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $noticia = Noticia::find($input['id']);

        if(isset($noticia->image)){
            $auxPath = explode("images",$noticia->image);

            if(Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);
        }
        $noticia->delete();

        return redirect("noticias")->with('message', 'Noticia Eliminada!');

    }

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
