<?php

$data = generalSetting();
$logo = '';
if ($data) {
    $logo =  $data->logo;
}

?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('superAdmin') }}" class="app-brand-link" style="width: 100px;">
            <span class="app-brand-logo demo">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">
                @if($logo == '')
                <img src="{{ asset('Assets/DefaultImage/Default_logo.png') }}" height="50px" width="200px" alt="logo">
                @else
                <img src="{{ asset('Assets/images/' .$logo) }}" height="50px" width="200px" alt="">
                @endif
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item active">
            <a href="{{ route('superAdmin') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                <div data-i18n="Misc">Artists</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('ArtistExpertize') }}" class="menu-link">
                        <div data-i18n="Error">Artist Expertize</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Artists') }}" class="menu-link">
                        <div data-i18n="Under Maintenance">Artists</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Food Store -->
        <li class="menu-item">
            <a href="{{ route('FoodStores') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Food Stores</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('TicketCategory') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Without navbar">Ticket Category</div>
            </a>
        </li>
        <!-- Events -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Events</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('EventCategory') }}" class="menu-link">
                        <div data-i18n="Without menu">Event Category</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Events') }}" class="menu-link">
                        <div data-i18n="Without navbar"> Events</div>
                    </a>
                </li>



                <!-- <li class="menu-item">
                    <a href="{{ route('EventTicket') }}" class="menu-link">
                        <div data-i18n="Without navbar">Manage Events Ticket</div>
                    </a>
                </li> -->
                <!-- <li class="menu-item">
                    <a href="{{ route('TicketListing') }}" class="menu-link">
                        <div data-i18n="Without navbar">Ticket Listing</div>
                    </a>
                </li> -->
            </ul>
        </li>

        <li class="menu-item">
            <a href="{{ route('EventOrganizer') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Without navbar">Events Organizer</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('UnVerifiedEvents') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Un-Verified Events</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pages</span>
        </li>
        <!-- Layouts -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Frontend Content</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('Cms') }}" class="menu-link">
                        <div data-i18n="Without menu">CMS Manager</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Banners') }}" class="menu-link">
                        <div data-i18n="Without navbar">Banners</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Faqs') }}" class="menu-link">
                        <div data-i18n="Container">FAQ's</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Templates') }}" class="menu-link">
                        <div data-i18n="Fluid">Templates</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings">Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('GeneralSetting') }}" class="menu-link">
                        <div data-i18n="Account">General Settings</div>
                    </a>
                </li>
                <!-- <li class="menu-item">
                    <a href="{{ route('SmtpMail') }}" class="menu-link">
                        <div data-i18n="Notifications">SMTP mail</div>
                    </a>
                </li> -->
                <li class="menu-item">
                    <a href="{{ route('SocialLinks') }}" class="menu-link">
                        <div data-i18n="Connections">Social Links</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('Currency') }}" class="menu-link">
                        <div data-i18n="Connections">Currency</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div data-i18n="Authentications">Enquiries</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('ContactEnquiry') }}" class="menu-link">
                        <div data-i18n="Basic">Contact Enquiry</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('MailSentUser') }}" class="menu-link">
                        <div data-i18n="Basic">Mail Sent User</div>
                    </a>
                </li>
            </ul>
        </li> -->
        <li class="menu-item">
            <!-- <a href="{{ route('ReviewsRatings') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Reviews and ratings</div>
            </a> -->
        </li>


        <!-- Components -->
        <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Components</span></li> -->
        <!-- Cards -->
        <!-- <li class="menu-item">
            <a href="cards-basic.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Cards</div>
            </a>
        </li> -->
        <!-- User interface -->
        <!-- <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="User interface">User interface</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="ui-accordion.html" class="menu-link">
                        <div data-i18n="Accordion">Accordion</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-alerts.html" class="menu-link">
                        <div data-i18n="Alerts">Alerts</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-badges.html" class="menu-link">
                        <div data-i18n="Badges">Badges</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-buttons.html" class="menu-link">
                        <div data-i18n="Buttons">Buttons</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-carousel.html" class="menu-link">
                        <div data-i18n="Carousel">Carousel</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-collapse.html" class="menu-link">
                        <div data-i18n="Collapse">Collapse</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-dropdowns.html" class="menu-link">
                        <div data-i18n="Dropdowns">Dropdowns</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-footer.html" class="menu-link">
                        <div data-i18n="Footer">Footer</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-list-groups.html" class="menu-link">
                        <div data-i18n="List Groups">List groups</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-modals.html" class="menu-link">
                        <div data-i18n="Modals">Modals</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-navbar.html" class="menu-link">
                        <div data-i18n="Navbar">Navbar</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-offcanvas.html" class="menu-link">
                        <div data-i18n="Offcanvas">Offcanvas</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-pagination-breadcrumbs.html" class="menu-link">
                        <div data-i18n="Pagination &amp; Breadcrumbs">Pagination &amp; Breadcrumbs</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-progress.html" class="menu-link">
                        <div data-i18n="Progress">Progress</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-spinners.html" class="menu-link">
                        <div data-i18n="Spinners">Spinners</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-tabs-pills.html" class="menu-link">
                        <div data-i18n="Tabs &amp; Pills">Tabs &amp; Pills</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-toasts.html" class="menu-link">
                        <div data-i18n="Toasts">Toasts</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-tooltips-popovers.html" class="menu-link">
                        <div data-i18n="Tooltips & Popovers">Tooltips &amp; popovers</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="ui-typography.html" class="menu-link">
                        <div data-i18n="Typography">Typography</div>
                    </a>
                </li>
            </ul>
        </li> -->

        <!-- Extended components -->
        <!-- <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-copy"></i>
                <div data-i18n="Extended UI">Extended UI</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="extended-ui-perfect-scrollbar.html" class="menu-link">
                        <div data-i18n="Perfect Scrollbar">Perfect scrollbar</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="extended-ui-text-divider.html" class="menu-link">
                        <div data-i18n="Text Divider">Text Divider</div>
                    </a>
                </li>
            </ul>
        </li> -->

        <!-- <li class="menu-item">
            <a href="icons-boxicons.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-crown"></i>
                <div data-i18n="Boxicons">Boxicons</div>
            </a>
        </li> -->

        <!-- Forms & Tables -->
        <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Forms &amp; Tables</span></li> -->
        <!-- Forms -->
        <!-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Form Elements</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="forms-basic-inputs.html" class="menu-link">
                        <div data-i18n="Basic Inputs">Basic Inputs</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="forms-input-groups.html" class="menu-link">
                        <div data-i18n="Input groups">Input groups</div>
                    </a>
                </li>
            </ul>
        </li> -->
        <!-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Layouts">Form Layouts</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="form-layouts-vertical.html" class="menu-link">
                        <div data-i18n="Vertical Form">Vertical Form</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="form-layouts-horizontal.html" class="menu-link">
                        <div data-i18n="Horizontal Form">Horizontal Form</div>
                    </a>
                </li>
            </ul>
        </li> -->
        <!-- Tables -->
        <!-- <li class="menu-item">
            <a href="tables-basic.html" class="menu-link">
                <i class="menu-icon tf-icons bx bx-table"></i>
                <div data-i18n="Tables">Tables</div>
            </a>
        </li> -->
        <!-- Misc -->
        <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
        <li class="menu-item">
            <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">Support</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Documentation">Documentation</div>
            </a>
        </li> -->
    </ul>
</aside>