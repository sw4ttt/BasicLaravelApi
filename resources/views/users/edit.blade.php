@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Usuario</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/users/edit/'.$usuario->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre')?old('nombre'):$usuario->nombre }}" data-validation="length" data-validation-length="5-50" data-validation-optional="true" required autofocus>
                        @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">Email</label>
                    <div class="col-md-6">
                        <input id="email" type="text" class="form-control" name="email" value="{{ old('email')?old('email'):$usuario->email }}" data-validation="email" required>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('tipoIdPersonal') ? ' has-error' : '' }}">
                    <label for="tipoIdPersonal" class="col-md-4 control-label">Tipo Id Personal</label>
                    <div class="col-md-6">
                        <input id="tipoIdPersonal" type="text" class="form-control" name="tipoIdPersonal" value="{{ old('tipoIdPersonal')?old('tipoIdPersonal'):$usuario->tipoIdPersonal }}" data-validation="length" data-validation-length="5-30" data-validation-optional="true" required>
                        @if ($errors->has('tipoIdPersonal'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipoIdPersonal') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('idPersonal') ? ' has-error' : '' }}">
                    <label for="idPersonal" class="col-md-4 control-label">Id Personal</label>
                    <div class="col-md-6">
                        <input id="idPersonal" type="text" class="form-control" name="idPersonal" value="{{ old('idPersonal')?old('idPersonal'):$usuario->idPersonal }}" data-validation="length" data-validation-length="5-30" data-validation-optional="true" required>
                        @if ($errors->has('idPersonal'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idPersonal') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('tlfDomicilio') ? ' has-error' : '' }}">
                    <label for="tlfDomicilio" class="col-md-4 control-label">Tlf Domicilio</label>
                    <div class="col-md-6">
                        <input id="tlfDomicilio" type="text" class="form-control" name="tlfDomicilio" data-validation="custom" data-validation-regexp="\+(57\d{8,9})" data-validation-help="ejemplo +5712345678" value="{{ old('tlfDomicilio')?old('tlfDomicilio'):$usuario->tlfDomicilio }}" data-validation-optional="true" required>
                        @if ($errors->has('tlfDomicilio'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tlfDomicilio') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('tlfCelular') ? ' has-error' : '' }}">
                    <label for="tlfCelular" class="col-md-4 control-label">Tlf Celular</label>
                    <div class="col-md-6">
                        <input id="tlfCelular" type="text" class="form-control" name="tlfCelular" data-validation="custom" data-validation-regexp="\+(57\d{8,9})" data-validation-help="ejemplo +5712345678" value="{{ old('tlfCelular')?old('tlfCelular'):$usuario->tlfCelular }}" data-validation-optional="true" required>
                        @if ($errors->has('tlfCelular'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tlfCelular') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                    <label for="direccion" class="col-md-4 control-label">Direccion</label>
                    <div class="col-md-6">
                        <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion')?old('direccion'):$usuario->direccion }}" data-validation="length" data-validation-length="5-50" data-validation-optional="true" required>
                        @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">Password</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" data-validation-optional="true">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" data-validation-optional="true">

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
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

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/users/delete/'.$usuario->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar la materia? no se pueden revertir los cambios.')">
                            Elimiar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
