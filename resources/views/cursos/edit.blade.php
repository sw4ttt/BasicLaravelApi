@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Curso</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/cursos/edit/'.$curso->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('seccion') ? ' has-error' : '' }}">
                    <label for="seccion" class="col-md-4 control-label">Seccion</label>
                    <div class="col-md-6">
                        <input id="seccion" type="text" class="form-control" name="seccion" value="{{ old('seccion')?old('seccion'):$curso->seccion }}" data-validation="length" data-validation-length="1-5" data-validation-optional="true" required autofocus>
                        @if ($errors->has('seccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seccion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('cupos') ? ' has-error' : '' }}">
                    <label for="cupos" class="col-md-4 control-label">Cupos</label>
                    <div class="col-md-6">
                        <input id="cupos" type="text" class="form-control" name="cupos" value="{{ old('cupos')?old('cupos'):$curso->cupos }}" data-validation="number" required>
                        @if ($errors->has('cupos'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cupos') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                    <label for="grado" class="col-md-4 control-label">Grado</label>
                    <div class="col-md-6">
                        {{--<input id="grado" type="text" class="form-control" name="grado" value="{{ old('grado') }}">--}}

                        <select id="grado" name="grado" class="form-control" required>
                            <option {{$curso->grado === 1?"selected":""}} value="1">Primero</option>
                            <option {{$curso->grado === 2?"selected":""}} value="2">Segundo</option>
                            <option {{$curso->grado === 3?"selected":""}} value="3">Tercero</option>
                            <option {{$curso->grado === 4?"selected":""}} value="4">Cuarto</option>
                            <option {{$curso->grado === 5?"selected":""}} value="5">Quinto</option>
                            <option {{$curso->grado === 6?"selected":""}} value="6">Transición</option>
                        </select>

                        @if ($errors->has('grado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
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

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/cursos/delete/'.$curso->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar el curso? no se pueden revertir los cambios.')">
                            Elimiar Curso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
