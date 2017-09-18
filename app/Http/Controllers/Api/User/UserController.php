<?php

namespace App\Http\Controllers\Api\User;

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
use App\Materia;

use GuzzleHttp\Client;

$UserTypes = array("ADMIN","PROFESOR","REPRESENTANTE");

class UserController extends Controller
{
    public function all(Request $request)
    {
        $users = User::all();
        return $users;
    }
    public function find(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);
        return $user;
    }
    public function estudiantes(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        return User::find($id)->estudiantes;
    }
    public function addEstudiantes(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id','idPersonal','nombre','grado');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id',
            'idPersonal' => 'required|numeric|unique:estudiantes,idPersonal',
            'nombre' => 'required|string',
            'grado' => 'required|numeric'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);

        if($user->type != 'REPRESENTANTE')
            return response()->json(['KEY'=>'WRONG_USERTYPE','MESSAGE'=>'Usuario no es tipo REPRESENTANTE'],400);
        $estudiante = new Estudiante([
            'idPersonal' => $input['idPersonal'],
            'nombre' => $input['nombre'],
            'grado' => $input['grado'],
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        return $user->estudiantes()->save($estudiante);
    }
    public function addMateria(Request $request, $idProfesor,$idMateria)
    {
        $request['idProfesor'] = $idProfesor;
        $request['idMateria'] = $idMateria;
        $input = $request->only('idProfesor','idMateria');
        $validator = Validator::make($input, [
            'idProfesor' => 'required|numeric|exists:users,id',
            'idMateria' => 'required|numeric|exists:materias,id'
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $profesor = User::find($idProfesor);

        if($profesor->type != 'PROFESOR')
            return response()->json(['KEY'=>'WRONG_USERTYPE','MESSAGE'=>'Usuario no es tipo PROFESOR'],400);

        $input['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $input['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');


        $profesor->materias()
            ->attach($input['idMateria'],
                [
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ]);
        return response()->json(['success'=>true]);
    }
    public function materias(Request $request, $id)
    {
        $request['id'] = $id;
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:users,id'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }
        $user = User::find($id);

        if ($user->type === 'PROFESOR')
            return $user->materias()->get();
        else
        {
            $estudiante = User::find($id)->estudiantes->first();
            if (is_null($estudiante))
                return [];
            return Materia::where('grado', $estudiante->grado)->get();
        }
    }


    public function testConsultaPayu(Request $request)
    {
        $client = new Client(['timeout'  => 10.0]);

        $body = [
                "test"=> false,
   "language"=> "en",
   "command"=> "PING",
   "merchant"=> [
       "apiLogin"=> "pRRXKOl8ikMmt9u",
      "apiKey"=> "4Vj8eK4rloUd272L48hsrarnUA"
   ]

        ];
        $response = $client->post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',[
            'headers'=>[
                'Content-Type'=>['application/json','charset=utf-8'],
                'Content-Length'=>'length',
                'Accept'=>'application/json'
            ],
            'json'=>$request['body']
        ]);
//        $response = $client->post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',[
//            'headers'=>[
//                'Content-Type'=>['application/json','charset=utf-8'],
//                'Content-Length'=>'length',
//                'Accept'=>'application/json'
//            ],
//            'json' => [
//                'test'=>false,
//                'language'=>'es',
//                'command'=>'SUBMIT_TRANSACTION',
//                'merchant'=>['apiKey'=>'4Vj8eK4rloUd272L48hsrarnUA','apiLogin'=>'pRRXKOl8ikMmt9u'],
//                'transaction'=>[
//                    'order'=>[
//                        'accountId'=>'512321',
//                        'referenceCode'=>'TestPayU',
//                        'description'=>'payment test',
//                        'language'=>'es',
//                        'signature'=>'7ee7cf808ce6a39b17481c54f2c57acc',
//                        'notifyUrl'=>'http://www.tes.com/confirmation',
//                        'additionalValues'=> [
//                            'TX_VALUE'=> [
//                                'value'=> 20000,
//                                'currency'=> 'COP'
//                            ],
//                            'TX_TAX'=> [
//                                'value'=> 3193,
//                                'currency'=> 'COP'
//                            ],
//                            'TX_TAX_RETURN_BASE'=> [
//                                'value'=> 16806,
//                                'currency'=> 'COP'
//                            ]
//                        ],
//                        'buyer'=>[
//                            'merchantBuyerId'=>'1',
//                            'fullName'=>'First name and second buyer  name',
//                            'emailAddress'=>'buyer_test@test.com',
//                            'contactPhone'=>'7563126',
//                            'dniNumber'=>'5415668464654',
//                            'shippingAddress'=>[
//                                'street1'=>'calle 100',
//                                'street2'=>'5555487',
//                                'city'=>'Medellin',
//                                'state'=>'Antioquia',
//                                'country'=>'CO',
//                                'postalCode'=>'000000',
//                                'phone'=>'7563126'
//                            ]
//                        ],
//                        'shippingAddress'=>[
//                            'street1'=>'calle 100',
//                            'street2'=>'5555487',
//                            'city'=>'Medellin',
//                            'state'=>'Antioquia',
//                            'country'=>'CO',
//                            'postalCode'=>'000000',
//                            'phone'=>'7563126'
//                        ]
//                    ],
//                    'payer'=>[
//                        'merchantBuyerId'=>'1',
//                        'fullName'=>'First name and second buyer  name',
//                        'emailAddress'=>'buyer_test@test.com',
//                        'contactPhone'=>'7563126',
//                        'dniNumber'=>'5415668464654',
//                        'billingAddress'=>[
//                            'street1'=>'calle 100',
//                            'street2'=>'5555487',
//                            'city'=>'Medellin',
//                            'state'=>'Antioquia',
//                            'country'=>'CO',
//                            'postalCode'=>'000000',
//                            'phone'=>'7563126'
//                        ]
//
//                    ],
//                    'extraParameters'=>[
//                        'INSTALLMENTS_NUMBER'=>1
//                    ],
//                    'creditCard'=>[
//                        'number'=>'4097440000000004',
//                        'securityCode'=>'321',
//                        'expirationDate'=>'2014/12',
//                        'name'=>'REJECTED',
//                    ],
//                    'type'=>'AUTHORIZATION_AND_CAPTURE',
//                    'paymentMethod'=>'VISA',
//                    'paymentCountry'=>'CO',
//                    'deviceSessionId'=>'vghs6tvkcle931686k1900o6e1',
//                    'ipAddress'=>'127.0.0.1',
//                    'cookie'=>'pt1t38347bs6jc9ruv2ecpv7o2',
//                    'userAgent'=>'Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0',
//                ]
//            ]
//        ]);

//        return response()->json([
//            'statusCode'=>$response->getStatusCode(),
//            'reason'=>$response->getReasonPhrase(),
//            'body'=>$response->getBody(),
//            'headers'=>$response->getHeaders()
//        ]);
        return $response->getBody();
    }
}
