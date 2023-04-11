@include('EventOrgnizer/Layouts/Links/HeaderLink')

<body>

    <!-- ======= Header ======= -->
    @include('EventOrgnizer/Layouts/Content/Header')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('EventOrgnizer/Layouts/Content/Sidebar')
    <!-- End Sidebar-->

    <!-- ======= Main ======= -->
    <main id="main" class="main">
        @yield('content')
    </main>
    <!-- End main -->

    <!-- ======= Footer ======= -->
    @include('EventOrgnizer/Layouts/Content/Footer')
    <!-- End Footer -->

    @include('EventOrgnizer/Layouts/Links/FooterLink')
</body>

</html>