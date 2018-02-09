@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Materia</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materias/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" data-validation="length" data-validation-length="5-50" data-validation-optional="true" required autofocus>
                        @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('curso') ? ' has-error' : '' }}">
                    <label for="curso" class="col-md-4 control-label">Curso</label>
                    <div class="col-md-6">
                        <select id="curso" name="curso" class="form-control" required>
                            @if(isset($cursos) && count($cursos) > 0)
                                @foreach ($cursos as $curso)
                                    <option value="{{$curso->id}}">{{$curso->gradoTexto}} - {{$curso->seccion}}</option>
                                @endforeach
                            @else
                                <option>-</option>
                            @endif
                        </select>
                        @if ($errors->has('curso'))
                            <span class="help-block">
                                <strong>{{ $errors->first('curso') }}</strong>
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
                                    <option value="{{$profesor->id}}">{{$profesor->id}} - {{$profesor->nombre}}</option>
                                @endforeach
                            @else
                                <option>-</option>
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
                        <button type="submit" class="btn btn-primary save" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Materia
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
