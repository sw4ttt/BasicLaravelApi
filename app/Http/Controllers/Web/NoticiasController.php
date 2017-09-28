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
        $input = $request->only('idMateria','titulo','descripcion');
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materials,id',
            'idMateria' => 'required|numeric|exists:materias,id',
            'titulo' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());

            return back()->withErrors($validator)->withInput();
        }


        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $material = Material::find($id);

        $material->idMateria = $input['idMateria'];
        $material->titulo = $input['titulo'];
        $material->descripcion = $input['descripcion'];
        $material->updated_at = $input['updated_at'];

        $material->save();

        return back()->with('message', 'Material Editado!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materials,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $material = Material::find($input['id']);

        $auxPath = explode("files",$material->file);

//        return $auxPath[1];

        if(!Storage::exists('files'.$auxPath[1]))  return response('Archivo no existe.',404);

        Storage::delete('files'.$auxPath[1]);

        $material->delete();

        return redirect("materiales")->with('message', 'Material Eliminado!');

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
