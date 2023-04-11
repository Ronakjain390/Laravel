@include('SuperAdmin/Layouts/Parts/HomeLayout/Links/Header')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('SuperAdmin/Layouts/Parts/HomeLayout/ContentParts/Sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('SuperAdmin/Layouts/Parts/HomeLayout/ContentParts/Navbar')
                <!-- / Navbar -->
                
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('SuperAdminContent')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('SuperAdmin/Layouts/Parts/HomeLayout/ContentParts/Footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
</body>

@include('SuperAdmin/Layouts/Parts/HomeLayout/Links/Footer')