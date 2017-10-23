<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="manifest" href="/manifest.json">
    {{--<script src="{{ url('/') }}/js/jquery-3.2.1.js"></script>--}}
    {{--<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>--}}


    {{--<script src="{{ url('/') }}/js/jquery-3.2.1.js"></script>--}}
    {{--<script src="{{ url('/') }}/js/loadingoverlay.min.js"></script>--}}

    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script>
    {{--<script src="{{ url('/') }}/js/loadingoverlay.min.js"></script>--}}
    <script>
      var OneSignal = window.OneSignal || [];
      OneSignal.push(["init", {
        appId: "9c9c55f8-cd80-461f-94b5-557e9204c8f2",
        autoRegister: false,
        notifyButton: {
          enable: true /* Set to false to hide */
        }
      }]);

      OneSignal.push(function() {
        OneSignal.sendTag("userType", "web");
        OneSignal.getUserId().then(function(userId) {
          console.log("OneSignal User ID:", userId);
          // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316
        });
      });
    </script>
    <!-- Styles -->
    <link href="{{ url('/') }}/css/app.css" rel="stylesheet">
    <link href="{{ url('/') }}/css/sidebar.css" rel="stylesheet">

    <link href="{{ url('/') }}/css/jquery.timepicker.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <row>
                    <div class="col-md-2">
                        <img src="{{asset("images/LOGO_COLEGIO.png")}}" class="pull-left" alt="imagen-articulo" style="width:70px;height:70px;">
                    </div>
                    <div class="col-md-10">
                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar">A</span>
                            <span class="icon-bar">B</span>
                            <span class="icon-bar">C</span>
                        </button>

                        <!-- Branding Image -->

                        <a class="navbar-brand" href="{{ url('/') }}" style="text-align: center">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                </row>


            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        {{--<li><a href="{{ url('/register') }}">Register</a></li>--}}
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->email }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-left: 20px;">
        <div class="row">
            @if (!Auth::guest())
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <nav class="navbar navbar-default sidebar" role="navigation">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-sidebar-navbar-collapse-1">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
                                <ul class="nav navbar-nav">
                                    <li class=""><a href="{{URL::to('home')}}"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-home"></span>Home</a></li>
                                    <li class=""><a href="{{URL::to('orders')}}"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-usd"></span>Ordenes de Pago</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-education"></span>Materias <span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('materias')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('materias/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-pencil"></span>Calificaciones <span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('calificaciones')}}">Listado</a></li>
{{--                                            <li style="padding-left:20px;"><a href="{{URL::to('materias/add')}}">Crear</a></li>--}}
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-shopping-cart"></span>Tienda y Pagables<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('articulos')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('articulos/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-calendar"></span>Horarios<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('horarios')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('horarios/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-book"></span>Materiales<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('materiales')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('materiales/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-user"></span>Usuarios <span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('users')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('users/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                            {{--<li><a href="#">Reportar</a></li>--}}
                                            {{--<li class="divider"></li>--}}
                                            {{--<li><a href="#">Separated link</a></li>--}}
                                            {{--<li class="divider"></li>--}}
                                            {{--<li><a href="#">Noticias</a></li>--}}
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-comment"></span>Notificaciones<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            {{--<li><a href="{{URL::to('materiales')}}">Listado</a></li>--}}
                                            <li style="padding-left:20px;"><a href="{{URL::to('notificaciones/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-picture"></span>Imagenes App<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            {{--<li><a href="{{URL::to('materiales')}}">Listado</a></li>--}}
                                            <li style="padding-left:20px;"><a href="{{URL::to('config/images-app/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                        </ul>
                                    </li>

                                    <li class="dropdown" >
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span style="font-size:16px;padding-right: 10px;" class="pull-left hidden-xs showopacity glyphicon glyphicon-inbox"></span>Noticias<span class="caret"></span></a>
                                        <ul class="dropdown-menu forAnimate" role="menu">
                                            <li style="padding-left:20px;"><a href="{{URL::to('noticias')}}">Listado</a></li>
                                            <li style="padding-left:20px;"><a href="{{URL::to('noticias/add')}}">Crear</a></li>
                                            {{--<li><a href="#">Modificar</a></li>--}}
                                            {{--<li><a href="#">Reportar</a></li>--}}
                                            {{--<li class="divider"></li>--}}
                                            {{--<li><a href="#">Separated link</a></li>--}}
                                            {{--<li class="divider"></li>--}}
                                            {{--<li><a href="#">Noticias</a></li>--}}
                                        </ul>
                                    </li>
                                    {{--<li ><a href="#">Noticias<span style="font-size:16px;" class="pull-right hidden-xs showopacity glyphicon glyphicon-th-list"></span></a></li>--}}
                                    {{--<li ><a href="#">Noticias<span style="font-size:16px;" class="pull-right hidden-xs showopacity glyphicon glyphicon-tags"></span></a></li>--}}
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            @endif
            <div class="col-xs-9 col-sm-9 col-md-9">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ url('/') }}/js/app.js"></script>
    <script src="{{ url('/') }}/js/loadingoverlay.min.js"></script>
    <script src="{{ url('/') }}/js/jquery.timepicker.min.js"></script>
    <script src="{{ url('/') }}/js/lodash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script>
      jQuery.LoadingOverlay("show");
    </script>
    <script>
      jQuery(document).ready(function() {
        jQuery.LoadingOverlay("hide",true);
      });
    </script>

    @stack('userType')
    @stack('notificacionesGrupo')
    @stack('horarioSelectorEntidad')
    @stack('userTlf')
    @stack('horarioInicio')
    @stack('horarioInputHoras')
    @stack('calificacionEditar')
    @stack('calificacionesValidate')
</body>
</html>
