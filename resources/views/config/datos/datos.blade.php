@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Manejo de Datos del Sistema <span class="badge"></span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Entidad</th>
                    <th>Id Personal</th>
                    <th>Email</th>
                    <th>Tipo</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($users) && count($users) > 0)
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->nombre }}</td>
                            <td>{{ $user->idPersonal }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->type }}</td>
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
        </div>
    </div>
@endsection
