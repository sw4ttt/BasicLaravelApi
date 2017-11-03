@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Usuarios <span class="badge"> {{count($users)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Id Personal</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Opción</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($users) > 0)
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->nombre }}</td>
                                <td>{{ $user->idPersonal }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->type }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ url('/users/edit/'.$user->id) }}" role="button">Editar</a>
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
