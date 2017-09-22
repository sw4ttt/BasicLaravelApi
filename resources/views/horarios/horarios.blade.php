@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Horarios <span class="badge"> {{count($horarios)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>entidad</th>
                        <th>idEntidad</th>
                        <th>nombreEntidad</th>
                        <th>descripcion</th>
                        <th>dia</th>
                        <th>inicio</th>
                        <th>fin</th>
                        <th>grado</th>
                        <th>lugar</th>
                        <th>Opcion</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($horarios) > 0)
                        @foreach ($horarios as $horario)
                            <tr>
                                <td>{{ $horario->entidad }}</td>
                                <td>{{ $horario->idEntidad }}</td>
                                <td>{{ $horario->nombreEntidad }}</td>
                                <td>{{ $horario->descripcion }}</td>
                                <td>{{ $horario->dia }}</td>
                                <td>{{ $horario->inicio }}</td>
                                <td>{{ $horario->fin }}</td>
                                <td>{{ $horario->grado }}</td>
                                <td>{{ $horario->lugar }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/horarios/edit/'.$horario->id) }}" role="button">Editar</a>
                                    <a class="btn btn-danger" href="{{ url('/horarios/delete/'.$horario->id) }}"
                                       role="button"
                                       onclick="return confirm('Esta seguro? Se eliminara toda la informacion relacionada a el horario. ')">
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
