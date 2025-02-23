@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-detail.css') }}">
@endsection

<!-- detail item -->
<section class="detail-fabric pb-5">
    <div class="container" style="margin-top: 90px;">
        <div class="row img-detail">
            <div class="col-10 main-view">
                <img id="main-image" src="{{ env('APP_URL') . '/storage/' . $product['image'] }}"
                    onclick="switchImage(this)" alt="{{ $product['name'] }}">
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
        </div>

        <div class="row mt-3 lable-general">
            <div class="d-flex directory">
                <a class="back-directory" >All products</a>
                <a class="back-directory" style="cursor: default">/</a>
                @if ($collection)
                    <a class="back-directory" href="/{{ $collection->slug }}">{{ $collection->name }}</a>
                    <a class="back-directory" style="cursor: default">/</a>
                    <a class="back-directory" href="/collection">Collection</a>
                @endif
                @if (isset($product->category->name))
                    <a class="back-directory" style="cursor: default">/</a>
                    <a class="back-directory" href="{{ $product->category->slug }}">{{ $product->category->name }}</a>
                    <a class="back-directory" style="cursor: default">/</a>
                @endif
                <a class="current-directory">{{ $product['name'] }}</a>
            </div>
            <div class="d-flex mt-5 lable-fabric">
                <div id="code">{{ $product['product_code'] }}</div>
                <div id="name">{{ $product['name'] }}</div>
            </div>
            @if (isset($product->category->name))
                <button type="button" class="btn btn-secondary">{{ $product->category->name }}</button>
            @endif
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
                                        <p style="margin-top: -3px;">TECHNICAL</p>
                                        <p style="margin-top: -15px;">DOCUMENTS</p>
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
                </div>
            @endif
            @if ($attributes)
                @foreach ($attributes as $attribute)
                    <div class="p-0 left-description">
                        @foreach ($attribute as $item)
                            <div class="content">
                                <div class="title">
                                    <p>{{ isset($item['name']) ? $item['name'] : '' }}</p>
                                </div>
                                <div class="description">
                                    <p>{{ isset($item['value']) ? $item['value'] : '' }}</p>
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

@if ($products_orther)
    <section class="table-products position-relative">
        <div class="row">
            <div class="col-4" style="margin:27px 70px 0px">
                <p id="lable-samecollection">In the same collection</p>
            </div>
        </div>
        <div class="container mt-5 mb-5">
            <div class="row">
                @foreach ($products_orther as $item)
                    <div class="col-lg-3 w-20 fabric-item">
                        <a href="/products/{{ $item['slug'] }}">
                            <img class="img w-100" src="{{ env('APP_URL') . '/storage/' . $item['image'] }}"
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
