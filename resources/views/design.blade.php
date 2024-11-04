@extends('layouts.main')

@section('title', 'Product Page')
@section('meta_description', 'This is the product page description')
@section('meta_keywords', 'product, item, details')
@section('meta_image', asset('images/product-page-image.jpg'))

@section('structured_data')
    <script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "Sample Product",
    "image": "https://www.yoursite.com/images/sample-product.jpg",
    "description": "This is a sample product description.",
    "sku": "12345",
    "mpn": "12345",
    "brand": {
        "@type": "Brand",
        "name": "Sample Brand"
    },
    "offers": {
        "@type": "Offer",
        "url": "https://www.yoursite.com/product/sample-product",
        "priceCurrency": "USD",
        "price": "29.99",
        "priceValidUntil": "2024-12-31",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')
    <!-- tab-general-fabric collection design -->
    @include('partials.tab_list')
    @include('design.show')
    <!-- Filter bar -->
    @include('partials.filter_bar')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/menu-script.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
