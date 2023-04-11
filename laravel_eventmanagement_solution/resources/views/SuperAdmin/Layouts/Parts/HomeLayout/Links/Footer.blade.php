    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('Assets/SuperAdmin/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('Assets/SuperAdmin/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('Assets/SuperAdmin/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('Assets/SuperAdmin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('Assets/SuperAdmin/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('Assets/SuperAdmin/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('Assets/SuperAdmin/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('Assets/SuperAdmin/js/dashboards-analytics.js') }}"></script>

    <!-- Google Address -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key={{ env('GOOGLE_ADDRESS_KEY') }}&libraries=places&callback=initAutocomplete"></script>
    <script src="{{ asset('Js/googleAddress.js') }}"></script>

    <!-- Ck Editor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>s
    <script src="{{ asset('Js/ckeEditor.js') }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('Js/SuperAdmin/SuperAdminCustom.js') }}"></script>

    <!-- Jquery  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

    <!-- Toaster Notification -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('js')

    </html>