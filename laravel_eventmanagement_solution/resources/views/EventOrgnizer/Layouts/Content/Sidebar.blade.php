<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link " href="{{ route('orgnizerdashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-journal-text"></i><span>Artists</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('OrganizerArtistExpertize') }}">
                        <i class="bi bi-circle"></i><span>Artist Expertize</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ArtistsOrganizer') }}">
                        <i class="bi bi-circle"></i><span>Artists</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('FoodStoresOrganizer') }}">
                <i class="bi bi-person"></i>
                <span>Food Store</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('listevents') }}">
                <i class="bi bi-person"></i>
                <span>Events</span>
            </a>
        </li>
        <!-- End Profile Page Nav -->

        <!-- End Profile Page Nav -->

    </ul>

</aside>