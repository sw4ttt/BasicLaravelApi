@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Articulo</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/articulos/edit/'.$articulo->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

            </form>
        </div>
    </div>
@endsection
