@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <h2 style="text-align: left">Calificaciones de Materia</h2>
                    <h3 style="text-align: left">Nombre: {{ $materia->nombre }}</h3>
                    <h3 style="text-align: left">Grado: {{ $materia->grado }}</h3>
                </div>
                <div class="col-md-4" style="margin-top: 22px">
                    <div class="row">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/calificaciones/evaluacion/add/materia/'.$materia->id) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div><h3>Crear Evaluación</h3></div>
                            <div class="form-group{{ $errors->has('titulo') ? ' has-error' : '' }}">
                                <label for="titulo" class="col-md-4 control-label">Titulo</label>
                                <div class="col-md-6">
                                    <input id="titulo" type="text" class="form-control" name="titulo" value="{{ old('titulo') }}">
                                    @if ($errors->has('titulo'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('titulo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('mensaje') ? ' has-error' : '' }}">
                                <label for="mensaje" class="col-md-4 control-label">Mensaje</label>
                                <div class="col-md-6">
                                    <input id="mensaje" type="text" class="form-control" name="mensaje" value="{{ old('mensaje') }}">
                                    @if ($errors->has('mensaje'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('mensaje') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Estas seguro que deseas eliminar el horario? no se pueden revertir los cambios.')">
                                        Crear Evaluación
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Evaluaciones</th>
                    <th>Acumulado</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($calificaciones) && count($calificaciones) > 0)
                    @foreach ($calificaciones as $calificacion)
                        <tr>
                            <td>{{ $calificacion->nombreEstudiante }}</td>
                            <td>
                                <ul class="list-group">
                                    @if(count($calificacion->evaluaciones)>0)
                                        @foreach ($calificacion->evaluaciones as $evaluacion)
                                            <li class="list-group-item"><strong>Titulo:</strong> {{$evaluacion['nombre']}} <strong>Nota:</strong> {{$evaluacion['nota']}}</li>
                                        @endforeach
                                    @else
                                        <li class="list-group-item">-</li>
                                    @endif
                                </ul>
                            </td>
                            <td>{{ $calificacion->acumulado }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>..</td>
                        <td>..</td>
                        <td>..</td>
                    </tr>
                @endif
                </tbody>
            </table>
            @if (Session::has('message'))
                <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ Session::get('message') }}</strong>
                </div>
            @endif
        </div>
    </div>

@endsection
