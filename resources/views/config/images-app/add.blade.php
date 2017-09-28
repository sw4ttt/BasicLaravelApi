@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Cargar Imagenes Para App</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/config/images-app/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{--'principal',--}}
                {{--'noticias',--}}
                {{--'calificaciones',--}}
                {{--'materiales',--}}
                {{--'horario',--}}
                {{--'pagos',--}}
                {{--'tienda'--}}
                <div class="form-group{{ $errors->has('principal') ? ' has-error' : '' }}">
                    <label for="principal" class="col-md-4 control-label">principal</label>
                    <div class="col-md-6">
                        <input id="principal" type="file" name="principal" class="form-control" required>
                        @if ($errors->has('principal'))
                            <span class="help-block">
                                <strong>{{ $errors->first('principal') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('noticias') ? ' has-error' : '' }}">
                    <label for="noticias" class="col-md-4 control-label">noticias</label>
                    <div class="col-md-6">
                        <input id="noticias" type="file" name="noticias" class="form-control" required>
                        @if ($errors->has('noticias'))
                            <span class="help-block">
                                <strong>{{ $errors->first('noticias') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('calificaciones') ? ' has-error' : '' }}">
                    <label for="calificaciones" class="col-md-4 control-label">calificaciones</label>
                    <div class="col-md-6">
                        <input id="calificaciones" type="file" name="calificaciones" class="form-control" required>
                        @if ($errors->has('calificaciones'))
                            <span class="help-block">
                                <strong>{{ $errors->first('calificaciones') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('materiales') ? ' has-error' : '' }}">
                    <label for="materiales" class="col-md-4 control-label">materiales</label>
                    <div class="col-md-6">
                        <input id="materiales" type="file" name="materiales" class="form-control" required>
                        @if ($errors->has('materiales'))
                            <span class="help-block">
                                <strong>{{ $errors->first('materiales') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('horario') ? ' has-error' : '' }}">
                    <label for="horario" class="col-md-4 control-label">horario</label>
                    <div class="col-md-6">
                        <input id="horario" type="file" name="horario" class="form-control" required>
                        @if ($errors->has('horario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('horario') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('pagos') ? ' has-error' : '' }}">
                    <label for="pagos" class="col-md-4 control-label">pagos</label>
                    <div class="col-md-6">
                        <input id="pagos" type="file" name="pagos" class="form-control" required>
                        @if ($errors->has('pagos'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pagos') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('tienda') ? ' has-error' : '' }}">
                    <label for="tienda" class="col-md-4 control-label">tienda</label>
                    <div class="col-md-6">
                        <input id="tienda" type="file" name="tienda" class="form-control" required>
                        @if ($errors->has('tienda'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tienda') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Cargar Imagenes
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
