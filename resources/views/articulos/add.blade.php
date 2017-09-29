@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Articulo</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/articulos/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                {{--'nombre',--}}
                {{--'cantidad',--}}
                {{--'estado',--}}
                {{--'precio',--}}
                {{--'image',--}}
                {{--'categoria',--}}
                {{--'descripcion'--}}

                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required autofocus>
                        @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('cantidad') ? ' has-error' : '' }}">
                    <label for="cantidad" class="col-md-4 control-label">Cantidad</label>
                    <div class="col-md-6">
                        <input id="cantidad" type="text" class="form-control" name="cantidad" value="{{ old('cantidad') }}" autofocus>
                        @if ($errors->has('cantidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cantidad') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('precio') ? ' has-error' : '' }}">
                    <label for="precio" class="col-md-4 control-label">Precio</label>
                    <div class="col-md-6">
                        <input id="precio" type="text" class="form-control" name="precio" value="{{ old('precio') }}" required autofocus>
                        @if ($errors->has('precio'))
                            <span class="help-block">
                                <strong>{{ $errors->first('precio') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label for="image" class="col-md-4 control-label">Imagen de Referencia</label>
                    <div class="col-md-6">
                        <input id="image" type="file" name="image" class="form-control">
                        @if ($errors->has('image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('categoria') ? ' has-error' : '' }}">
                    <label for="categoria" class="col-md-4 control-label">Categoria</label>
                    <div class="col-md-6">
                        <select id="categoria" name="categoria" class="form-control selectpicker show-tick" required>
                            <option value="PAPELERIA">Papeleria</option>
                            <option value="LIBRERIA">Libreria</option>
                            <option value="UTILES">Utiles</option>
                            <option value="MATRICULA">Matricula</option>
                        </select>

                        @if ($errors->has('categoria'))
                            <span class="help-block">
                                <strong>{{ $errors->first('categoria') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                    <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                    <div class="col-md-6">
                        <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" required autofocus>
                        @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('estado') ? ' has-error' : '' }}">
                    <label for="estado" class="col-md-4 control-label">Estado</label>
                    <div class="col-md-6">
                        <select id="estado" name="estado" class="form-control selectpicker show-tick" required>
                            <option>HABILITADO</option>
                            <option>DESHABILITADO</option>
                        </select>

                        @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Articulo
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
