@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Articulos y Pagables <span class="badge"> {{count($articulos)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Categoria</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Image</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($articulos) > 0)
                        @foreach ($articulos as $articulo)
                            <tr>
                                {{--'nombre',--}}
                                {{--'cantidad',--}}
                                {{--'estado',--}}
                                {{--'precio',--}}
                                {{--'image',--}}
                                {{--'categoria',--}}
                                {{--'descripcion'--}}
                                <td>{{ $articulo->nombre }}</td>
                                <td>{{ $articulo->descripcion }}</td>
                                <td>{{ $articulo->categoria }}</td>
                                <td>
                                    @if(!isset($articulo->cantidad))
                                        N/A
                                    @else
                                        {{ $articulo->cantidad }}
                                    @endif
                                </td>
                                <td>{{ $articulo->estado }}</td>
                                <td>{{ $articulo->precio }}</td>
                                <td>
                                    @if(!isset($articulo->image))
                                        N/A
                                    @else
                                        <img src="{{$articulo->image}}" alt="imagen-articulo" style="width:100px;height:100px;">
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/articulos/edit/'.$articulo->id) }}" role="button">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                            <td>..</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @if (Session::has('message'))
                <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ Session::get('message') }}</strong>
                </div>
            @endif
        </div>
    </div>
@endsection
