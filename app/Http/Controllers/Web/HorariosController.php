<?php

namespace App\Http\Controllers\Web;

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
use OneSignal;
use App\Horario;

class HorariosController extends Controller
{
    public function all(Request $request)
    {
        return view('horarios/horarios', ['horarios' => Horario::all()]);
    }
}
