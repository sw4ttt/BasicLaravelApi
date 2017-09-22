@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Material</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materiales/edit/'.$material->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{--'idMateria','titulo','descripcion','file'--}}

                <div class="form-group{{ $errors->has('titulo') ? ' has-error' : '' }}">
                    <label for="titulo" class="col-md-4 control-label">Titulo</label>
                    <div class="col-md-6">
                        <input id="titulo" type="text" class="form-control" name="titulo" value="{{ old('titulo')?old('titulo'):$material->titulo }}" required autofocus>
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
                        <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion')?old('descripcion'):$material->descripcion }}" required>
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
                                    <option value="{{$materia->id}}">{{$materia->id}} - {{$materia->nombre}}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('idMateria'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idMateria') }}</strong>
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

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/materiales/delete/'.$material->id) }}" enctype="multipart/form-data">
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
