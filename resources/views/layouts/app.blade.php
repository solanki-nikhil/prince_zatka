@php
Route::get('/clear-cache', function() {
Artisan::call('cache:clear');
});
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height: auto;">

<head>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Styles -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('image/favicon.png')}}">
    <!-- Fonts -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/fontawesome.css')}}">
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-ecommerce.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/extensions/ext-component-toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/extensions/ext-component-sweet-alerts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/jquery/jquery.fancybox.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/jquery-ui/css/jquery-ui.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/lib/css/select2.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/lib/css/bootstrap-select.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/style.css')}}">
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="navbar-brand" href="#">
                        <span class="brand-logo">
                            <img class="top-logo" src="{{asset('image/logo-one.png')}}">
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
                <ul class="nav navbar-nav">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link nav-link-style">
                            <i class="ficon" data-feather="moon"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name fw-bolder">{{Auth::user()->name}}</span>
                            <span class="user-status">{{ucfirst(Auth::user()->roles[0]->name)}}</span>
                        </div>
                        <span class="avatar">
                            <img class="round" src="{{asset('image/user.png')}}" alt="avatar" height="40" width="40">
                            <span class="avatar-status-online"></span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <!-- Change Password Option -->
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="me-50" data-feather="user"></i> View Profile
                        </a>

                        <!-- Logout Option -->
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="me-50" data-feather="power"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>

                    <!-- <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();"><i class="me-50" data-feather="power"></i> Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div> -->
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="horizontal-menu-wrapper">
        <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border container-xxl" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item me-auto">
                        <a class="navbar-brand" href="{{route('home')}}">
                            <span class="brand-logo">
                                <img class="top-logo" src="{{asset('image/logo-one.png')}}">
                            </span>
                        </a>
                    </li>
                    <li class="nav-item nav-toggle">
                        <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                            <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <div class="navbar-container main-menu-content" data-menu="menu-container">
                <ul class="nav navbar-nav d-flex justify-content-center" id="main-menu-navigation" data-menu="menu-navigation">

                    @if(Auth::user()->roles[0]->name == 'customer')
                    <li class="nav-item @if(Request::segment(1) == 'customer' && Request::segment(2) == 'dashboard'): active @endif">
                        <a href="{{ route('customer.dashboard') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="home"></i>
                            <span data-i18n="Dashboards">Dashboard</span>
                        </a>
                    </li>
                    <!-- customer order -->
                    <!-- <li class="nav-item @if(Request::segment(1) == 'customer' && Request::segment(2) == 'order'): active @endif">
                        <a href="{{ route('customerorder') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="shopping-bag"></i>
                            <span data-i18n="customerorder">Customer Order</span>
                        </a>
                    </li> -->

                    <li class="dropdown nav-item nav-item @if(Request::segment(1) == 'customer' && Request::segment(2) == 'order' ): active @endif" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="shopping-bag"></i>
                            <span data-i18n="customerorder">Customer Order</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li data-menu="" class="@if(Route::currentRouteName() == 'customerorder'  && !request('outofstock')): active @endif">
                                <a href="{{ route('customerorder') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="layers"></i>
                                    <span data-i18n="all">All Orders</span>
                                </a>
                            </li>
                            <li data-menu="" class="@if(Route::currentRouteName() == 'customerorder' && request('outofstock') == 'true'): active @endif">
                                <a href="{{ route('customerorder', ['outofstock' => 'true']) }}" class="dropdown-item d-flex align-items-center">
                                    <i data-feather="package"></i>
                                    <span data-i18n="outofstock">Replace Orders</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item @if(Request::segment(1) == 'customer' && Request::segment(2) == 'media' ): active @endif">
                        <a href="{{ route('customermedia') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="film"></i>
                            <span data-i18n="customermedia">Media</span>
                        </a>
                    </li>
                    <li class="nav-item @if(Request::segment(1) == 'warranty' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('warranty.add') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="gift"></i>
                            <span data-i18n="serialno">Add/Check Warrenty</span>
                        </a>
                    </li>
                    <li class="dropdown nav-item @if(Request::segment(1) == 'serialno' || Request::segment(1) == '' ): active @endif" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="layers"></i>
                            <span data-i18n="serialno">Serial No.</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <!-- <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.index'): active @endif">
                                <a href="{{ route('serialno.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="layers"></i>
                                    <span data-i18n="all">All</span>
                                </a>
                            </li> -->
                            <!-- <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.replaced'): active @endif">
                                <a href="{{ route('serialno.replaced') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="package"></i>
                                    <span data-i18n="replaced">Replaced</span>
                                </a>
                            </li> -->
                            <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.rejected'): active @endif">
                                <a href="{{ route('serialno.rejected') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="x-circle"></i>
                                    <span data-i18n="rejected">Rejected</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @else
                    <li class="nav-item @if(Request::segment(1) == 'home' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('home') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="home"></i>
                            <span data-i18n="Dashboards">Dashboards</span>
                        </a>
                    </li>

                    <li class="dropdown nav-item" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="grid"></i>
                            <span data-i18n="Forms &amp; Tables">Masters</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <!-- <li data-menu="" class="nav-item @if(Request::segment(1) == 'slider' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('slider.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="sliders"></i>
                                    <span data-i18n="slider">Slider</span>
                                </a>
                            </li> -->
                            <li data-menu="" class="nav-item @if(Request::segment(1) == 'status-master' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('status-master.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="truck"></i>
                                    <span data-i18n="status">Status</span>
                                </a>
                            </li>
                            <li data-menu="" class="nav-item @if(Request::segment(1) == 'country' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('country.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="flag"></i>
                                    <span data-i18n="country">Area</span>
                                </a>
                            </li>
                            <!-- <li data-menu="" class="nav-item @if(Request::segment(1) == 'state' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('state.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="square"></i>
                                    <span data-i18n="state">State</span>
                                </a>
                            </li>
                            <li data-menu="" class="@if(Request::segment(1) == 'city' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('city.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="box"></i>
                                    <span data-i18n="city">City</span>
                                </a>
                            </li> -->
                        </ul>
                    </li>

                    <li class="dropdown nav-item" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="layout"></i>
                            <span>Products</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li data-menu="" class="@if(Request::segment(1) == 'category' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('category.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="stop-circle"></i>
                                    <span data-i18n="category">Category</span>
                                </a>
                            </li>
                            <!-- <li data-menu="" class="@if(Request::segment(1) == 'sub-category' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('sub-category.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="target"></i>
                                    <span data-i18n="sub-category">Sub Category</span>
                                </a>
                            </li> -->
                            <li data-menu="" class="@if(Request::segment(1) == 'product' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('product.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="shopping-cart"></i>
                                    <span data-i18n="product">Product</span>
                                </a>
                            </li>
                            <!-- <li data-menu="" class="nav-item @if(Request::segment(1) == 'stock-master' || Request::segment(1) == '' ): active @endif">
                                <a href="{{ route('stock-master.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="database"></i>
                                    <span data-i18n="status">Stock</span>
                                </a>
                            </li> -->
                        </ul>
                    </li>

                    <!-- <li class="nav-item @if(Request::segment(1) == 'order' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('order.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="shopping-bag"></i>
                            <span data-i18n="order">Order</span>
                        </a>
                    </li> -->

                    <li class="dropdown nav-item nav-item @if(Request::segment(1) == 'order' || Request::segment(1) == '' ): active @endif" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="shopping-bag"></i>
                            <span data-i18n="order">Order</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li data-menu="" class="@if(Route::currentRouteName() == 'order.index'  && !request('outofstock')): active @endif">
                                <a href="{{ route('order.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="layers"></i>
                                    <span data-i18n="all">All Orders</span>
                                </a>
                            </li>
                            <li data-menu="" class="@if(Route::currentRouteName() == 'order.index' && request('outofstock') == 'true'): active @endif">
                                <a href="{{ route('order.index', ['outofstock' => 'true']) }}" class="dropdown-item d-flex align-items-center">
                                    <i data-feather="package"></i>
                                    <span data-i18n="outofstock">Replace Orders</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item @if(Request::segment(1) == 'media-master' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('media-master.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="film"></i>
                            <span data-i18n="media">Media</span>
                        </a>
                    </li>

                    <!-- <li class="nav-item @if(Request::segment(1) == 'qr-code' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('qr-code.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="code"></i>
                            <span data-i18n="qr-code">QR Code</span>
                        </a>
                    </li>

                    <li class="nav-item @if(Request::segment(1) == 'redeem-point' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('redeem-point.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="gift"></i>
                            <span data-i18n="redeem">Redeem</span>
                        </a>
                    </li> -->

                    <li class="nav-item @if(Request::segment(1) == 'user' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('user.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="users"></i>
                            <span data-i18n="users">User</span>
                        </a>
                    </li>

                    <!-- <li class="nav-item @if(Request::segment(1) == 'serialno' || Request::segment(1) == '' ): active @endif">
                        <a href="{{ route('serialno.index') }}" class="nav-link d-flex align-items-center">
                            <i data-feather="gift"></i>
                            <span data-i18n="serialno">Serial No.</span>
                        </a>
                    </li> -->

                    <li class="dropdown nav-item @if(Request::segment(1) == 'serialno' || Request::segment(1) == '' ): active @endif" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i data-feather="gift"></i>
                            <span data-i18n="serialno">Serial No.</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.index'): active @endif">
                                <a href="{{ route('serialno.index') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="layers"></i>
                                    <span data-i18n="all">All</span>
                                </a>
                            </li>
                            <!-- <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.replaced'): active @endif">
                                <a href="{{ route('serialno.replaced') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="package"></i>
                                    <span data-i18n="replaced">Replaced</span>
                                </a>
                            </li> -->
                            <li data-menu="" class="@if(Route::currentRouteName() == 'serialno.rejected'): active @endif">
                                <a href="{{ route('serialno.rejected') }}" class="dropdown-item d-flex align-items-center" data-bs-toggle="">
                                    <i data-feather="x-circle"></i>
                                    <span data-i18n="rejected">Rejected</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @endif
                </ul>
            </div>
        </div>
    </div>
    <!-- END: Main Menu-->
    @yield('content')
    <!-- BEGIN: Footer-->
    <!-- <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2023<a class="ms-25" href="https://dndsoftware.in/" target="_blank">D&D Software.</a>
                <span class="d-none d-sm-inline-block"> All Rights Reserved.</span>
            </span>
        </p>
    </footer> -->
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{asset('plugins/jquery-ui/js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/extensions/ext-component-toastr.js')}}"></script>
    <script src="{{asset('plugins/jquery/jquery.fancybox.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/lib/js/bootstrap-select.js')}}"></script>
    <script src="{{asset('app-assets/vendors/lib/js/select2.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <script src="{{ asset('js/app.js') }}"></script>


    @yield('pagescript')

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
    <!-- 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


    <!-- cart code -->
    @if(auth()->check() && auth()->user()->roles[0]->name == 'customer')
    @include('customer.cart_sidebar')



    <!-- Floating Cart Button -->
    <button id="cartButton" class="btn bg-danger text-white position-fixed d-flex align-items-center justify-content-center"
        style="top: 50%; right: 0px; transform: translateY(-50%); z-index: 1050; height: 60px; width: 60px; 
               border-radius: 30px 0 0 30px; transition: width 0.3s ease-in-out;">
        <i class="fas fa-shopping-cart fs-4"></i>
        <span id="cartText" class="ms-2 d-none">View Cart</span>
    </button>

    <!-- JavaScript for Cart Toggle & Quantity Update -->
    <script>
        document.getElementById('cartButton').addEventListener('click', function() {
            document.getElementById('cartSidebar').style.transform = 'translateX(0)';
        });

        document.getElementById('closeCart').addEventListener('click', function() {
            document.getElementById('cartSidebar').style.transform = 'translateX(100%)';
            // refresh page 
            location.reload();
        });

        // Close cart when clicking outside
        document.addEventListener('click', function(event) {
            let cartSidebar = document.getElementById('cartSidebar');
            let cartButton = document.getElementById('cartButton');

            if (!cartSidebar.contains(event.target) && !cartButton.contains(event.target)) {
                cartSidebar.style.transform = 'translateX(100%)';
            }
        });


        // $(document).on('click', '.increase-qty', function() {
        //     let input = this.parentElement.querySelector('.quantity-input');
        //     // input.value = parseInt(input.value) + 1;
        //     updateAJAX(input.dataset.id, parseInt(input.value) + 1);
        // });
        // $(document).on('click', '.decrease-qty', function() {
        //     let input = this.parentElement.querySelector('.quantity-input');
        //     if (parseInt(input.value) > 1) {
        //         // input.value = parseInt(input.value) - 1;
        //         updateAJAX(input.dataset.id, parseInt(input.value) - 1);
        //     }
        // });

        // let cartUpdateTimer;

        // $(document).on("input", ".quantity-input", function() {
        //     let input = this.parentElement.querySelector('.quantity-input');
        //     let newQuantity = parseInt(input.value); // Get updated quantity

        //     // Ensure quantity is a valid number
        //     if (newQuantity < 1 || isNaN(newQuantity)) {
        //         return;
        //     }

        //     // Clear previous timer to prevent multiple calls
        //     clearTimeout(cartUpdateTimer);

        //     // Set a delay before updating (1 second after typing stops)
        //     cartUpdateTimer = setTimeout(() => {
        //         updateAJAX(input.dataset.id, quantity);

        //     }, 1000);
        // });


        // function updateAJAX(itemId, quantity) {
        //     $.ajax({
        //         url: `/cart/update/${itemId}`,
        //         method: "POST", // Change to "PUT" if needed
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
        //             quantity: quantity
        //         },
        //         success: function(response) {
        //             console.log("Cart updated", response);

        //             // Refresh cart UI
        //             updateCartView();
        //         },
        //         error: function(xhr) {
        //             console.error("Error updating cart:", xhr.responseText);
        //         }
        //     });
        // }
        // function updateCart(itemId, quantity) {
        //     updateAJAX(itemId, quantity);

        // fetch(`/cart/update/${itemId}`, {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         body: JSON.stringify({
        //             quantity: quantity
        //         })
        //     }).then(response => response.json())
        //     .then(data => console.log('Cart updated', data));
        // }

        // Expand Button on Hover
        let cartButton = document.getElementById('cartButton');
        cartButton.addEventListener('mouseenter', function() {
            this.style.width = "150px";
            document.getElementById('cartText').classList.remove('d-none');
        });
        cartButton.addEventListener('mouseleave', function() {
            this.style.width = "60px";
            document.getElementById('cartText').classList.add('d-none');
        });

        $(document).on('click', '.remove-item', function() {
            let itemId = $(this).data('id');

            $.ajax({
                url: "{{ route('cart.remove') }}", // Ensure this route is defined
                method: "POST",
                data: {
                    id: itemId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    updateCartView(); // Refresh cart dynamically

                    updateCartTotal();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        function updateCartView() {
            $.ajax({
                url: "{{ route('cart.view') }}",
                method: "GET",
                success: function(response) {
                    let cartContent = $(response).find('.p-0.flex-grow-1').html();
                    $('#cartSidebar .p-0.flex-grow-1').html(cartContent);

                    // Ensure quantity buttons and remove buttons are re-attached
                    // attachCartEventHandlers();

                },
                error: function(xhr) {
                    console.error("Error updating cart:", xhr.responseText);
                }
            });
        }

        function updateCartTotal() {
            $.ajax({
                url: "{{ route('cart.total') }}", // Ensure this route returns the total price
                method: "GET",
                success: function(response) {
                    $('#cartTotal').text(response.total.toFixed(2));
                },
                error: function(xhr) {
                    console.error("Error updating cart total:", xhr.responseText);
                }
            });
        }

        $(document).ready(function() {

            // $('#orderButton').click(function() {
            $(document).on('click', '#orderButton', function() {
                $.ajax({
                    url: "{{ route('order.cartsubmit') }}", // Replace with your actual route
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        document.getElementById('cartSidebar').style.transform = 'translateX(100%)';
                        updateCartView(); // Refresh cart after order

                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "View Order"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/customer/order"; // Redirect to order page
                            }
                        });

                        // alert(response.message); // Show success message

                    },
                    error: function(xhr) {
                        console.error("Order failed:", xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        });
    </script>
    @endif

    <!-- Global Cart Sidebar Plus/Minus Handler -->
    <script>
    $(document).on('click', '#cartSidebar .cart-plus', function() {
        let productId = String($(this).data('id'));
        let productPrice = parseFloat($(this).data('price')) || 0;
        let boxSize = parseInt($(this).data('box')) || 1;
        let quantityElement = $(this).siblings('.cart-quantity');
        let totalElement = $(".cart-total[data-id='" + productId + "']");
        let currentQuantityRaw = quantityElement.text();
        let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
        let newQuantity = currentQuantity + boxSize;
        let newTotal = (newQuantity * productPrice).toFixed(2);
        console.log('[CART SIDEBAR GLOBAL] PLUS clicked', {productId, currentQuantity, newQuantity, boxSize});
        quantityElement.text(newQuantity);
        totalElement.text(`₹${newTotal}`);
        updateCart(productId, newQuantity);
    });

    $(document).on('click', '#cartSidebar .cart-minus', function() {
        let productId = String($(this).data('id'));
        let productPrice = parseFloat($(this).data('price')) || 0;
        let boxSize = parseInt($(this).data('box')) || 1;
        let quantityElement = $(this).siblings('.cart-quantity');
        let totalElement = $(".cart-total[data-id='" + productId + "']");
        let currentQuantityRaw = quantityElement.text();
        let currentQuantity = parseInt(currentQuantityRaw.replace(/[^0-9]/g, ''), 10) || 0;
        let newQuantity = currentQuantity - boxSize;
        let newTotal = (newQuantity * productPrice).toFixed(2);
        console.log('[CART SIDEBAR GLOBAL] MINUS clicked', {productId, currentQuantity, newQuantity, boxSize});
        if (newQuantity <= 0) {
            $(this).parent().remove();
            totalElement.text(`₹0.00`);
            updateCart(productId, 0);
        } else {
            quantityElement.text(newQuantity);
            totalElement.text(`₹${newTotal}`);
            updateCart(productId, newQuantity);
        }
    });

    function updateCart(productId, qty) {
        console.log('[CART SIDEBAR GLOBAL] updateCart called', {productId, qty});
        $.ajax({
            url: "{{ route('cart.manage') }}",
            method: "POST",
            data: {
                product_id: productId,
                quantity: qty,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log('[CART SIDEBAR GLOBAL] updateCart AJAX success', response);
                if (!$('.toast-success').length) {
                    toastr.success(response.message, 'Success');
                }
                if (typeof updateCartView === 'function') {
                    updateCartView();
                } else {
                    location.reload();
                }
            },
            error: function(xhr) {
                console.log('[CART SIDEBAR GLOBAL] updateCart AJAX error', xhr);
                if (xhr.status === 403) {
                    alert('Please log in to update your cart.');
                    window.location.href = "{{ route('login') }}";
                }
            }
        });
    }
    </script>

    <!-- Push Notification Script -->
    <script src="{{ asset('js/push-notifications.js') }}"></script>

</body>
<!-- END: Body-->

</html>