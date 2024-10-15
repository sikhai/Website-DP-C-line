@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-collection.css') }}">
@endsection

<section class="table-products position-relative mb-5">
    <div class="row">
        <div class="col-4" style="margin:27px 70px 0px">
            <p id="sum-products">{{ count($products) }} Products, {{ count($designs) }} designs</p>
        </div>
    </div>


    <div class="container">
        <div class="row">
            @foreach ($designs as $item)
                @php
                    $images = json_decode($item['images'], true);
                @endphp
                <div class="col-lg-4">
                    <div class="fabric-item">
                        <a class="text-decoration-none" href="/design/{{ $item['slug'] }}">
                            <img class="img w-100" src="{{ Voyager::image($images[0]) }}" alt="{{ $item['name'] }}"
                                loading="lazy">
                            <div class="d-flex m-0" id="block-collection-lable">
                                <p id="collection-name">{{ $item['name'] }}</p>
                                <p id="collection-quantity">({{ $item->products->count() }})</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @if (count($designs) > 12)
        <div class="button-showmore pt-5">
            <button type="button" class="btn btn-primary" id="btn-showmore">
                SHOW MORE PRODUCTS
            </button>
        </div>
    @endif
</section>
