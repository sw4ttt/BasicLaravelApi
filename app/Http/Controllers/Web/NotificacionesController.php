<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Materia;
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

class NotificacionesController extends Controller
{
    public function add(Request $request)
    {
        $input = $request->only('asunto', 'mensaje','grupo','idGrupo');
        $validator = Validator::make($input, [
            'asunto' => 'required|string|max:50',
            'mensaje' => 'required|string|max:250',
            'grupo' => 'required|string|in:GRADO,TODOS',
            'idGrupo' => 'required_if:grupo,GRADO',
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());

            return back()->withErrors($validator)->withInput();
        }

//        return "SIN ERROR";

        if($input['grupo'] === 'TODOS') {
            OneSignal::sendNotificationToAll(
                "Notificacion: ".$input['asunto'],
                $url = null,
                [
                    "key"=>"NOTIFICACION",
                    "mensaje"=>$input['mensaje']
                ],
                $buttons = null,
                $schedule = null
            );
            return back()->with('message', 'Mensaje enviado!');
        }
        else {

            $tag = new \stdClass;
            $tag->key = "grado";
            $tag->relation = "=";
            $tag->value = $input['idGrupo'];

            $tags = array();
            array_push($tags,$tag);

            OneSignal::sendNotificationUsingTags(
                $input['asunto'],
                $tags,
                $url = null,
                [
                    "key"=>"NOTIFICACION",
                    "mensaje"=>$input['mensaje']
                ],
                $buttons = null,
                $schedule = null
            );
            return back()->with('message', 'Mensaje enviado!');
        }
    }
}
