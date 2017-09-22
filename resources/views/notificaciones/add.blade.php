@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Materia</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/notificaciones/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{--'asunto' => 'required|string',--}}
                {{--'mensaje' => 'required|string',--}}
                {{--'grupo' => 'required|string|in:GRADO,TODOS',--}}
                {{--'idGrupo' => 'required_if:grupo,GRADO',--}}

                <div class="form-group{{ $errors->has('asunto') ? ' has-error' : '' }}">
                    <label for="asunto" class="col-md-4 control-label">Asunto</label>
                    <div class="col-md-6">
                        <input id="asunto" type="text" class="form-control" name="asunto" value="{{ old('asunto') }}" required autofocus>
                        @if ($errors->has('asunto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asunto') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('mensaje') ? ' has-error' : '' }}">
                    <label for="mensaje" class="col-md-4 control-label">Mensaje</label>
                    <div class="col-md-6">
                        <input id="mensaje" type="text" class="form-control" name="mensaje" value="{{ old('mensaje') }}" required autofocus>
                        @if ($errors->has('mensaje'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mensaje') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('grupo') ? ' has-error' : '' }}">
                    <label for="grupo" class="col-md-4 control-label">Grupo</label>
                    <div class="col-md-6">
                        <select id="grupo" name="grupo" class="form-control" required>
                            <option value="GRADO">GRADO</option>
                            <option value="TODOS">TODOS</option>
                        </select>
                        @if ($errors->has('grupo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grupo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div id="opcionalNotificaciones" >
                    <div class="form-group{{ $errors->has('idGrupo') ? ' has-error' : '' }}">
                        <label for="idGrupo" class="col-md-4 control-label">Grado</label>
                        <div class="col-md-6">
                            <input id="idGrupo" type="text" class="form-control" name="idGrupo" value="{{ old('idGrupo') }}">
                            @if ($errors->has('idGrupo'))
                                <span class="help-block">
                                <strong>{{ $errors->first('idGrupo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Enviar Notificacion
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
