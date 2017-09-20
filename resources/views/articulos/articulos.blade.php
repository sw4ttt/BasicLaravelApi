@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Articulos <span class="badge"> {{count($articulos)}}</span></h2></div>
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
                                <td>{{ $articulo->cantidad }}</td>
                                <td>{{ $articulo->estado }}</td>
                                <td>{{ $articulo->precio }}</td>
                                <td>
                                    <a class="" href="{{ $articulo->image }}">Link</a>
                                    {{--{{ $articulo->image }}--}}
                                </td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/materias/edit/'.$articulo->id) }}" role="button">Editar</a>
                                    <a class="btn btn-danger" href="{{ url('/materias/delete/'.$articulo->id) }}"
                                       role="button"
                                       onclick="return confirm('Esta seguro? Se eliminara toda la informacion relacionada a el articulo. ')">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
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
