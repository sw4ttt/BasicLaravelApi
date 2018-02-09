@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Cursos <span class="badge"> {{count($cursos)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Grado</th>
                        <th>Seccion</th>
                        <th>Cupos</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($cursos) > 0)
                        @foreach ($cursos as $curso)
                            <tr>
                                <td>
                                    {{ isset($curso->gradoTexto)?$curso->gradoTexto:$curso->grado }}
                                </td>
                                <td>{{ $curso->seccion }}</td>
                                <td>{{ $curso->cupos }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/cursos/edit/'.$curso->id) }}" role="button">Editar</a>
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
