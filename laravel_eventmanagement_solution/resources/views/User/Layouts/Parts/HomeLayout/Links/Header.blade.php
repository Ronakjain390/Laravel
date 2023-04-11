<?php
$generalSetting = generalSetting();
?>
<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <title>Addweb Event-Manager</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    @if(isset($favicon)==1)
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('Assets/SuperAdmin/img/GeneralSetting/' .$generalSetting->favicon )}}">
    @endif
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="{{asset('Assets/User/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/gijgo.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('Assets/User/css/slicknav.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{asset('Assets/User/css/style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('css')
    <link rel="stylesheet" href="{{ asset('CSS/User/UserCustom.css') }}">
</head>