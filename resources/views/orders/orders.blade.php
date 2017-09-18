@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Ordenes</h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cod Referencia</th>
                        <th>Id Usuario</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($orders) > 0)
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->reference }}</td>
                                <td>{{ $order->user_id }}</td>
                                <td>{{ $order->user_name }}</td>
                                <td>{{ $order->state }}</td>
                                <td>{{ $order->value }}</td>
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
