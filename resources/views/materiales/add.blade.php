@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Material</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materiales/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('titulo') ? ' has-error' : '' }}">
                    <label for="titulo" class="col-md-4 control-label">Titulo</label>
                    <div class="col-md-6">
                        <input id="titulo" type="text" class="form-control" name="titulo" value="{{ old('titulo') }}" required autofocus>
                        @if ($errors->has('titulo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('titulo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                    <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                    <div class="col-md-6">
                        <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" required>
                        @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('idMateria') ? ' has-error' : '' }}">
                    <label for="idMateria" class="col-md-4 control-label">Materia</label>
                    <div class="col-md-6">
                        <select id="idMateria" name="idMateria" class="form-control" required>
                            @if(isset($materias) && count($materias) > 0)
                                @foreach ($materias as $materia)
                                    <option value="{{$materia->id}}">{{$materia->nombre}}</option>
                                @endforeach
                            @else
                                <option>-</option>
                            @endif
                        </select>
                        @if ($errors->has('idMateria'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idMateria') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="file" class="col-md-4 control-label">Archivo</label>
                    <div class="col-md-6">
                        <input id="file" type="file" name="file" class="form-control" required>
                        @if ($errors->has('file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Material
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
