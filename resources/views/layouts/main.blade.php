<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description', 'Default description')">
    <meta name="keywords" content="@yield('meta_keywords', 'default, keywords')">
    <meta name="author" content="Your Name or Company">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description', 'Default description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('meta_image', asset('default-image.jpg'))">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Voyager::image(setting('site.logo')) }}">
    @yield('meta_tags')
    @yield('structured_data')
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @yield('styles')
</head>

<body>
    <main class="big-layout">
        <div class="opacity-layer" style="display: none;"></div>
        <div class="layout hidden">
            @include('partials.header')
            @yield('content')
            @include('partials.footer')
        </div>
    </main>
    @yield('loader')
    @home
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}" defer></script>
    <script src="{{ asset('js/menu-script.js') }}" defer></script>
    <script src="{{ asset('js/Scrollbanner.js?v2') }}" defer></script>
    @endhome
    @stack('scripts')
</body>

</html>
