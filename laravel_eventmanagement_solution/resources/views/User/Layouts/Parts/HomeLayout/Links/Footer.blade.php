 <!-- JS here -->
 <script src="{{asset('Assets/User/js/vendor/modernizr-3.5.0.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/vendor/jquery-1.12.4.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/popper.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/bootstrap.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/owl.carousel.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/isotope.pkgd.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/ajax-form.js')}}"></script>
 <script src="{{asset('Assets/User/js/waypoints.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.counterup.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/imagesloaded.pkgd.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/scrollIt.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.scrollUp.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/wow.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/gijgo.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/nice-select.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.slicknav.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.magnific-popup.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/tilt.jquery.js')}}"></script>
 <script src="{{asset('Assets/User/js/plugins.js')}}"></script>
 <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

 <!--contact js-->
 <script src="{{asset('Assets/User/js/contact.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.ajaxchimp.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.form.js')}}"></script>
 <script src="{{asset('Assets/User/js/jquery.validate.min.js')}}"></script>
 <script src="{{asset('Assets/User/js/mail-script.js')}}"></script>

 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
 <script src="{{asset('Assets/User/js/main.js')}}"></script>
 <script src="{{ asset('Js/googleAddress.js') }}"></script>
 <script src="{{asset('Js/User/UserCustom.js')}}"></script>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 <!-- Google Address -->
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key={{ env('GOOGLE_ADDRESS_KEY') }}&libraries=places&callback=initAutocomplete"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 @stack('js')

 </html>