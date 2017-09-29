@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Noticia</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/noticias/edit/'.$noticia->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-4 control-label">Titulo</label>
                    <div class="col-md-6">
                        <input id="title" type="text" class="form-control" name="title" value="{{ old('title')?old('title'):$noticia->title }}" required autofocus>
                        @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                    <label for="content" class="col-md-4 control-label">Contenido</label>
                    <div class="col-md-6">
                        <input id="content" type="text" class="form-control" name="content" value="{{ old('content')?old('content'):$noticia->content }}" required>
                        @if ($errors->has('content'))
                            <span class="help-block">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label for="image" class="col-md-4 control-label">Imagen de Referencia</label>
                    @if(!$noticia->image)
                        <div class="col-md-6">
                            <input id="image" type="file" name="image" class="form-control" disabled>
                            @if ($errors->has('image'))
                                <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                            @endif
                        </div>
                    @else
                        <div class="col-md-4">
                            <input id="image" type="file" name="image" class="form-control">
                            @if ($errors->has('image'))
                                <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <img src="{{$noticia->image}}" alt="imagen-articulo" style="width:100px;height:100px;">
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Editar Noticia
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

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/noticias/delete/'.$noticia->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar la materia? no se pueden revertir los cambios.')">
                            Elimiar Noticia
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
