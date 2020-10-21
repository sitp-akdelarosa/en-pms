<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(Auth::check())
    <meta name="user_id" content="{{ Auth::user()->id }}">
    @endif

    <title>@yield('title') | {{ config('app.name', 'Production Monitoring System') }}</title>

    @stack('styles')
    <link rel="stylesheet" href="{{ mix('/css/main.css') }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="app" class="wrapper">

        <div class="loadingOverlay"></div>
        
        @include('includes.layout.header')

        @include('includes.layout.sidebar')


        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right d-none d-sm-inline-block">
            </div>Copyright &copy; {{ date('Y') }} All Rights Reserved.
        </footer>

        @include('includes.modals.system-modals')

    </div>

    <script type="text/javascript">
        var getAuditTrailDataURL = "{{ url('/admin/audit-trail/get-data') }}";
    </script>

    <script src="{{ mix('/js/main.js') }}"></script>
    @stack('scripts')

</body>

</html>