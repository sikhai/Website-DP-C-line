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

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-detail.css?v=2') }}">
@endsection

@section('content')
    <section class="header-temp">
        <div class="container">
            <div class="top_head pt-3 pb-3">
                <div class="row">
                    <img src="{{ asset('images/arrow-left.svg') }}" width="55" id="img-arrow-left" alt="logo"
                        loading="lazy" onclick="back()">
                </div>
            </div>

        </div>
    </section>

    <!-- detail item -->
    <section class="detail-fabric pb-5">
        <div class="container" style="margin-top: 90px;">
            <div class="row img-detail">
                <div class="col-10 main-view">
                    <img id="main-image" src="{{ env('APP_URL') . '/storage/' . $product['image'] }}"
                        onclick="switchImage(this)" alt="{{ $product['name'] }}">
                    <div class="btn-showimg">
                        <button type="button" class="btn btn-primary" id="btn-showimg" onclick="showAllImages()">
                            <img src="{{ asset('images/icons8-grid-64.png') }}" alt="">Show all photos
                        </button>
                    </div>
                </div>
                <div class="col-2 small-view">
                    <div class="current-selected">
                        <img src="{{ env('APP_URL') . '/storage/' . $product['image'] }}" onclick="switchImage(this)"
                            alt="{{ $product['name'] }}">
                    </div>
                    @foreach (json_decode($product['images'] ?? '[]') as $item)
                        <div class="next-selected">
                            <img src="{{ Voyager::image($item) }}" onclick="switchImage(this)" alt="{{ $product['name'] }}">
                        </div>
                    @endforeach

                </div>

                <div class="fabric-photos">
                    <div class="row">
                        <img src="{{ env('APP_URL') . '/storage/' . $product['image'] }}" onclick="switchImage(this)"
                            alt="{{ $product['name'] }}">
                        @foreach (json_decode($product['images'] ?? '[]') as $item)
                            <img src="{{ Voyager::image($item) }}" onclick="switchImage(this)"
                                alt="{{ $product['name'] }}">
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row mt-3 lable-general">
                <div class="directory">
                    <a class="back-directory" href="{{ route('home') }}">Home /</a>

                    @if ($breadcrumb['category'])
                        <a class="back-directory" href="{{ route('categories.show', $breadcrumb['category']) }}">
                            {{ $breadcrumb['category']->name }}
                        </a> /
                    @endif

                    @if ($breadcrumb['collection'])
                        <a class="back-directory" href="{{ route('collections.show', $breadcrumb['collection']) }}">
                            {{ $breadcrumb['collection']->name }}
                        </a> /
                    @endif

                    @if ($breadcrumb['design'])
                        <a class="back-directory" href="{{ route('designs.show', $breadcrumb['design']) }}">
                            {{ $breadcrumb['design']->name }}
                        </a> /
                    @endif

                    <span class="current-directory">{{ $breadcrumb['product']->name }}</span>
                </div>

                <div class="d-flex mt-5 lable-fabric">
                    <div id="code">{{ $product->product_code }}</div>
                    <div id="name">{{ $product->name }}</div>
                </div>
            </div>

            <div class="row p-0" style="margin-top: 80px;">
                @if ($product->file)
                    @php
                        $filePath = null;
                        $files = json_decode($product->file, true);
                        if (is_array($files) && isset($files[0])) {
                            $filePath = $files[0];
                        }
                    @endphp
                    <div class="download">
                        @if ($filePath)
                            <div class="d-flex download-item">
                                <img src="{{ asset('images/file-download.svg') }}" alt="">
                                @if ($filePath && isset($filePath['download_link'], $filePath['original_name']))
                                    <div id="name">
                                        <a class="text-decoration-none"
                                            href="{{ Storage::url($filePath['download_link']) }}"
                                            download="{{ basename($filePath['original_name']) }}">
                                            <p>TECHNICAL DOCUMENTS</p>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="d-flex mt-3 download-item">
                            <img src="{{ asset('images/file-download.svg') }}" alt="">
                            <div id="name">
                                <p>HD IMAGES</p>
                            </div>
                        </div>
                        <div id="line"></div>
                    </div>
                @endif
                @if ($attributes)
                    @foreach ($attributes as $attribute)
                        <div class="p-0 left-description">
                            @foreach ($attribute as $key => $value)
                                <div class="content">
                                    <div class="title">
                                        <p>{{ $key }}</p>
                                    </div>
                                    <div class="description">
                                        <p>{{ $value }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- /* tab-general-fabric collection design */ -->

    @if ($products_orther->count() > 0)
        <section class="table-products position-relative">
            <div class="row">
                <div class="col-4 lable" style="margin:27px 70px 0px">
                    <p id="lable-samecollection">In the same design</p>
                </div>
            </div>
            <div class="container mt-5 mb-5">
                <div class="row">
                    @foreach ($products_orther as $item)
                        <div class="col-lg-3 w-20 fabric-item">
                            <a href="/products/{{ $item['slug'] }}">
                                <img class="img w-100" src="{{ Voyager::image($item->image) }}"
                                    alt="" loading="lazy">
                                <p class="pt-2 m-0" id="design-name">{{ $item['name'] }}</p>
                                <p class="pt-2 m-0" id="design-code">{{ $item['product_code'] }}</p>
                            </a>
                        </div>
                    @endforeach

                </div>

            </div>

            <!-- <div class="button-showmore">
                        <button type="button" class="btn btn-primary" id="btn-showmore">
                            SHOW MORE PRODUCTS
                        </button>
                    </div> -->
        </section>
    @endif
@endsection


@push('scripts')
    <script src="{{ asset('js/scripts.js') }}"></script>
@endpush