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
use App\Articulo;
use PayUException;
use App\Order;
use App\Tarjeta;
use Epayco\Epayco;
use Illuminate\Support\Facades\Log;
use OneSignal;
use App\Mensaje;

class PagosController extends Controller
{
    public function all(Request $request)
    {
        $articulos = Articulo::all();
        return view('articulos/articulos', ['articulos' => $articulos]);
    }

    public function respuesta(Request $request,$factura,$transactionID)
    {
        $epayco = new Epayco(array(
            "apiKey" => "74d69a0e9f1cc5eee5d600bffa313fbc",
            "privateKey" => "032e97935cd5ac5dcc28809d04ee4c43",
            "lenguage" => "ES",
            "test" => true
        ));

        $pse = $epayco->bank->get("transactionID");

        return view('articulos/articulos', ['pse' => $pse]);
    }

}
