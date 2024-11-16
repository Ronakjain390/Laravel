<!doctype html>
<html class="bg-white h-screen flex flex-col">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <link  href="{{ mix('css/app.min.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('image/Vector.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="{{ asset('js/components/invoiceComponent.js') }}" defer></script>

    @livewireStyles
</head>

<body class="flex-grow bg-grays">

    @yield('body')

    @livewireScripts
    <script src="{{ mix('js/app.min.js') }}" defer></script>
</body>

</html>
