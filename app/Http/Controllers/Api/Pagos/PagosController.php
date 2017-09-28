<?php

namespace App\Http\Controllers\Api\Pagos;

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
use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Alexo\LaravelPayU\LaravelPayU;
use PayUException;
use App\Order;
use App\Tarjeta;
use App\Articulo;


class PagosController extends Controller
{
    public function all(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired'],401);
            }else{
                return response()->json(['error'=>'Token Missing'],400);
            }
        }
        
        $pagables = Articulo::where('categoria','MATRICULA')->where('estado','HABILITADO')->get();
        
        //string(6) "nombre"
        //string(8) "cantidad"
        //string(6) "estado"
        //string(6) "precio"
        //string(5) "image"
        //string(9) "categoria"
        //string(11) "descripcion"
        
        $pagables->transform(function ($item, $key) {
            $itemPagable = new \stdClass;
            $itemPagable->id = $item->id;
            $itemPagable->nombre = $item->nombre;
            $itemPagable->state = "PAGABLE";
            $itemPagable->value = $item->precio;
            $itemPagable->created_at = $item->created_at->format('Y-m-d H:i:s');
            return $itemPagable;
        });
        
        //return Order::where('user_id', $user->id)->where('state', 'APPROVED')->get();
        $orders = Order::where('user_id', $user->id)->where('state', 'APPROVED')->get();
        return response()->json(['orders'=>$orders,'pagables'=>$pagables]);

    }
    public function add(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['error'=>'Token is Invalid'],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['error'=>'Token is Expired'],401);
            }else{
                return response()->json(['error'=>'Token Missing'],400);
            }
        }

        $input = $request->only(
            'idTarjeta'
        );
        $validator = Validator::make($input, [
            'idTarjeta' => 'required|numeric|exists:tarjetas,id',
        ]);
        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $tarjeta = Tarjeta::find($input['idTarjeta']);

        if($tarjeta->idUsuario !== $user->id)
            return response()->json(['error'=>'La tarjeta no corresponde al usuario actual.'],400);

        $articulos = $user->carrito()->withPivot('cantidad')->get();

        if(count($articulos)===0)
            return response()->json(['key'=>'ERROR','message'=>'El carrito de compras esta vacio.'],400);

//        return $articulos;

        $totalPago = $this->calcularMonto($articulos);

        foreach ($articulos as $articulo) {
//            unset($articulo->cantidad);
//            unset($articulo->estado);
            unset($articulo->image);
            unset($articulo->created_at);
            unset($articulo->updated_at);
//            unset($articulo->pivot->cantidad);
        }

//        return response()->json(['articulos'=>$articulos,'total'=>$totalPago]);

        $pagable = true;
        foreach ($articulos as $articulo){
            if($articulo->cantidad < $articulo->pivot->cantidad)
                $pagable = false;
        }

        if(!$pagable)
            return response()->json(['success'=>false,'message'=>'No hay Inventario Suficiente para completar la orden.'],400);


        $order = Order::create([
            'user_id'=>$user->id,
            'user_name'=>$user->nombre,
            'reference'=>Carbon::now()->format('Y-m-d_H-i-s').str_random(10),
            'payu_order_id'=>'',
            'transaction_id'=>'',
            'state'=>'NEW',
            'value'=>$totalPago,
            'articulos'=>$articulos,
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s')
        ]);


        LaravelPayU::setPayUEnvironment();

        $data = [
            //Ingrese aquí el identificador de la cuenta.
            \PayUParameters::ACCOUNT_ID => "512321",
            //Ingrese aquí el código de referencia.
            \PayUParameters::REFERENCE_CODE => $order->reference,
            //Ingrese aquí la descripción.
            \PayUParameters::DESCRIPTION => "PAGO_CARRITO",

            \PayUParameters::SIGNATURE => "DEFAULT",

            // -- Valores --
            //Ingrese aquí el valor de la transacción.
            \PayUParameters::VALUE => $order->value,
            //Ingrese aquí el valor del IVA (Impuesto al Valor Agregado solo valido para Colombia) de la transacción,
            //si se envía el IVA nulo el sistema aplicará el 19% automáticamente. Puede contener dos dígitos decimales.
            //Ej: 19000.00. En caso de no tener IVA debe enviarse en 0.
            \PayUParameters::TAX_VALUE => "0",
            //Ingrese aquí el valor base sobre el cual se calcula el IVA (solo valido para Colombia).
            //En caso de que no tenga IVA debe enviarse en 0.
            \PayUParameters::TAX_RETURN_BASE => "0",
            //Ingrese aquí la moneda.
            \PayUParameters::CURRENCY => "COP",

            // -- Comprador
            //Ingrese aquí el nombre del comprador.
            \PayUParameters::BUYER_NAME => $user->nombre,
            //Ingrese aquí el email del comprador.
            \PayUParameters::BUYER_EMAIL => $user->email,
            //Ingrese aquí el teléfono de contacto del comprador.
            \PayUParameters::BUYER_CONTACT_PHONE => $user->tlfCelular,
            //Ingrese aquí el documento de contacto del comprador.
            \PayUParameters::BUYER_DNI => $user->idPersonal,
            //Ingrese aquí la dirección del comprador.
            \PayUParameters::BUYER_STREET => $user->direccion,
            \PayUParameters::BUYER_STREET_2 => "DEFAULT",
            \PayUParameters::BUYER_CITY => "DEFAULT",
            \PayUParameters::BUYER_STATE => "DEFAULT",
            \PayUParameters::BUYER_COUNTRY => "CO",
            \PayUParameters::BUYER_POSTAL_CODE => "DEFAULT",
            \PayUParameters::BUYER_PHONE => $user->tlfCelular,

            // -- pagador --
            //Ingrese aquí el nombre del pagador.
            \PayUParameters::PAYER_NAME => "APPROVED",
            //Ingrese aquí el email del pagador.
            \PayUParameters::PAYER_EMAIL => "payer_test@test.com",
            //Ingrese aquí el teléfono de contacto del pagador.
            \PayUParameters::PAYER_CONTACT_PHONE => "7563126",
            //Ingrese aquí el documento de contacto del pagador.
            \PayUParameters::PAYER_DNI => "5415668464654",
            //Ingrese aquí la dirección del pagador.
            \PayUParameters::PAYER_STREET => "calle 93",
            \PayUParameters::PAYER_STREET_2 => "125544",
            \PayUParameters::PAYER_CITY => "Bogota",
            \PayUParameters::PAYER_STATE => "Bogota",
            \PayUParameters::PAYER_COUNTRY => "CO",
            \PayUParameters::PAYER_POSTAL_CODE => "000000",
            \PayUParameters::PAYER_PHONE => "7563126",

            // -- Datos de la tarjeta de crédito --
            //Ingrese aquí el número de la tarjeta de crédito
            \PayUParameters::CREDIT_CARD_NUMBER => $tarjeta->numero,
            //Ingrese aquí la fecha de vencimiento de la tarjeta de crédito
            \PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $tarjeta->vencimiento,
            //Ingrese aquí el código de seguridad de la tarjeta de crédito
            \PayUParameters::CREDIT_CARD_SECURITY_CODE=> $tarjeta->cod,
            //Ingrese aquí el nombre de la tarjeta de crédito
            //VISA||MASTERCARD||AMEX||DINERS
            \PayUParameters::PAYMENT_METHOD => $tarjeta->tipo,

            //Ingrese aquí el número de cuotas.
            \PayUParameters::INSTALLMENTS_NUMBER => "1",
            //Ingrese aquí el nombre del pais.
            \PayUParameters::COUNTRY => "CO",

            //Session id del device.
            \PayUParameters::DEVICE_SESSION_ID => "vghs6tvkcle931686k1900o6e1",
            //IP del pagadador
            \PayUParameters::IP_ADDRESS => "127.0.0.1",
            //Cookie de la sesión actual.
            \PayUParameters::PAYER_COOKIE=>"pt1t38347bs6jc9ruv2ecpv7o2",
            //Cookie de la sesión actual.
            \PayUParameters::USER_AGENT=>"Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
        ];

        $promise = new Promise(function () use (&$promise,&$order,&$data,&$articulos,&$user) {

            $order->payWith($data, function($response, $order) use(&$promise,&$articulos,&$user){
                if ($response->code == 'SUCCESS') {

                    switch($response->transactionResponse->state)
                    {
                        case 'APPROVED';
//                            echo "SUCCESS - APPROVED";
                            $order->update([
                                'state' => "APPROVED",
                                'payu_order_id' => $response->transactionResponse->orderId,
                                'transaction_id' => $response->transactionResponse->transactionId
                            ]);
                            $user->carrito()->detach();
                            foreach ($articulos as $articulo){
                                $articulo->decrement('cantidad',$articulo->pivot->cantidad);
                            }
//                            $promise->resolve(get_object_vars($response));
                            $promise->resolve([
                                'key'=>'APPROVED',
                                'message'=>isset(
                                    $response->transactionResponse->responseMessage)?
                                    $response->transactionResponse->responseMessage:
                                    'APPROVED',
                                'code'=>isset(
                                    $response->transactionResponse->responseCode)?
                                    $response->transactionResponse->responseCode:
                                    'APPROVED',
                                'order'=>$order
                            ]);
                            break;
                        case 'DECLINED';
//                            echo "SUCCESS - DECLINED";
                            $order->update([
                                'state' => "DECLINED",
                                'payu_order_id' => $response->transactionResponse->orderId,
                                'transaction_id' => $response->transactionResponse->transactionId
                            ]);
//                            $promise->resolve(get_object_vars ( $response ));
                            $promise->resolve([
                                'key'=>'ERROR',
                                'message'=>$response->transactionResponse->responseMessage,
                                'code'=>$response->transactionResponse->responseCode,
                                'order'=>$order
                            ]);
                            break;
                        case 'PENDING';
//                            echo "SUCCESS - PENDING";
                            $order->update([
                                'state' => "PENDING",
                                'payu_order_id' => $response->transactionResponse->orderId,
                                'transaction_id' => $response->transactionResponse->transactionId
                            ]);
                            $promise->resolve([
                                'key'=>'ERROR',
                                'message'=>$response->transactionResponse->responseMessage,
                                'code'=>$response->transactionResponse->responseCode,
                                'order'=>$order
                            ]);
                            break;
                        case 'PENDING_TRANSACTION_CONFIRMATION';
//                            echo "SUCCESS - PENDING_TRANSACTION_CONFIRMATION";
                            $order->delete();
                            $promise->resolve([
                                'key'=>'UNKNOWN',
                                'message'=>'Respuesta desconocida del server.',
                                'code'=>'UNKNOWN_RESPONSE'
                            ]);
                            break;
                        default;
                            echo "SUCCESS - DEFAULT";
                            $order->delete();
                            $promise->resolve([
                                'key'=>'UNKNOWN',
                                'message'=>'Respuesta desconocida del server.',
                                'code'=>'UNKNOWN_RESPONSE'
                            ]);
                            break;
                    }
                    // ... El resto de acciones sobre la orden
                } else {
                    //... El código de respuesta no fue exitoso
//                    echo "... El código de respuesta no fue exitoso";
                    $order->delete();
//                    $promise->resolve(get_object_vars ( $response ));
                    $promise->resolve([
                        'key'=>'ERROR',
                        'message'=>
                            isset($response->transactionResponse->responseMessage)?
                                $response->transactionResponse->responseMessage:
                                "DEFAULT_ERROR_MESSAGE",
                        'code'=>isset($response->transactionResponse->responseCode)?
                            $response->transactionResponse->responseCode:
                            "DEFAULT_ERROR_CODE"
                    ]);
                }
            }, function($error) use(&$promise){
                // ... Manejo de errores PayUException, InvalidArgument
//                echo "... Manejo de errores PayUException, InvalidArgument";
//                if(is_array($error->message))
//                    echo "ES ARREGLO WEON";
                if(isset($error->payUCode))
                {
                    $promise->resolve(['key'=>$error->payUCode,'message'=>$error->message]);
                }
                else
                    $promise->resolve(['key'=>'ERROR','message'=>'ERROR_GENERAL']);
            });

        });
        $out = $promise->wait();
        return $out; // outputs "foo"

    }

    public function testPayuApi2(Request $request)
    {
        LaravelPayU::setPayUEnvironment();

        try {

            $promise = new Promise(function () use (&$promise,&$request) {

                LaravelPayU::doPing(function($response) use (&$promise) {
                    var_dump(($response)) ;
                    $promise->resolve($response->code);
                }, function($error) use(&$promise) {
                    echo ("WHAT\n".($error)."\nEND");
                    $promise->resolve($error);
                });

            });

            $out = $promise->wait();
//            $outObj = json_decode($out,true);
            return $out; // outputs "foo"
//            echo $response->getBody();
//            echo $response->getBody();
//            return ["error"=>false,"body"=>$response->getBody()];
        }
        catch(RequestException $e) {
            echo $e->getMessage();
            return ["error"=>true,"body"=>null,"key"=>$e->getMessage()];
        }


    }

    public function testPayuApi(Request $request)
    {
        $body = [
            "test"=> true,
            "language"=> "en",
            "command"=> "PING",
            "merchant"=> [
                "apiLogin"=> "pRRXKOl8ikMmt9u",
                "apiKey"=> "4Vj8eK4rloUd272L48hsrarnUA"
            ]

        ];
        $client = new Client(['timeout'  => 30.0]);

        try {
            $response = $client->post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',[
                'headers'=>[
                    'Content-Type'=>['application/json','charset=utf-8'],
                    'Accept'=>'application/json'
                ],
                'json'=>$body
            ]);
            return $response->getBody();
        }
        catch(RequestException $e) {
            return 'Message: ' .$e->getMessage();
        }
    }

    public function testPayuApiPago(Request $request)
    {


        try {

            $promise = new Promise(function () use (&$promise,&$request) {

                $client = new Client(['timeout'  => 30.0]);
                $response = $client->post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',[
                    'headers'=>[
                        'Content-Type'=>['application/json','charset=utf-8'],
                        'Accept'=>'application/json'
                    ],
                    'json'=>$request['body']
                ]);
                $promise->resolve($response->getBody()->getContents());
            });

            $out = $promise->wait();
            $outObj = json_decode($out,true);
            return $outObj; // outputs "foo"
//            echo $response->getBody();
//            echo $response->getBody();
//            return ["error"=>false,"body"=>$response->getBody()];
        }
        catch(RequestException $e) {
            echo $e->getMessage();
            return ["error"=>true,"body"=>null,"key"=>$e->getMessage()];
        }
//        return $out;
    }

    private function calcularMonto($arrayArticulos)
    {
        $total = 0;
        foreach ($arrayArticulos as $articulo) {
            $total = $total + ($articulo->precio * $articulo->pivot->cantidad);
        }

        return $total;
    }

    static function buildSignature($order,$merchantId, $key, $algorithm){

        $message = SignatureUtil::buildMessage($order, $merchantId, $key);

        if (SignatureUtil::MD5_ALGORITHM == $algorithm) {
            return md5($message);
        }else if (SignatureUtil::SHA_ALGORITHM == $algorithm) {
            return sha1($message);
        }else {
            throw new InvalidArgumentException("Could not create signature. Invalid algoritm");
        }


    }
}
