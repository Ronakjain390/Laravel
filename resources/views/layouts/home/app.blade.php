<!doctype html>
<html class="bg-white">

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KF37BKG');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="preload" href="{{ asset('image/Vector.png') }}" as="image" type="image/png">
    <meta property="og:site_name" content="theparchi.com">
    <meta property="og:title" content="TheParchi" />
    <meta property="og:description"
        content="Paperless proof of delivery of your goods!! No more paper challan and signed receiving required for sending or receiving goods. Instant confirmation with digital records accessible online all the time." />
    <meta property="og:image" itemprop="image" content="{{ asset('image/Vector.png') }}">
    <meta property="og:type" content="website" />
    <meta property="og:updated_time" content="1440432930" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

    @livewireStyles
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KF37BKG" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    @yield('body')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"  crossorigin="anonymous" defer></script>
    <script>
        NProgress.configure({ showSpinner: false });
    </script>
    @livewireScripts
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>
