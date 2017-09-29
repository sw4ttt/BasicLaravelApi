@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Materia</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materias/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{--<td>{{ $horario->entidad }}</td>--}}
                {{--<td>{{ $horario->idEntidad }}</td>--}}
                {{--<td>{{ $horario->nombreEntidad }}</td>--}}
                {{--<td>{{ $horario->descripcion }}</td>--}}
                {{--<td>{{ $horario->dia }}</td>--}}
                {{--<td>{{ $horario->inicio }}</td>--}}
                {{--<td>{{ $horario->fin }}</td>--}}
                {{--<td>{{ $horario->grado }}</td>--}}
                {{--<td>{{ $horario->lugar }}</td>--}}

                <div class="form-group{{ $errors->has('entidad') ? ' has-error' : '' }}">
                    <label for="entidad" class="col-md-4 control-label">Entidad</label>
                    <div class="col-md-6">
                        <select id="entidad" name="entidad" class="form-control" required>
                            <option>Materia</option>
                            <option>Profesor</option>
                            <option>General</option>
                        </select>
                        @if ($errors->has('entidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('entidad') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                    <label for="grado" class="col-md-4 control-label">Grado</label>
                    <div class="col-md-6">
                        <input id="grado" type="text" class="form-control" name="grado" value="{{ old('grado') }}" required>
                        @if ($errors->has('grado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-4 control-label">Tipo de Usuario</label>
                    <div class="col-md-6">
                        {{--<input id="type" type="text" class="form-control" name="type" value="{{ old('type') }}" required>--}}

                        <select id="type" name="type" class="form-control" required>
                            <option>ADMIN</option>
                            <option>PROFESOR</option>
                            <option>REPRESENTANTE</option>
                        </select>

                        @if ($errors->has('type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Horario
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
        </div>
    </div>
@endsection
