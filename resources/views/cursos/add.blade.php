@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Crear Curso</h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/cursos/add') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('seccion') ? ' has-error' : '' }}">
                    <label for="seccion" class="col-md-4 control-label">Seccion</label>
                    <div class="col-md-6">
                        <input id="seccion" type="text" class="form-control" name="seccion" value="{{ old('seccion') }}" data-validation="length" data-validation-length="1-5" data-validation-optional="true" required autofocus>
                        @if ($errors->has('seccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seccion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('cupos') ? ' has-error' : '' }}">
                    <label for="cupos" class="col-md-4 control-label">Cupos</label>
                    <div class="col-md-6">
                        <input id="cupos" type="text" class="form-control" name="cupos" value="{{ old('cupos') }}" data-validation="number" required>
                        @if ($errors->has('cupos'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cupos') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                    <label for="grado" class="col-md-4 control-label">Grado</label>
                    <div class="col-md-6">
                        {{--<input id="grado" type="text" class="form-control" name="grado" value="{{ old('grado') }}">--}}

                        <select id="grado" name="grado" class="form-control" required>
                            <option value="1">Primero</option>
                            <option value="2">Segundo</option>
                            <option value="3">Tercero</option>
                            <option value="4">Cuarto</option>
                            <option value="5">Quinto</option>
                            <option value="6">Sexto</option>
                            <option value="7">Septimo</option>
                            <option value="8">Octavo</option>
                            <option value="9">Noveno</option>
                            <option value="10">Decimo</option>
                            <option value="11">Pre-Jardin</option>
                            <option value="12">Jardin</option>
                            <option value="13">Transicion</option>
                            <option value="14">Parvulo</option>
                        </select>

                        @if ($errors->has('grado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary save" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Curso
                        </button>
                        @if (Session::has('message'))
                            <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>{{ Session::get('message') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
