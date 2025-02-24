@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-design.css') }}">
@endsection

<section class="table-products position-relative">
    <div class="row">
        <div class="col-4" style="margin:27px 70px 0px">
            <p id="sum-products">{{ $products->total() }} Products</p>
        </div>
    </div>
    <div class="container mb-5">
        <div class="row" id="product-list">
            @foreach ($products as $item)
                <div class="fabric-item">
                    <a class="text-decoration-none" href="/products/{{ $item['slug'] }}">
                        <img class="img w-100" src="{{ env('APP_URL') . '/storage/' . $item['image'] }}"
                            alt="{{ $item['name'] }}" loading="lazy">
                        <p class="pt-2 m-0" id="design-name">{{ $item['name'] }}</p>
                        <p class="pt-2 m-0" id="design-code">{{ $item->category->name }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    @if ($products->total() > 20)
        @if (isset($attributeString) && isset($category_slug) && isset($encrypted_ids))
            <div class="button-showmore">
                <button type="button" class="btn btn-primary" id="btn-showmore-filter"
                    data-list_ids="{{ $encrypted_ids }}">
                    <span id="btn-loading" class="spinner-border spinner-border-sm d-none" role="status"
                        aria-hidden="true"></span>
                    SHOW MORE PRODUCTS
                </button>
            </div>
        @else
            <div class="button-showmore">
                <button type="button" class="btn btn-primary" id="btn-showmore"
                    data-category="{{ isset($designs->slug) ? $designs->slug : '' }}">
                    <span id="btn-loading" class="spinner-border spinner-border-sm d-none" role="status"
                        aria-hidden="true"></span>
                    SHOW MORE PRODUCTS
                </button>
            </div>
        @endif

    @endif
</section>

<script id="product-template" type="text/x-handlebars-template">
    <div class="col-lg-3">
        <div class="fabric-item">
            <a class="text-decoration-none" href="/design/@{{ slug }}">
                <img class="img w-100" src="@{{ image_url }}" alt="@{{ name }}" loading="lazy">
                <p class="pt-2 m-0" id="design-name">@{{ name }}</p>
                <p class="pt-2 m-0" id="design-code">@{{ category.name }}</p>
            </a>
        </div>
    </div>
</script>
