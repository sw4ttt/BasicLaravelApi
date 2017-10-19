@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Horarios <span class="badge"> {{count($horarios)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Entidad</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Dia</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Grado</th>
                        <th>Lugar</th>
                        <th>Opcion</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($horarios) > 0)
                        @foreach ($horarios as $horario)
                            <tr>
                                <td>{{ $horario->entidad }}</td>
                                <td>{{ isset($horario->nombreEntidad)?$horario->nombreEntidad:"N/A" }}</td>
                                <td>{{ isset($horario->descripcion)?$horario->descripcion:"N/A" }}</td>
                                <td>{{ $horario->dia }}</td>
                                <td>{{ $horario->inicio }}</td>
                                <td>{{ $horario->fin }}</td>
                                <td>{{ $horario->grado }}</td>
                                <td>{{ $horario->lugar }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/horarios/edit/'.$horario->id) }}" role="button">Editar</a>
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
                            {{--<td>..</td>--}}
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
