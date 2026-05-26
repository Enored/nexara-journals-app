<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="@yield('metaDescription', platform_name() . ' accelerates the dissemination of knowledge through the publication of high quality research articles using the open access model.')">
    <title>@yield('title', platform_name())</title>

    <link href="{{ asset('assets/public/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset('assets/public/css/owlcarousel/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/css/owlcarousel/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/css/vendors.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/css/icon_fonts/css/all_icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/js/slick-1.8.0/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/public/js/slick-1.8.0/slick/slick-theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

<header class="header fadeInDown animated">
    <div id="logo">
        <a href="{{ route('home') }}" style="font-size: 1.5rem; font-weight: 700; color: #333; text-decoration: none; white-space: nowrap;">Nexara Journals</a>
    </div>
    <ul id="top_menu">
        <li class="slash hidden-xs"><a href="{{ route('home') }}#journals">Journals</a></li>
        @auth
            <li class="slash hidden-xs"><a href="{{ platform_route('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ platform_route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a></li>
            <form id="logout-form" action="{{ platform_route('logout') }}" method="POST" class="hidden">@csrf</form>
        @else
            <li class="slash hidden-xs"><a href="{{ platform_route('login') }}">Login</a></li>
            <li><a href="{{ platform_route('register') }}">Register</a></li>
        @endauth
        <li><a href="#" class="search-overlay-menu-btn">Search</a></li>
        <li>
            <div class="hamburger hamburger--spin">
                <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                </div>
            </div>
        </li>
    </ul>

    <div class="search-overlay-menu">
        <span class="search-overlay-close"><span class="closebt"><i class="ti-close"></i></span></span>
        <form role="search" id="searchform" action="{{ route('home') }}" method="get">
            <input value="" name="q" type="search" placeholder="Search..." />
            <button type="submit"><i class="icon_search"></i></button>
        </form>
    </div>
</header>

<div id="main_menu">
    <div class="container">
        <nav class="version_2">
            <div class="row">
                <div class="col-md-3">
                    <h3><span></span>Home</h3>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                    </ul>
                    <h3><span></span>About Us</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3><span></span>Journals</h3>
                    <ul>
                        <li><a href="{{ route('home') }}#journals">All Journals</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3><span></span>Authors</h3>
                    <ul>
                        @auth
                            <li><a href="{{ platform_route('author.submissions') }}">My Submissions</a></li>
                        @else
                            <li><a href="{{ platform_route('login') }}">Submit Manuscript</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3><span></span>Contact Us</h3>
                    <ul>
                        <li><a href="#">Support Center</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

<main>
    @yield('content')
</main>

<footer>
    <div class="footer_padding_custom">
        <div class="row">
            <div class="col-lg-3 col-md-3 ml-lg-auto">
                <h5>Authors</h5>
                <ul class="links">
                    @auth
                        <li><a href="{{ platform_route('author.submissions') }}">My Submissions</a></li>
                    @else
                        <li><a href="{{ platform_route('login') }}">Submit Manuscript</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-lg-3 col-md-3 ml-lg-auto">
                <h5>Journals</h5>
                <ul class="links">
                    <li><a href="{{ route('home') }}#journals">All Journals</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-3 ml-lg-auto">
                <h5>About</h5>
                <ul class="links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-3 ml-lg-auto">
                <h5>Policies</h5>
                <ul class="links">
                    <li><a href="#">Open Access</a></li>
                    <li><a href="#">Creative Commons</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <ul id="additional_links">
                    <li><a href="javascript:;">&copy; {{ platform_name() }} {{ date('Y') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('assets/public/js/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('assets/public/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/public/css/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/public/js/slick-1.8.0/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/public/js/common_scripts.js') }}"></script>
<script src="{{ asset('assets/public/js/main.js') }}"></script>

@stack('scripts')
</body>
</html>
