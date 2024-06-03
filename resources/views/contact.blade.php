@extends('layouts.main')

@section('title', 'Contact Page')
@section('meta_description', 'This is the contact page description')
@section('meta_keywords', 'contact, reach out, support')
@section('meta_image', asset('images/contact-page-image.jpg'))

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Contact Page",
    "description": "This is the contact page description"
}
</script>
@endsection

@section('content')
    <h1>Contact Page</h1>
    <p>This is the contact page.</p>
@endsection
