@include('User/Layouts/Parts/HomeLayout/Links/Header')
<body>
@include('User/Layouts/Parts/HomeLayout/ContentParts/Profile')

@yield('UserContent')

@include('User/Layouts/Parts/HomeLayout/ContentParts/Footer')
</body>