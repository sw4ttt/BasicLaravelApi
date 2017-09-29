@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Noticias <span class="badge"> {{count($noticias)}}</span></h2></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    {{--'idUser',--}}
                    {{--'title',--}}
                    {{--'content',--}}
                    {{--'image'--}}
                    <th>Titulo</th>
                    <th>Contenido</th>
                    <th>Imagen</th>
                    {{--<th>Opcion</th>--}}
                </tr>
                </thead>
                <tbody>
                @if(count($noticias) > 0)
                    @foreach ($noticias as $noticia)
                        <tr>
                            <td>{{ $noticia->title }}</td>
                            <td>{{ $noticia->content }}</td>
                            <td><a href="{{ $noticia->image }}">Imagen</a></td>
                            {{--<td>--}}
                                {{--<a class="btn btn-primary" href="{{ url('/noticias/edit/'.$noticia->id) }}" role="button">Editar</a>--}}
                            {{--</td>--}}
                        </tr>
                    @endforeach
                @else
                    <tr>
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
