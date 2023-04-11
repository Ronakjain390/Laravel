@include('User/Layouts/Parts/HomeLayout/Links/Header')

<body>
    @include('User/Layouts/Parts/HomeLayout/ContentParts/Header')

        @yield('UserContent')

    @include('User/Layouts/Parts/HomeLayout/ContentParts/Footer')
</body>
@include('User/Layouts/Parts/HomeLayout/Links/Footer')