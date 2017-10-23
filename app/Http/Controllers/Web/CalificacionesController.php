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
use App\Materia;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NotificacionGeneral;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use OneSignal;
use App\Horario;

class CalificacionesController extends Controller
{
    public function all(Request $request)
    {
        $materias = Materia::all();
        $materias->transform(function ($item, $key) {
            switch ($item->grado) {
                case 1:
                    $item->gradoTexto = "Primero";
                    break;
                case 2:
                    $item->gradoTexto = "Segundo";
                    break;
                case 3:
                    $item->gradoTexto = "Tercero";
                    break;
                case 4:
                    $item->gradoTexto = "Cuarto";
                    break;
                case 5:
                    $item->gradoTexto = "Quinto";
                    break;
                case 6:
                    $item->gradoTexto = "Transición";
                    break;
                default:
                    $item->gradoTexto = "Otro";
            }
            return $item;
        });
        return view('calificaciones/calificaciones', ['materias' => $materias]);
    }

    public function editarEvaluacion(Request $request,$id,$nombreEvaluacion)
    {
        $input = $request->only('nota');
        $validator = Validator::make($input, [
            'nota' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }
        $calificacion = Calificacion::find($id);

        if (is_null($calificacion))
            return redirect("calificaciones");

//        return response()->json(['success' => true, 'nombre' => $evaluaciones[0]['nombre']]);

        $evaluaciones = $calificacion->evaluaciones;

        $position = 0;
        foreach ($calificacion->evaluaciones as $itemEvaluacion){
            if ($itemEvaluacion['nombre'] === $nombreEvaluacion){
                $evaluaciones[$position]['nota'] = $input['nota'];
                continue;
            }
            $position++;
        }

        $calificacion->evaluaciones = $evaluaciones;
        $calificacion->save();

        return redirect("calificaciones/materia/".$calificacion->idMateria)->with('message', 'Evaluacion Editada!');

    }

    public function editarAcumulado(Request $request,$id)
    {
        $input = $request->only('acumulado');
        $validator = Validator::make($input, [
            'acumulado' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            //throw new ValidationHttpException($validator->errors()->all());
            return back()->withErrors($validator)->withInput();
        }
        $calificacion = Calificacion::find($id);

        if (is_null($calificacion))
            return redirect("calificaciones");

        $calificacion->acumulado = $input['acumulado'];
        $calificacion->save();

        return redirect("calificaciones/materia/".$calificacion->idMateria)->with('message', 'Acumulado Editado!');

    }


    public function getForMateria(Request $request,$id)
    {
        $materia = Materia::find($id);
        if(is_null($materia))
            return redirect("calificaciones");
        $calificaciones = Calificacion::where('idMateria',$materia->id)->get();
        $estudiantes = Estudiante::all();

        $calificaciones->transform(function ($calItem, $calKey) use(&$estudiantes){

            $keyEstudiante = $estudiantes->search(function ($estItem, $estKey) use(&$calItem){
                return $estItem->id === $calItem->idEstudiante;
            });

            $calItem['nombreEstudiante']= $estudiantes[$keyEstudiante]->nombre;

            return $calItem;
        });

//        return response()->json(['success'=>true,'calificaciones'=>$calificaciones]);

        return view('/calificaciones/edit',[
            'materia'=>$materia,
            'calificaciones'=>$calificaciones
        ]);

    }

    public function addEvaluacion(Request $request,$id)
    {
        $input = $request->only('titulo','mensaje');

        $input['titulo'] = rtrim($input['titulo']);

        $materia = Materia::find($id);

        if(is_null($materia))
            return redirect("calificaciones");

        $calificaciones = Calificacion::where('idMateria',$materia->id)->get();

        if(count($calificaciones) === 0){
            $profesor = $materia->profesores()->first();

            $created = Carbon::now()->format('Y-m-d H:i:s');
            $updated = Carbon::now()->format('Y-m-d H:i:s');

            $estudiantes = Estudiante::where('grado',$materia->grado)->get();
            foreach ($estudiantes as $estudiante) {
                Calificacion::create([
                    'idProfesor'=>$profesor->id,
                    'idEstudiante'=>$estudiante->id,
                    'idMateria'=>$materia->id,
                    'periodo'=>'2017-2018',
                    'evaluaciones'=>[],
                    'acumulado'=>0,
                    'created_at'=>$created,
                    'updated_at'=>$updated,
                ]);
            }
        }

        $calificaciones = Calificacion::where('idMateria',$materia->id)->get();

        $auxCalificacion = $calificaciones->first();

        if(!is_null($auxCalificacion)){
            $existe = false;
            foreach ($auxCalificacion->evaluaciones as $evaluacion){
                if($evaluacion['nombre'] === $input['titulo'])
                    $existe = true;
            }

            if($existe === true)
                return back()->withErrors(['titulo'=>['Ya existe una evaluacion con ese titulo']])->withInput();
        }

//        return back()->withErrors(['titulo'=>['TODO BIEN']])->withInput();

        foreach ($calificaciones as $calificacion){

            $evaluacion = new \stdClass;
            $evaluacion->nombre = $input['titulo'];
            $evaluacion->nota = 0;
            $evaluacion->mensaje = $input['mensaje'];

            $tempEvaluaciones = $calificacion->evaluaciones;

            if(is_array($tempEvaluaciones))
            {
                array_push($tempEvaluaciones,$evaluacion);
                $calificacion->evaluaciones = $tempEvaluaciones;
                $calificacion->save();
            }
        }

        return redirect("calificaciones/materia/".$materia->id)->with('message', 'Evaluacion Creada!');

    }
}
