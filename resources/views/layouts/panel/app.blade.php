<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
  <link rel="preload" href="{{ asset('image/Vector.png') }}" as="image" type="image/png">
  
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2" defer></script>
  <style>
    [x-cloak] { display: none !important; }
  </style>
  @vite('resources/css/app.css')
  @livewireStyles
</head>
<body>
  @yield('body')
  @livewireScripts
  
  <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
