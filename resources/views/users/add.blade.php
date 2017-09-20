@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Usuario</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/users/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                {{--'tipoIdPersonal' => 'required|string',--}}
                {{--'idPersonal' => 'required|numeric|unique:users,idPersonal',--}}
                {{--'nombre' => 'required|string',--}}
                {{--'tlfDomicilio' => 'required|string',--}}
                {{--'tlfCelular' => 'required|string',--}}
                {{--'direccion' => 'required|string',--}}
                {{--'email' => 'required|email|unique:users,email',--}}
                {{--'image' => 'required|image',--}}
                {{--'password' => 'required|min:4',--}}
                {{--'type' => 'required|string|in:ADMIN,PROFESOR,REPRESENTANTE'--}}

                {{--'nombreEstudiante' => 'required|string',--}}
                {{--'idPersonalEstudiante' => 'required|string',--}}
                {{--'grado' => 'required|integer'--}}

                <div class="form-group{{ $errors->has('tipoIdPersonal') ? ' has-error' : '' }}">
                    <label for="tipoIdPersonal" class="col-md-4 control-label">Tipo Id Personal</label>
                    <div class="col-md-6">
                        <input id="tipoIdPersonal" type="text" class="form-control" name="tipoIdPersonal" value="{{ old('tipoIdPersonal') }}" required autofocus>
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
                        <input id="idPersonal" type="text" class="form-control" name="idPersonal" value="{{ old('idPersonal') }}" required>
                        @if ($errors->has('idPersonal'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idPersonal') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                        @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail</label>
                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('tlfDomicilio') ? ' has-error' : '' }}">
                    <label for="tlfDomicilio" class="col-md-4 control-label">Tlf Domicilio</label>
                    <div class="col-md-6">
                        <input id="tlfDomicilio" type="text" class="form-control" name="tlfDomicilio" value="{{ old('tlfDomicilio') }}" required>
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
                        <input id="tlfCelular" type="text" class="form-control" name="tlfCelular" value="{{ old('tlfCelular') }}" required>
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
                        <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" required>
                        @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
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

//**********************************************************************************************************************
                {{--'nombreEstudiante' => 'required|string',--}}
                {{--'idPersonalEstudiante' => 'required|string',--}}
                {{--'grado' => 'required|integer'--}}
                <div class="form-group{{ $errors->has('nombreEstudiante') ? ' has-error' : '' }}">
                    <label for="nombreEstudiante" class="col-md-4 control-label">Nombre Estudiante</label>
                    <div class="col-md-6">
                        <input id="nombreEstudiante" type="text" class="form-control" name="nombreEstudiante" value="{{ old('nombreEstudiante') }}">
                        @if ($errors->has('nombreEstudiante'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombreEstudiante') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('idPersonalEstudiante') ? ' has-error' : '' }}">
                    <label for="idPersonalEstudiante" class="col-md-4 control-label">Id Personal Estudiante</label>
                    <div class="col-md-6">
                        <input id="idPersonalEstudiante" type="text" class="form-control" name="idPersonalEstudiante" value="{{ old('idPersonalEstudiante') }}">
                        @if ($errors->has('idPersonalEstudiante'))
                            <span class="help-block">
                                <strong>{{ $errors->first('idPersonalEstudiante') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                    <label for="grado" class="col-md-4 control-label">Grado</label>
                    <div class="col-md-6">
                        <input id="grado" type="text" class="form-control" name="grado" value="{{ old('grado') }}">
                        @if ($errors->has('grado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
//**********************************************************************************************************************

                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label for="image" class="col-md-4 control-label">Imagen de Perfil</label>
                    <div class="col-md-6">
                        <input id="image" type="file" name="image" class="form-control" required>
                        {{--<input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" required>--}}
                        @if ($errors->has('image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-md-4 control-label">Password</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" name="password" required>

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
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Crear Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
