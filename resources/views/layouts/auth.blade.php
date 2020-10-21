<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ config('app.name', 'Production Monitoring System') }}</title>
    <link rel="stylesheet" href="{{ mix('/css/login.css') }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
</head>
<body class="hold-transition login-page">
    <div id="app">
        <div class="login-box">

            <div class="login-box-body">
                <div class="login-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo2.png') }}" alt="">
                    </a>
                </div>

                <h4 class="login-box-msg">Production Monitoring System</h4>

                @yield('content')

            </div>

        </div>
    </div>
    <script src="{{ mix('/js/login.js') }}"></script>
    @stack('scripts')
</body>
</html>
