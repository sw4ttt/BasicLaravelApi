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
use Epayco\Epayco;
use Illuminate\Support\Facades\Log;
use OneSignal;
use App\Mensaje;

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
        
        $pagables = Articulo::where('categoria','MATRICULA')->where('estado','HABILITADO')->orderBy('updated_at', 'desc')->get();
        
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
        $orders = Order::where('idUsuario', $user->id)->orderBy('updated_at', 'desc')->get();
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
            'articulos',
            'descripcion',
            'tipo',
            'recibo',
            'ref_payco',
            'documento',
            'factura',
            'transactionID',
            'ticketId',
            'pin',
            'codigoproyecto',
            'estado',
            'valor',
            'nombre',
            'apellido',
            'email'
        );

        $validator = Validator::make($input, [
            'articulos'=> 'required_if:tipo,CARRITO|array|nullable',
            'tipo'=> 'required|string|in:CARRITO,MATRICULA',
            'recibo'=> 'string',
            'descripcion'=> 'required_if:tipo,MATRICULA|string|nullable',
            'pin'=> 'string|nullable',
            'codigoproyecto'=> 'string|nullable',
            'ref_payco'=> 'string',
            'documento'=> 'required|string',
            'factura'=> 'string',
            'transactionID'=> 'string',
            'ticketId'=> 'string',
            'estado'=> 'required|string',
            'valor'=> 'required|string',
            'nombre'=> 'required|string',
            'apellido'=> 'required|string',
            'email'=> 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        if($input['tipo']==='MATRICULA')
            $input['articulos']=[];
        if($input['tipo']==='CARRITO')
            $input['descripcion']="";
            

        $input['idUsuario']= $user->id;
        
        $input['factura'] = Carbon::now()->format('His').str_random(10);

        $order = Order::create($input);

        return response()->json(['success'=>true,'order'=>$order]);

    }

    public function edit(Request $request)
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

        $input['idUsuario']= $user->id;

        $input = $request->only(
            'id',
            'recibo',
            'ref_payco',
            'transactionID',
            'ticketId',
            'pin',
            'codigoproyecto',
            'estado'
        );

        $validator = Validator::make($input, [
            'id'=>'required|numeric|exists:orders,id',
            'recibo'=> 'required|string',
            'ref_payco'=> 'required|string',
            'transactionID'=> 'string|nullable',
            'ticketId'=> 'string|nullable',
            'pin'=> 'string|nullable',
            'codigoproyecto'=> 'string|nullable',
            'estado'=> 'required|string'
        ]);

        if($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return response()->json($validator->errors(),400);
        }

        $input['estado'] = strtoupper($input['estado']);

        if(($input['estado'] === 'FALLIDA') || ($input['estado'] ==='RECHAZADA'))
        {
            $order = Order::find($input['id']);
            $order->delete();
            return response()->json(['success'=>true,'response'=>'orden cancelada']);
        }

        $order = Order::find($input['id']);

        $order->recibo=$input['recibo'];
        $order->ref_payco=$input['ref_payco'];
        $order->transactionID=$input['transactionID'];
        $order->ticketId=$input['ticketId'];
        $order->pin=$input['pin'];
        $order->codigoproyecto=$input['codigoproyecto'];
        $order->estado=$input['estado'];

        $order->save();

        return response()->json(['success'=>true,'order'=>$order]);

    }
    public function epaycoTestPSEESTADO(Request $request)
    {

        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));

        $pse = $epayco->bank->get("1347533");


        return response()->json(['message'=>'ESTADO PSE','pse'=>$pse]);
    }
    public function epaycoTestCash(Request $request)
    {

        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));

        $cash = $epayco->cash->create("efecty", array(
            "invoice" => "1472050779",
            "description" => "pay test",
            "value" => "25000",
            "tax" => "0",
            "tax_base" => "0",
            "currency" => "COP",
            "type_person" => "0",
            "doc_type" => "CC",
            "doc_number" => "10358514",
            "name" => "testing",
            "last_name" => "PAYCO",
            "email" => "test@mailinator.com",
            "cell_phone" => "11111111111",
            "end_date" => "2017-10-15",
            "url_response" => "https://lacasacreativaapp.com/api/pagos/respuesta",
            "url_confirmation" => "https://lacasacreativaapp.com/api/pagos/confirmacion",
            "method_confirmation" => "GET",
        ));


        return response()->json(['cash'=>$cash]);
    }
    public function epaycoTestPSE(Request $request)
    {

        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));

        $pse = $epayco->bank->create(array(
            "bank" => "1022",
            "invoice" => "1472050778",
            "description" => "Pago pruebas",
            "value" => "10000",
            "tax" => "0",
            "tax_base" => "0",
            "currency" => "COP",
            "type_person" => "0",
            "doc_type" => "CC",
            "doc_number" => "10358519",
            "name" => "PRUEBAS",
            "last_name" => "PAYCO",
            "email" => "no-responder@payco.co",
            "country" => "CO",
            "cell_phone" => "3010000001",
            "url_response" => "https://lacasacreativaapp.com/api/pagos/respuesta",
            "url_confirmation" => "https://lacasacreativaapp.com/api/pagos/confirmacion",
            "method_confirmation" => "GET",
        ));

//        pagos/respuesta/factura/{factura}/transaccion/{transactionID}

        return response()->json(['pse'=>$pse]);
    }

    public function epaycoTest(Request $request)
    {

        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));


//        $token = $epayco->token->create(array(
//            "card[number]" => '4575623182290326',
//            "card[exp_year]" => "2017",
//            "card[exp_month]" => "07",
//            "card[cvc]" => "123"
//        ));
//
//        $customer = $epayco->customer->create(array(
//            "token_card" => $token->id,
//            "name" => "Joe Doe",
//            "email" => "joe@payco.co",
//            "phone" => "3005234321",
//            "default" => true
//        ));
//
//
//        $logData = json_encode( get_object_vars($token));
//        Log::info('TOKEN: RESPONSE= '.$logData);
//
//        $logData = json_encode( get_object_vars($customer));
//        Log::info('CUSTOMER: RESPONSE= '.$logData);
//


        //TOKEN MGpZhGeD44X4FLLvF
        //CUSTOMER SWwsdFbKynWb9wBno

        $p_cust_id_cliente = "16086";

        // 6d8f198c0a0e399b18ba0dfaa8f557faa349ebc0fabad2f12bbf5832b7fbb0cf
        $p_key = "032e97935cd5ac5dcc28809d04ee4c43";

        $x_ref_payco = "418519";
        $x_transaction_id = "418519";

        $x_amount = "116000";
        $x_currency_code = "COP";


        $signature=hash('sha256',
            $p_cust_id_cliente.'^'
            .$p_key.'^'
            .$x_ref_payco.'^'
            .$x_transaction_id.'^'
            .$x_amount.'^'
            .$x_currency_code
        );

        $queryString = "x_amount=116000&x_amount_base=100000&x_amount_country=116000&x_amount_ok=116000&x_approval_code=000000&x_bank_name=Banco%20de%20Pruebas&x_business=PAOLA%20ROMERO&x_cardnumber=457562%2A%2A%2A%2A%2A%2A%2A0326&x_cod_response=1&x_cod_respuesta=1&x_currency_code=COP&x_cust_id_cliente=16086&x_customer_address=NA&x_customer_city=NA&x_customer_country=CO&x_customer_doctype=CC&x_customer_document=1035851980&x_customer_email=example%40email.com&x_customer_ip=169.254.74.36&x_customer_lastname=Doe&x_customer_name=John&x_customer_phone=0000000&x_description=Test%20Payment&x_errorcode=00&x_extra1=&x_extra2=&x_extra3=&x_fecha_transaccion=2017-10-03%2017%3A42%3A31&x_franchise=VS&x_id_factura=OR-1234&x_id_invoice=OR-1234&x_quotas=12&x_ref_payco=418519&x_response=Aceptada&x_response_reason_text=00-Aprobada&x_respuesta=Aceptada&x_signature=6d8f198c0a0e399b18ba0dfaa8f557faa349ebc0fabad2f12bbf5832b7fbb0cf&x_tax=16000&x_test_request=TRUE&x_transaction_date=2017-10-03%2017%3A42%3A31&x_transaction_id=418519";

        $array = null;
        parse_str($queryString, $array);

        return response()->json(['array'=>$array]);
//        if($signature === "6d8f198c0a0e399b18ba0dfaa8f557faa349ebc0fabad2f12bbf5832b7fbb0cf")
//            return response()->json(['signature'=>'IGUAL']);
//        else
//            return response()->json(
//                [
//                    'signature'=>'DIFERENTE',
//                    'A'=>$signature,
//                    'B'=>"6d8f198c0a0e399b18ba0dfaa8f557faa349ebc0fabad2f12bbf5832b7fbb0cf"
//                ]
//            );
//        $pay = $epayco->charge->create(array(
//            "token_card" => "MGpZhGeD44X4FLLvF",
//            "customer_id" => "SWwsdFbKynWb9wBno",
//            "doc_type" => "CC",
//            "doc_number" => "1035851980",
//            "name" => "John",
//            "last_name" => "Doe",
//            "email" => "example@email.com",
//            "bill" => "OR-1234",
//            "description" => "Test Payment",
//            "value" => "116000",
//            "tax" => "16000",
//            "tax_base" => "100000",
//            "currency" => "COP",
//            "dues" => "12",
//            "url_confirmation"=> "https://lacasacreativaapp.com/api/pagos/confirmacion"
//        ));
//        return response()->json(['pay'=>$pay]);

//        {
//            "pay": {
//            "success": true,
//        "title_response": "payment",
//        "text_response": "detalle de la transacción",
//        "last_action": "insert payment",
//        "data": {
//                "ref_payco": 418519,
//            "factura": "OR-1234",
//            "descripcion": "Test Payment",
//            "valor": "116000",
//            "iva": "16000",
//            "baseiva": 100000,
//            "moneda": "COP",
//            "banco": "Banco de Pruebas",
//            "estado": "Aceptada",
//            "respuesta": "Aprobada",
//            "autorizacion": "000000",
//            "recibo": 418519,
//            "fecha": "2017-10-03 17:42:31",
//            "cod_respuesta": 1,
//            "ip": "169.254.74.36",
//            "tipo_doc": "CC",
//            "documento": "1035851980",
//            "nombres": "John",
//            "apellidos": "Doe",
//            "email": "example@email.com",
//            "enpruebas": 1
//        }
//    }
//}

    }

    public function confirmacion(Request $request)
    {
        $queryString = $request->getQueryString();
//        Log::info('CONFIRMACION:');
        Log::info('CONFIRMACION: getQueryString= '.$request->getQueryString());

        $data = null;
        parse_str($queryString, $data);

        $cool = false;

        if(!is_null($data)){

            if(!isset($data['x_cust_id_cliente']) || !isset($data['x_id_invoice']))
                return response()->json(['success'=>false,'message'=>'error en data'],400);

            if((env('EPAYCO_CUSTOMER_ID', '16092')===$data['x_cust_id_cliente']))
                $cool = true;

            $order = Order::where("ref_payco",$data['x_ref_payco'])->where("factura",$data['x_id_invoice'])->first();

            if(is_null($order))
                return response()->json(['success'=>false,'message'=>'no existe orden con esa info.'],400);

            switch (intval($data['x_cod_response'])){
                case 1:{
                    $order->estado = "APROBADO";
                    $this->notificarEstadoOrder($order->idUsuario,"Su pago ha sido APROBADO, para la orden:".$order->factura);
                    $order->save();
                }
                break;
                case 2:{
                    $order->estado = "RECHAZADO";
                    $this->notificarEstadoOrder($order->idUsuario,"Su pago ha sido RECHAZADO, para la orden:".$order->factura);
                    $order->delete();
                }
                    break;
                case 3:{
                    $order->estado = "PENDIENTE";
                    $this->notificarEstadoOrder($order->idUsuario,"Su pago se encuentra en estado PENDIENTE, para la orden:".$order->factura);
                    $order->save();
                }
                    break;
                case 4:{
                    $order->estado = "FALLIDO";
                    $this->notificarEstadoOrder($order->idUsuario,"Su pago fue FALLIDO, para la orden:".$order->factura);
                    $order->delete();
                }
                    break;
                default:{
                }
            }
        }
        else{
            return response()->json(['success'=>false]);
        }

//        $logData = json_encode( get_object_vars($request));
        Log::info('CONFIRMACION: getQueryString= '.$request->getQueryString());
        return response()->json(['success'=>$cool]);
    }

    private function notificarEstadoOrder($idUsuario,$mensaje){

        $tag = new \stdClass;
        $tag->key = 'userId';
        $tag->relation = "=";
        $tag->value = $idUsuario;

        $tags = array();

        array_push($tags,$tag);

//        'idEmisor',
//        'idReceptor',
//        'nombre',
//        'idMateria',
//        'materia',
//        'grado',
//        'asunto',
//        'mensaje'

        $input['idEmisor'] = 1;
        $input['idReceptor'] = $idUsuario;
        $input['nombre'] = "ADMIN";
        $input['idMateria'] = 0;
        $input['materia'] = "";
        $input['grado'] = 0;

        $input['asunto'] = 'ESTADO DE PAGO: '.$mensaje;
        $input['mensaje'] = $mensaje;

        $mensaje = Mensaje::create($input);

        OneSignal::sendNotificationUsingTags(
            $input['asunto'],
            $tags,
            $url = null,
            [
                "key"=>"NOTIFICACION",
                "id"=>$mensaje->id,
                "idMateria"=>$input['idMateria'],
                "materia"=>$input['materia'],
                "idEmisor"=>$input['idEmisor'],
                "idReceptor"=>$input['idReceptor'],
                "nombre"=>$input['nombre'],
                "asunto"=>$input['asunto'],
                "mensaje"=>$input['mensaje'],
                "created_at"=>$mensaje->created_at->format('Y-m-d H:i:s')
            ],
            $buttons = null,
            $schedule = null
        );

    }
    public function respuesta(Request $request,$factura,$transactionID)
    {
        $queryString = $request->getQueryString();

        $data = null;
        parse_str($queryString, $data);

        Log::info('RESPUESTA: getQueryString= '.$request->getQueryString());

        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));

        $pse = $epayco->bank->get($transactionID);

        return response()->json(['success'=>true,'pse'=>$pse]);
    }


    public function addOLD(Request $request)
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

}
