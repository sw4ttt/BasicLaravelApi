@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Materiales <span class="badge"> {{count($materiales)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Titulo</th>
                    <th>Descripcion</th>
                    <th>Materia</th>
                    <th>Tamaño</th>
                    <th>Archivo</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                @if(count($materiales) > 0)
                    @foreach ($materiales as $material)
                        <tr>
                            <td>{{ $material->titulo }}</td>
                            <td>{{ $material->descripcion }}</td>
                            <td>{{ $material->idMateria }}</td>
                            <td>{{ $material->size }}</td>
                            <td><a href="{{ $material->file }}">Archivo</a></td>
                            <td>
                                <a class="btn btn-primary" href="{{ url('/materiales/edit/'.$material->id) }}" role="button">Editar</a>
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
