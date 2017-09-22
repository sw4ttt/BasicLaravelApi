<?php

namespace App\Http\Controllers\Api\Material;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Hash;
use JWTAuth;
use phpDocumentor\Reflection\Types\This;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use App\Materia;
use App\Material;

//$table->string('idMateria');
//$table->string('titulo');
//$table->string('descripcion');
//$table->string('size');
//$table->string('file');
class MaterialController extends Controller
{
    //
    public function all(Request $request)
    {
        return Material::all();
    }
    public function add(Request $request)
    {
        $input = $request->only('idMateria','titulo','descripcion','size','file');
        $validator = Validator::make($input, [
            'idMateria' => 'required|numeric|exists:materias,id',
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'file' => 'required|file'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if (!$request->file('file')->isValid()) {
            return response()->json(["error"=>"error with file."],400);
        }
        $input['file'] = Storage::put('files', $input['file']);
        $input['size'] = Storage::size($input['file']);
        $input['size'] = $this->formatSizeUnits($input['size']);
        $input['file'] = asset($input['file']);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        Material::create($input);
        return response()->json(['success'=>true,'file'=>$input['file']]);
    }
    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materials,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return Material::find($id);
    }
    public function edit(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id','titulo','descripcion');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materials,id',
            'titulo' => 'required|string',
            'descripcion' => 'required|string'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $material = Material::find($input['id']);

        $material->titulo = $input['titulo'];
        $material->descripcion = $input['descripcion'];
        $material->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        $material->save();

        return response()->json(['success'=>true],201);
    }

    public function delete(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:materials,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $material = Material::find($id);
        $material->file = str_replace(url('/'),'',$material->file,$i);
        $path = realpath('./../public/').$material->file;
        $path = str_replace('\\', '/', $path);


        if( file_exists($path) )
            unlink($path);

        Material::destroy($id);
        return response()->json(['success'=>true]);
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
