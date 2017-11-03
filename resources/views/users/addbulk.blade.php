@extends('layouts.app')

@section('content')

    <div class="panel panel-default ">
        <div class="panel-heading">
            <h2>Carga Masiva de Usuarios</h2><h4>Archivo de Ejemplo: <a href="{{url("/")."/"."files/usuarios_template.csv"}}" download="{{url("/")."/"."files/usuarios_template.csv"}}">Descargar</a></h4>
            <div>
                <h3>Consideraciones:</h3>
                <ul>
                    <li>El campo <strong>email</strong> debe ser unico.</li>
                    <li>El campo <strong>type</strong> hace referencia al tipo de usuario, las opciones son: <strong>ADMIN, PROFESOR y REPRESENTANTE</strong></li>
                    <li>Los campos <strong>tlfdomicilio</strong> y <strong>tlfcelular</strong> deben tener el formato "57123456".</li>
                    <li>El campo <strong>idpersonal</strong> debe ser unico.</li>
                    <li>En caso del usuario ser tipo "REPRESENTANTE" el campo "idpersonalestudiante" debe ser unico.</li>
                    <li>En caso del usuario ser tipo "REPRESENTANTE" el campo "grado" debe ser un numero entero del 1 al 6.</li>
                    <li>A los Usuarios creados se les coloca una clave por defecto. <strong>123456</strong></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" accept-charset="UTF-8" action="{{ url('/users/add/masivo') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('usersFile') ? ' has-error' : '' }}">
                    <label for="usersFile" class="col-md-4 control-label">Archivo de Usuarios</label>
                    <div class="col-md-6">
                        <input id="usersFile" type="file" name="usersFile" class="form-control"
                               data-validation="required mime size"
                               data-validation-allowing="application/vnd.ms-excel"
                               data-validation-max-size="4M"
                               data-validation-error-msg-mime="Solo se permiten archivos con extension .csv"
                        >
                        @if ($errors->has('usersFile'))
                            <span class="help-block">
                                <strong>{{ $errors->first('usersFile') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" style="font-size:18px;" onclick="return confirm('Estas seguro?')">
                            <span class="glyphicon glyphicon-floppy-save"></span>
                            Crear Usuarios
                        </button>

                    </div>
                </div>
            </form>
            @if (Session::has('message'))
                <div class="alert alert-success alert-dismissable fade in" style="margin-top: 10px">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ Session::get('message') }}</strong>
                </div>
            @endif
            @if(Session::has('rejected') && is_array(Session::get('rejected')) && count(Session::get('rejected')) > 0)
                <div><h2>Lineas del Archivo Rechazadas</h2></div>
                <table class="table table-striped">
                    <thead>
                        <tr style="font-size: 10px">
                            <th>Linea</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo id Personal</th>
                            <th>Id Personal</th>
                            <th>Tlf Domicilio</th>
                            <th>Tlf Celular</th>
                            <th>Direccion</th>
                            <th>Tipo de Usuario</th>
                            <th>Nombre Estudiante</th>
                            <th>ID Personal Estudiante</th>
                            <th>Grado</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 10px">
                    @if(count(Session::get('rejected')) > 0)
                        @foreach (Session::get('rejected') as $item)
                            <tr>
                                <td>{{ $item['index'] }}</td>
                                <td>{{ $item['nombre'] }}</td>
                                <td>{{ $item['email'] }}</td>
                                <td>{{ $item['tipoidpersonal'] }}</td>
                                <td>{{ $item['idpersonal'] }}</td>
                                <td>{{ $item['tlfdomicilio'] }}</td>
                                <td>{{ $item['tlfcelular'] }}</td>
                                <td>{{ $item['direccion'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['nombreestudiante'] }}</td>
                                <td>{{ $item['idpersonalestudiante'] }}</td>
                                <td>{{ $item['grado'] }}</td>
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
                            <td>..</td>
                            <td>..</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    @push('userType')
    <script>
      jQuery(document).ready(function($) {
        $( "#type" ).change(function() {
          var selectedText = $(this).val();
          if(selectedText === 'REPRESENTANTE')
            $( "#opcionalEstudiante" ).show( "slow");
          else
            $( "#opcionalEstudiante" ).hide( "slow");
        });
      });
    </script>
    @endpush

    @push('usersAddMasivo')
    <script>
      jQuery(document).ready(function($) {

//        var data;
//        function handleFileSelect(evt) {
//          jQuery.LoadingOverlay("show");
//          var file = evt.target.files[0];
//          Papa.parse(file, {
//            header: true,
//            dynamicTyping: true,
//            complete: function(results) {
//              jQuery.LoadingOverlay("hide",true);
//              data = results;
//              if(_.has(data,'errors')&&!_.isEmpty(data.errors))
//                console.log("errors=",JSON.stringify(data.errors));
//              else
//                console.log("data=",JSON.stringify(data));
//            },
//            error: function (err,file) {
//              jQuery.LoadingOverlay("hide",true);
//              console.log("err=",JSON.stringify(err));
//              console.log("file=",JSON.stringify(file));
//            }
//          });
//        }
//        $("input:file").change(handleFileSelect);
      })
    </script>
    @endpush
@endsection
