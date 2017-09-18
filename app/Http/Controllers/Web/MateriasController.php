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
use App\Materia;

class MateriasController extends Controller
{
    public function all(Request $request)
    {
        $user = Auth::user();

        if($user->type === 'ADMIN')
        {
            $materias = Materia::all();
            return view('materias/materias', ['materias' => $materias]);
        }
        else
            return redirect('home');


    }

}
