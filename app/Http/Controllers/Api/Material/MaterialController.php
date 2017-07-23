<?php

namespace App\Http\Controllers\Api\Material;

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
        $input['file'] = url('/')."/".$input['file'];
        $input['file'] = str_replace('public','storage',$input['file'],$i);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        Material::create($input);
        return response()->json(['success'=>true,'file'=>$input['file']]);
    }
}
