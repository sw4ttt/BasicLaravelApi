@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading"><h2>Editar Horario</h2></div>
        <?php var_dump($errors->all()) ?>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/horarios/edit/'.$horario->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('entidad') ? ' has-error' : '' }}">
                    <label for="entidad" class="col-md-4 control-label">Entidad</label>
                    <div class="col-md-6">
                        <select id="entidad" name="entidad" class="form-control" required>
                            <option {{($horario->entidad === 'MATERIA')?"SELECTED":""}} value="MATERIA">Materia</option>
                            <option {{($horario->entidad === 'PROFESOR')?"SELECTED":""}} value="PROFESOR">Profesor</option>
                            <option {{($horario->entidad === 'GENERAL')?"SELECTED":""}} value="GENERAL">General</option>
                        </select>
                        @if ($errors->has('entidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('entidad') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div id="selectorProfesor" style="display: none;">
                    <div class="form-group{{ $errors->has('idEntidadProfesor') ? ' has-error' : '' }}">
                        <label for="idEntidadProfesor" class="col-md-4 control-label">Profesor</label>
                        <div class="col-md-6">
                            <select id="idEntidadProfesor" name="idEntidadProfesor" class="form-control" required>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach ($profesores as $profesor)
                                        <option {{($horario->idEntidad === (string)$profesor->id)?"SELECTED":""}} value="{{$profesor->id}}">{{$profesor->id}} - {{$profesor->nombre}}</option>
                                    @endforeach
                                @else
                                    <option>-</option>
                                @endif
                            </select>
                            @if ($errors->has('idEntidadProfesor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('idEntidadProfesor') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="selectorMateria">
                    <div class="form-group{{ $errors->has('idEntidadMateria') ? ' has-error' : '' }}">
                        <label for="idEntidadMateria" class="col-md-4 control-label">Materia</label>
                        <div class="col-md-6">
                            <select id="idEntidadMateria" name="idEntidadMateria" class="form-control" required>
                                @if(isset($materias) && count($materias) > 0)
                                    @foreach ($materias as $materia)
                                        <option {{($horario->idEntidad === ((string)$materia->id))?"SELECTED":""}} value="{{$materia->id}}">{{$materia->id}} - {{$materia->nombre}}</option>
                                    @endforeach
                                @else
                                    <option>-</option>
                                @endif
                            </select>
                            @if ($errors->has('idEntidadMateria'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('idEntidadMateria') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="opcionalDescripcion" style="display: none;">
                    <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                        <div class="col-md-6">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}">
                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('grado') ? ' has-error' : '' }}">
                        <label for="grado" class="col-md-4 control-label">Grado</label>
                        <div class="col-md-6">
                            <select id="grado" name="grado" class="form-control" required>
                                <option {{($horario->grado === '1')?"SELECTED":""}} value="1">Primero</option>
                                <option {{($horario->grado === '2')?"SELECTED":""}} value="2">Segundo</option>
                                <option {{($horario->grado === '3')?"SELECTED":""}} value="3">Tercero</option>
                                <option {{($horario->grado === '4')?"SELECTED":""}} value="4">Cuarto</option>
                                <option {{($horario->grado === '5')?"SELECTED":""}} value="5">Quinto</option>
                                <option {{($horario->grado === '6')?"SELECTED":""}} value="6">Transición</option>
                            </select>

                            @if ($errors->has('grado'))
                                <span class="help-block">
                                <strong>{{ $errors->first('grado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('dia') ? ' has-error' : '' }}">
                    <label for="dia" class="col-md-4 control-label">Dia</label>
                    <div class="col-md-6">
                        <select id="dia" name="dia" class="form-control" required>
                            <option {{($horario->dia === 'LUNES')?"SELECTED":""}} value="LUNES">Lunes</option>
                            <option {{($horario->dia === 'MARTES')?"SELECTED":""}} value="MARTES">Martes</option>
                            <option {{($horario->dia === 'MIERCOLES')?"SELECTED":""}} value="MIERCOLES">Miercoles</option>
                            <option {{($horario->dia === 'JUEVES')?"SELECTED":""}} value="JUEVES">Jueves</option>
                            <option {{($horario->dia === 'VIERNES')?"SELECTED":""}} value="VIERNES">Viernes</option>
                            <option {{($horario->dia === 'SABADO')?"SELECTED":""}} value="SABADO">Sabado</option>
                            <option {{($horario->dia === 'DOMINGO')?"SELECTED":""}} value="DOMINGO">Domingo</option>
                        </select>

                        @if ($errors->has('dia'))
                            <span class="help-block">
                                <strong>{{ $errors->first('dia') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('inicio') ? ' has-error' : '' }}">
                    <label for="inicio" class="col-md-4 control-label">inicio</label>
                    <div class="col-md-6">
                        <input id="inicio" type="text" data-time-format="H:i" class="form-control" name="inicio" value="{{ old('inicio')?old('inicio'):$horario->inicio }}" required>
                        @if ($errors->has('inicio'))
                            <span class="help-block">
                                <strong>{{ $errors->first('inicio') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('fin') ? ' has-error' : '' }}">
                    <label for="fin" class="col-md-4 control-label">fin</label>
                    <div class="col-md-6">
                        <input id="fin" type="text" data-time-format="H:i" class="form-control" name="fin" value="{{ old('fin')?old('fin'):$horario->fin }}" required>
                        @if ($errors->has('fin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fin') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('lugar') ? ' has-error' : '' }}">
                    <label for="lugar" class="col-md-4 control-label">lugar</label>
                    <div class="col-md-6">
                        <input id="lugar" type="text" class="form-control" name="lugar" value="{{ old('lugar')?old('lugar'):$horario->lugar }}" required>
                        @if ($errors->has('lugar'))
                            <span class="help-block">
                                <strong>{{ $errors->first('lugar') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Editar Horario
                        </button>
                        @if (Session::has('message'))
                            <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                <strong>{{ Session::get('message') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/horarios/delete/'.$horario->id) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Estas seguro que deseas eliminar el horario? no se pueden revertir los cambios.')">
                            Elimiar Horario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('horarioInicio')
    <script>
      jQuery(document).ready(function($) {
        var selectedText = $("#entidad").val();
        switch (selectedText) {
          case "PROFESOR":{
            $( "#selectorProfesor" ).show( "fast");
            $( "#selectorMateria" ).hide( "fast");
            $( "#opcionalDescripcion" ).hide( "fast");
          }
            break;
          case "MATERIA":{
            $( "#selectorProfesor" ).hide( "fast");
            $( "#selectorMateria" ).show( "fast");
            $( "#opcionalDescripcion" ).hide( "fast");

          }
            break;
          case "GENERAL":{
            $( "#selectorProfesor" ).hide( "fast");
            $( "#selectorMateria" ).hide( "fast");
            $( "#opcionalDescripcion" ).show( "fast");
          }
            break;
          default:{
            $( "#selectorProfesor" ).hide( "fast");
            $( "#selectorMateria" ).hide( "fast");
          }
        }
      })
    </script>
    @endpush
    @push('horarioSelectorEntidad')
    <script>
      jQuery(document).ready(function($) {
        $( "#entidad" ).change(function() {
          var selectedText = $(this).val();
          switch (selectedText) {
            case "PROFESOR":{
              $( "#selectorProfesor" ).show( "slow");
              $( "#selectorMateria" ).hide( "slow");
              $( "#opcionalDescripcion" ).hide( "slow");
            }
              break;
            case "MATERIA":{
              $( "#selectorProfesor" ).hide( "slow");
              $( "#selectorMateria" ).show( "slow");
              $( "#opcionalDescripcion" ).hide( "slow");

            }
              break;
            case "GENERAL":{
              $( "#selectorProfesor" ).hide( "slow");
              $( "#selectorMateria" ).hide( "slow");
              $( "#opcionalDescripcion" ).show( "slow");
            }
              break;
            default:{
              $( "#selectorProfesor" ).hide( "slow");
              $( "#selectorMateria" ).hide( "slow");
            }
          }
        })
      })
    </script>
    @endpush

    @push('horarioInputHoras')
    <script>
      jQuery(document).ready(function($) {
        $('#inicio').timepicker({'disableTextInput':true,'step': 5,'scrollDefault': 'now'});
        $('#fin').timepicker({'disableTextInput':true,'step': 5,'scrollDefault': 'now'});
      })
    </script>
    @endpush
@endsection
