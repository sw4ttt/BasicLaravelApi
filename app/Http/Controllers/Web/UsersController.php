<?php

namespace App\Http\Controllers\Web;

use App\Tarjeta;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\Noticia;
use App\Calificacion;
use App\Estudiante;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NotificacionGeneral;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Materia;
use App\Curso;
use OneSignal;
use App\Mail\OlvidoPassword;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
/**
 * Class UsersController
 * @package App\Http\Controllers\Web
 */
class UsersController extends Controller
{
    public function all(Request $request)
    {
        $users = User::all();

//        Mail::to('oscar.marquez.to@gmail.com')->send(new OlvidoPassword);

//        OneSignal::sendNotificationToAll("Some Message", $url = 'do_not_open', $data = ["dataTest"=>'test value'], $buttons = null, $schedule = null);

        return view('users/users', ['users' => $users]);
    }

    public function add(Request $request)
    {
        $UserTypes = array("ADMIN","PROFESOR","REPRESENTANTE");

        $input = $request->only(
            'tipoIdPersonal',
            'idPersonal',
            'tlfDomicilio',
            'tlfCelular',
            'direccion',
            'nombre',
            'email',
            'image',
            'password',
            'password_confirmation',
            'type');
        $validator = Validator::make($input, [
            'tipoIdPersonal' => 'required|string',
            'idPersonal' => 'required|numeric',
            'nombre' => 'required|string',
            'tlfDomicilio' => 'required|string',
            'tlfCelular' => 'required|string',
            'direccion' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'image' => 'required|image',
            'password' => 'required|min:4|confirmed',
            'type' => 'required|string|in:ADMIN,PROFESOR,REPRESENTANTE'
        ]);

//        if(array_search(strtoupper($input['type']),$UserTypes) == false)
//            return back()->withErrors(["type"=>"Wrong User Type.","valid types"=>$UserTypes])->withInput();
//
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }


        $userType = strtoupper($input['type']);


        if($userType === 'REPRESENTANTE')
        {
            $input['nombreEstudiante'] = $request['nombreEstudiante'];
            $input['idPersonalEstudiante'] = $request['idPersonalEstudiante'];
            $input['curso'] = $request['curso'];

            $validator = Validator::make($input, [
                'nombreEstudiante' => 'required|string',
                'idPersonalEstudiante' => 'required|numeric',
                'curso' => 'required|numeric|exists:cursos,id'
            ]);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $curso = Curso::find($input['curso']);

            if($curso->cupos <= 0)
                return back()->withErrors([
                    'curso'=>['No hay cupos disponibles para el curso indicado.']
                ])->withInput();

            $curso->decrement('cupos');
            $curso->save();
        }

        $input['email'] = strtolower($input['email']);
        $input['image'] = Storage::put('images', $request->image);
        $input['image'] = str_replace('public','storage',$input['image'],$i);
        $input['image'] = url('/')."/".$input['image'];
        $input['type'] = strtoupper($input['type']);
        $input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        $user = User::create($input);

        if($userType === 'REPRESENTANTE')
        {
            $input['idUser'] = $user->id;
            $input['idPersonal'] = $input['idPersonalEstudiante'];
            $input['nombre'] = strtoupper($input['nombreEstudiante']);
            $input['seccion'] = $curso->seccion;
            $input['grado'] = $curso->grado;

            $estudiante = Estudiante::create($input);

            $materias = Materia::where('grado',$curso->grado)->where('seccion',$curso->seccion)->get();
            foreach ($materias as $materia) {
                $profesor = $materia->profesores()->get()->first();
                if(!is_null($profesor))
                {
                    Calificacion::create([
                        'idProfesor'=>$profesor->id,
                        'idEstudiante'=>$estudiante->id,
                        'idMateria'=>$materia->id,
                        'periodo'=>'2017-2018',
                        'evaluaciones'=>[],
                        'acumulado'=>0,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        return redirect("users/add")->with('message', 'Usuario Creado!');
    }

    public function edit(Request $request,$id)
    {
        $input = $request->only(
            'nombre',
            'email',
            'tipoIdPersonal',
            'idPersonal',
            'tlfDomicilio',
            'tlfCelular',
            'direccion',
            'password',
            'password_confirmation'
        );
        $input['id'] = $id;

        $validator = Validator::make($input, [
            'email' => 'required|string|email',
            'nombre' => 'required|string',
            'tipoIdPersonal' => 'required|string',
            'idPersonal' => 'required|numeric',
            'tlfDomicilio' => 'required|string',
            'tlfCelular' => 'required|string',
            'direccion' => 'required|string',
            'id' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $usuario = User::find($id);

        if($usuario->email !== $input['email']){
            $validator = Validator::make($input, [
                'email' => 'required|string|unique:users,email',
            ]);
            if ($validator->fails())
                return back()->withErrors($validator)->withInput();
        }

        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

        if(isset($input['password']) && $input['password'] !== ''){
            $validator = Validator::make($input, [
                'password' => 'required|min:4|confirmed',
            ]);
            if ($validator->fails())
                return back()->withErrors($validator)->withInput();
            $usuario->password = Hash::make($input['password']);
        }

        $usuario->nombre = $input['nombre'];
        $usuario->email = $input['email'];
        $usuario->tipoIdPersonal = $input['tipoIdPersonal'];
        $usuario->idPersonal = $input['idPersonal'];
        $usuario->tlfDomicilio = $input['tlfDomicilio'];
        $usuario->tlfCelular = $input['tlfCelular'];
        $usuario->direccion = $input['direccion'];
        $usuario->updated_at = $input['updated_at'];

        $usuario->save();

        return redirect("users/edit/".$id)->with('message', 'Usuario Editado!');
    }

    public function delete(Request $request,$id)
    {
        $input['id'] = $id;
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $usuario = User::find($input['id']);

        if($usuario->type === "PROFESOR"){
            $materias = $usuario->materias()->get();
            if(count($materias)>0){
                return back()->withErrors(['nombre'=>['El Usuario es Profesor y tiene materias asignadas. Asigne las materias a otro profesor o elimine la materia para no perder la informacion asociada..']])->withInput();
            }
        }


        if(isset($usuario->image)){
            $auxPath = explode("images",$usuario->image);
            if($auxPath[1] !== 'default-profile.jpg' && Storage::exists('images'.$auxPath[1]))  Storage::delete('images'.$auxPath[1]);
        }

        $tarjetas = $usuario->tarjetas();
        if(is_array($tarjetas) && count($tarjetas) > 0)
            $usuario->tarjetas()->detach();

        Tarjeta::where("idUsuario",$usuario->id)->delete();

        $carrito = $usuario->carrito();
        if(is_array($carrito) && count($carrito) > 0)
            $usuario->carrito()->detach();

        $estudiante = $usuario->estudiantes()->first();

        if(!is_null($estudiante)){
            Calificacion::where("idEstudiante",$estudiante->id)->delete();
            $estudiante->delete();
        }

        $materias = $usuario->materias()->get();

        $usuario->materias()->detach();

        if(is_array($materias) && count($materias) > 0){
            $usuario->materias()->detach();
            Calificacion::where("idProfesor",$usuario->id)->delete();
        }

        foreach ($materias as $materia){
            $materia->delete();
        }

        $usuario->delete();

        return redirect("users")->with('message', 'Usuario Eliminado!');
    }

    public function addBulk(Request $request)
    {
        $input = $request->only('usersFile');

        $validator = Validator::make($input, [
            'usersFile' => 'required|file'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }

        $results = [];
        $rejected = [];
        $filtered = [];
        $valid = true;

        Excel::load($input['usersFile'], function($reader) use (&$results,&$filtered,&$valid) {
            $results = $reader->takeRows(200)->get();

            if($results->count()===0){
                $valid = false;
            }
            else
                foreach ($results as $row){
                    $check = array_has($row, [
                        'tipoidpersonal',
                        'idpersonal',
                        'tlfdomicilio',
                        'tlfcelular',
                        'direccion',
                        'nombre',
                        'email',
                        'type',
                        'nombreestudiante',
                        'idpersonalestudiante',
                        'grado',
                        'seccion'
                    ]);
                    if(!$check)
                        $valid = false;
                }
        });

        if(!$valid)
            return back()->withErrors(['usersFile'=>['El archivo contiene un estructura invalida. utiliza el archivo de ejemplo.']])->withInput();

        $index = 1;
        foreach ($results as $row){

            $rowValidation = $row->toArray();

            $rowValidation['password'] = 123456;
            $rowValidation['image'] = url('/')."/images/default-profile.jpg";

            $rowValidation['tipoidpersonal'] = strval($rowValidation['tipoidpersonal']);
            $rowValidation['idpersonal'] = strval($rowValidation['idpersonal']);

//            $rowValidation['nombre'] = utf8_encode($rowValidation['nombre']);
//            echo "Palabra=".$rowValidation['nombre']."\n";

//            $rowValidation['nombre'] = $this->removeAccents($rowValidation['nombre']);

//            echo "Palabra-Out=".$rowValidation['nombre']."\n";

//            $rowValidation['nombre'] = preg_replace('/[^A-Za-z0-9\-]/', 'X', $rowValidation['nombre']);

            $rowValidation['tlfdomicilio'] = "+57".strval($rowValidation['tlfdomicilio']);
            $rowValidation['tlfcelular'] = "+57".strval($rowValidation['tlfcelular']);
            $rowValidation['direccion'] = strval($rowValidation['direccion']);
            $rowValidation['email'] = strval($rowValidation['email']);
            $rowValidation['password'] = strval($rowValidation['password']);
            $rowValidation['type'] = strval($rowValidation['type']);

            $rowValidation['nombreestudiante'] = strval($rowValidation['nombreestudiante']);
            $rowValidation['idpersonalestudiante'] = strval($rowValidation['idpersonalestudiante']);
            $rowValidation['grado'] = strval($rowValidation['grado']);
            $rowValidation['seccion'] = strval($rowValidation['seccion']);

            foreach ($rowValidation as $key => $value){
                if($key === "email" && $value === "" && $rowValidation['nombre'] !== ""){

                    $number = rand(1, 999);

//                    $tempName = strtolower($rowValidation['nombre']);
//                    $tempName = str_replace('ñ', 'n', $tempName);

//                    $auxName = explode(" ", $this->removeAccents($rowValidation['nombre']));
                    $auxName = str_word_count($this->removeAccents($rowValidation['nombre']), 2);

//                    foreach ($auxName as $stuff){
//                        echo "item=".$stuff."\n";
//                    }

                    $validName = true;

                    foreach ($auxName as $auxNameKey => $auxNameValue){

                        $itemToValidate = ['nombre'=>$auxNameValue];

                        $validator = Validator::make($itemToValidate, [
//                            'nombre' => 'required|string|regex:/^[a-zA-Z]{1,40}$/'
                            'nombre' => 'required|string'
                        ]);

                        if($validator->fails()){
                            $validName = false;
                        }
                    }

                    if($validName === true){
                        $lastName = end($auxName);
                        $sureName = reset($auxName);
                        if($lastName && $sureName){
                            $rowValidation[$key] = $lastName.$sureName.$number."@tucede.com";
//                            echo $rowValidation[$key]."\n";
                            $rowValidation[$key] = mb_strtolower($rowValidation[$key],'UTF-8');
//                            echo $rowValidation[$key]."\n";
//                            echo "\n";
//                            $rowValidation[$key] = preg_replace("/[^A-Za-z]/", 'n', $rowValidation[$key]);
//                            $auxName = str_replace(' ', '_', $rowValidation['nombre']);
//                            $rowValidation[$key] = mb_convert_encoding($rowValidation[$key], "UTF-8");
                        }
                    }
//                    echo "NAME=".$rowValidation['nombre']."\n";
//                    $auxName = str_replace(' ', '_', $rowValidation['nombre']);
//                    $auxName = preg_replace('/[^A-Za-z0-9\-]/', '', $auxName);
////                    $auxName = utf8_encode($rowValidation['nombre']);
////                    $auxName = mb_convert_encoding($auxName, "UTF-8");
////                    echo "AUX_NAME=".$auxName."\n";
//                    $rowValidation[$key] = substr($auxName, 0,8).$number."@tucede.com";
                }

                if($key !== "type" && $key !== "grado" && $key !== "seccion" && $key !== "email" && ($value === "" || $value === "+57")){

                    switch ($key) {
                        case "tipoidpersonal":{
                            $rowValidation[$key] = "Numero Identificacion";
                        }
                            break;
                        case "idpersonal":{
                            $rowValidation[$key] = "123456789";
                        }
                            break;
                        case "tlfdomicilio":{
                            $rowValidation[$key] = "+57312345678";

                        }
                            break;
                        case "tlfcelular":{
                            $rowValidation[$key] = "+57312345678";
                        }
                            break;
                        case "direccion":{
                            $rowValidation[$key] = "Colombia";
                        }
                            break;
                    }
                }

                if(isset($rowValidation['type']) && $rowValidation['type'] === 'REPRESENTANTE' ){
                    if(isset($rowValidation['idpersonalestudiante']) &&
                        $rowValidation['idpersonalestudiante'] === '' ){
                        $rowValidation['idpersonalestudiante'] = "123456789";
                    }
                }

            }

            $validator = Validator::make($rowValidation, [
                'tipoidpersonal' => 'required|string',
                'idpersonal' => 'required|numeric',
                'nombre' => 'required|string',
                'tlfdomicilio' => 'required|string|regex:/^((\+57)(\d){6,10})$/',
                'tlfcelular' => 'required|string|regex:/^((\+57)(\d){6,10})$/',
                'direccion' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'type' => 'required|string|in:ADMIN,PROFESOR,REPRESENTANTE'
            ]);

            if(!$validator->fails()) {
                if(strtoupper($rowValidation['type']) === 'REPRESENTANTE'){
                    $validator = Validator::make($rowValidation, [
                        'nombreestudiante' => 'required|string',
                        'idpersonalestudiante' => 'required|numeric',
                        'grado' => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15',
                        'seccion' => 'required|string'
                    ]);
                    if(!$validator->fails()){
                        $curso = Curso::where("grado",strtoupper($rowValidation['grado']))->where("seccion",strtoupper($rowValidation['seccion']))->get()->first();

                        if(!is_null($curso) && $curso->cupos > 0){
//                            $curso->decrement('cupos');
//                            $curso->save();
                            array_push($filtered,$rowValidation);
                        }
                        else{
                            $rowValidation['index'] = $index;
                            if(!isset($rowValidation['errors']))
                                $rowValidation['errors'] = [];
                            $rowValidation['errors'] = array_merge($rowValidation['errors'], ['curso']);
                            array_push($rejected,$rowValidation);
                        }
                    }
                    else {
                        $rowValidation['index'] = $index;
                        if(!isset($rowValidation['errors']))
                            $rowValidation['errors'] = [];
                        $rowValidation['errors'] = array_merge($rowValidation['errors'], $validator->errors()->keys());
                        array_push($rejected,$rowValidation);
                    }
                }
                else{
                    array_push($filtered,$rowValidation);
                }
            }
            else{
                $rowValidation['index'] = $index;
                if(!isset($rowValidation['errors']))
                    $rowValidation['errors'] = [];
                $rowValidation['errors'] = array_merge($rowValidation['errors'], $validator->errors()->keys());
                array_push($rejected,$rowValidation);
            }
            $index++;
        }

//        return response()->json(['_BIEN' => "BIEN",'filtered' => $filtered,'rejected' => $rejected],400);

        if(count($filtered)===0){
            return back()->withErrors(['usersFile'=>['El archivo contiene un estructura invalida. utiliza el archivo de ejemplo.']])->with('rejected',$rejected);
        }

        foreach ($filtered as $account){

            $newAccount = [];

            $newAccount['tipoIdPersonal'] = $account['tipoidpersonal'] ;
            $newAccount['idPersonal'] = $account['idpersonal'] ;
            $newAccount['nombre'] = $account['nombre'] ;
            $newAccount['tlfDomicilio'] = $account['tlfdomicilio'] ;
            $newAccount['tlfCelular'] = $account['tlfcelular'] ;
            $newAccount['direccion'] = $account['direccion'] ;
            $newAccount['email'] = $account['email'] ;
            $newAccount['password'] = Hash::make($account['password']);
            $newAccount['image'] = $account['image'] ;
            $newAccount['type'] = $account['type'] ;

            $user = User::create($newAccount);

            if($user->type === 'REPRESENTANTE')
            {
                $auxEstudiante = [];
                $auxEstudiante['idUser'] = $user->id;
                $auxEstudiante['idPersonal'] = $account['idpersonalestudiante'];
                $auxEstudiante['nombre'] = $account['nombreestudiante'];
                $auxEstudiante['grado'] = $account['grado'];
                $auxEstudiante['seccion'] = $account['seccion'];

                $estudiante = Estudiante::create($auxEstudiante);

                $materias = Materia::where('grado',$account['grado'])->where('seccion',$account['seccion'])->get();
                foreach ($materias as $materia) {
                    $profesor = $materia->profesores()->get()->first();
                    if(!is_null($profesor))
                    {
                        Calificacion::create([
                            'idProfesor'=>$profesor->id,
                            'idEstudiante'=>$estudiante->id,
                            'idMateria'=>$materia->id,
                            'periodo'=>'2017-2018',
                            'evaluaciones'=>[],
                            'acumulado'=>0,
                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

        }
        return redirect("users/add/masivo")->with(['message'=>'Usuarios Agregados!','rejected'=>$rejected,'accepted'=>$filtered]);
    }

    private function removeAccents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        return str_replace($a, $b, $str);
    }

}
