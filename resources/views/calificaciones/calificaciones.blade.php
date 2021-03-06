@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Calificaciones</h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Seccion</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                @if(count($materias) > 0)
                    @foreach ($materias as $materia)
                        <tr>
                            <td>{{ $materia->nombre }}</td>
                            <td>
                                {{ isset($materia->gradoTexto)?$materia->gradoTexto:$materia->grado }}
                            </td>
                            <td>{{ $materia->seccion }}</td>
                            <td>
                                <a class="btn btn-primary" href="{{ url('/calificaciones/materia/'.$materia->id) }}" role="button">Consultar</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
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
