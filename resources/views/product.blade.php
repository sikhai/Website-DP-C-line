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
    <h1>Product Page</h1>
    <p>This is the product page.</p>
    <div>
        <h2>Sample Product</h2>
        <img src="{{ asset('images/sample-product.jpg') }}" alt="Sample Product">
        <p>This is a sample product description. It includes details about the product, pricing, and availability.</p>
    </div>
@endsection
