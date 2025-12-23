<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>KHANG THỊNH PHÁT</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') . "?v=" .  env('APP_VERSION', 0) }}" rel="stylesheet">
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/sb-admin.css') . "?v=" .  env('APP_VERSION', 0)}}" rel="stylesheet" type="text/css">
</head>
<body id="page-top">
<div id="app">
</div>

<!-- Scripts -->
<script>
    window.Auth = {!! json_encode([
            'signedIn' => Auth::check(),
            'user' => Auth::user(),
            'isAdmin' => Auth::user()->admin,
            //'permissions' => session()->get('permissions'),
            'permissions' => Auth::user()->getPermissions(),
            'pageUrl' => route("wellcome"),
            'publicUrl' => asset('/'),
        ])!!};
</script>
<script src="{{ asset('js/app.js') . "?v=" .  env('APP_VERSION', 0) }}"></script>
</body>
</html>
