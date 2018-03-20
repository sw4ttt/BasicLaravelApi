@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <h2 style="text-align: left">Calificaciones de Materia</h2>
                    <h3 style="text-align: left">Nombre: {{ $materia->nombre }}</h3>
                    <h3 style="text-align: left">Profesor: {{ $profesor->nombre }}</h3>
                    <h3 style="text-align: left">Grado: {{ $materia->grado }}</h3>
                    <h3 style="text-align: left">Seccion: {{ $materia->seccion }}</h3>
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
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Estas seguro que deseas crear la evaluacion?')">
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
                        <th class="col-md-4" style="text-align: center">Titulo</th>
                        <th class="col-md-6" style="text-align: center">Mensaje</th>
                        <th class="col-md-2" style="text-align: center">Opcion</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($evaluaciones) && count($evaluaciones) > 0)
                    @foreach ($evaluaciones as $evaluacion)
                        <tr>
                            <td class="col-md-4" style="text-align: center">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        {{ $evaluacion['nombre'] }}
                                    </li>
                                </ul>
                            </td>
                            <td class="col-md-6" style="text-align: center">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        {{ $evaluacion['mensaje'] }}
                                    </li>
                                </ul>
                            </td>
                            <td class="col-md-2" style="text-align: center">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <form class="form-horizontal" role="form" method="POST" action="{{ url('calificaciones/materia/'.$materia->id.'/delete/evaluacion/'.$evaluacion['nombre'].'/'.$evaluacion['mensaje']) }}" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar la Evaluacion? no se pueden revertir los cambios.')">
                                                        Elimiar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                    </li>
                                </ul>
                            </td>
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

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-md-2" style="text-align: center">Estudiante</th>
                        <th class="col-md-3" style="text-align: center">Evaluaciones</th>
                        <th class="col-md-2" style="text-align: center">Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($calificaciones) && count($calificaciones) > 0)
                    @foreach ($calificaciones as $calificacion)
                        <tr>
                            <td class="col-md-2" style="text-align: center">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        {{ $calificacion->nombreEstudiante }}
                                    </li>
                                </ul>
                            </td>
                            <td class="col-md-3" style="text-align: center">
                                <ul class="list-group">
                                    @if(count($calificacion->evaluaciones)>0)
                                        @foreach ($calificacion->evaluaciones as $evaluacion)
                                            <li class="list-group-item">
                                                <form class="form-horizontal" role="form" method="POST" action="{{ url('/calificaciones/'.$calificacion->id.'/evaluacion/'.$evaluacion['nombre']) }}" enctype="multipart/form-data">
                                                    {{ csrf_field() }}

                                                    <div class="form-group{{ $errors->has('nota') ? ' has-error' : '' }}">

                                                        <div class="row">
                                                            <label for="titulo" class="col-md-4 control-label">Titulo: {{$evaluacion['nombre']}}</label>
                                                            {{--<label for="nota" class="col-md-2 control-label">Nota: {{$evaluacion['nombre']}}</label>--}}
                                                            <div class="col-md-4">
                                                                <label for="nota" class="control-label">Nota</label>
                                                                <input id="nota" type="text" data-validation="number" data-validation-allowing="range[0.0;5.0],float" data-validation-decimal-separator="." data-validation-help="Valor de 0-5" class="form-control input-sm" name="nota" value="{{ $evaluacion['nota'] }}">
                                                                @if ($errors->has('nota'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('nota') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-4">
                                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Guardar Nota?.')">
                                                                    Guardar
                                                                </button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="list-group-item">-</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="col-md-3" style="text-align: center">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/calificaciones/'.$calificacion->id.'/acumulado') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group{{ $errors->has('acumulado') ? ' has-error' : '' }}">
                                                <label for="acumulado" class="col-md-4 control-label">Acumulado:</label>
                                                <div class="col-md-4">
                                                    <input id="acumulado" type="text" data-validation="number" data-validation-allowing="range[0.0;5.0],float" data-validation-decimal-separator="." data-validation-help="Valor de 0-5" class="form-control input-sm" data-validation-optional="true" name="acumulado" value="{{ $calificacion->acumulado }}">
                                                    @if ($errors->has('acumulado'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('acumulado') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Guardar Acumulado?')">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </td>
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
