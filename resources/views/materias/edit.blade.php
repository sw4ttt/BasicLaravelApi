@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Materia</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materias/edit/'.$materia->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre')?old('nombre'):$materia->nombre }}" data-validation="length" data-validation-length="min5,max50" data-validation-optional="true" required autofocus>
                        @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                    <label for="grado" class="col-md-4 control-label">Grado</label>
                    <div class="col-md-6">
                        {{--<input id="grado" type="text" class="form-control" name="grado" value="{{ old('grado') }}">--}}

                        <select id="grado" name="grado" class="form-control" required>
                            <option {{$materia->grado === 1?"selected":""}} value="1">Primero</option>
                            <option {{$materia->grado === 2?"selected":""}} value="2">Segundo</option>
                            <option {{$materia->grado === 3?"selected":""}} value="3">Tercero</option>
                            <option {{$materia->grado === 4?"selected":""}} value="4">Cuarto</option>
                            <option {{$materia->grado === 5?"selected":""}} value="5">Quinto</option>
                            <option {{$materia->grado === 6?"selected":""}} value="6">Transición</option>
                        </select>

                        @if ($errors->has('grado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('idProfesor') ? ' has-error' : '' }}">
                    <label for="idProfesor" class="col-md-4 control-label">Profesor</label>
                    <div class="col-md-6">
                        <select id="idProfesor" name="idProfesor" class="form-control" required>
                            @if(isset($profesores) && count($profesores) > 0)
                                @foreach ($profesores as $profesor)
                                    <option {{(strtoupper($profesor->nombre) === strtoupper($materia->profesor))?"selected":""}} value="{{$profesor->id}}">{{$profesor->id}} - {{$profesor->nombre}}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('idProfesor'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idProfesor') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Estas seguro?')">
                            Guardar Cambios
                        </button>
                        @if (Session::has('message'))
                            <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>{{ Session::get('message') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </form>

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materias/delete/'.$materia->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar la materia? no se pueden revertir los cambios.')">
                            Elimiar Materia
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
