
<!Doctype html>
<html class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('image/Vector.png')}}">

    <!-- Include CKEditor -->
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="//unpkg.com/alpinejs" ></script> <!-- Include Alpine.js --> 

    @livewireStyles
</head>

<body class="h-full">

    @yield('body')

    @livewireScripts
    <script src="{{ mix('js/app.js') }}" defer></script>
    {{-- <script src="{{ mix('js/custom.js') }}"></script> --}}
</body>

</html>