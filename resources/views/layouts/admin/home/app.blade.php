<!doctype html>
<html class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="preload" href="{{ asset('image/Vector.png') }}" as="image" type="image/png">
    @livewireStyles
</head>

<body class="h-full">
    @yield('body')
    @livewireScripts
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>

