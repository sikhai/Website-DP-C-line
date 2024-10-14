@extends('layouts.main')

@section('title', 'Home Page')
@section('meta_description', 'This is the home page description')
@section('meta_keywords', 'home, main, welcome')
@section('meta_image', asset('images/home-page-image.jpg'))

@section('structured_data')
    <script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Home Page",
    "description": "This is the home page description"
}
</script>
@endsection

@yield('structured_data')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@yield('styles')

@section('content')
    @include('partials.banner')
    @include('partials.our_services')
    @include('partials.trending')
    @include('partials.project')
    @include('partials.clients')
@endsection
