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
    @yield('meta_tags')
    @yield('structured_data')
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('news') }}">News</a>
            <a href="{{ route('product') }}">Product</a>
            <a href="{{ route('contact') }}">Contact</a>
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
    <footer>
        <p>&copy; 2024 Your Website</p>
    </footer>
</body>
</html>
