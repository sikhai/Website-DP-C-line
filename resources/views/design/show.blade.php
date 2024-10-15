@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-design.css') }}">
@endsection

<section class="table-products position-relative">
    <div class="row">
        <div class="col-4" style="margin:27px 70px 0px">
            <p id="sum-products">{{ count($products) }} Products, 1 designs</p>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach ($products as $item)
                <div class="col-lg-3">
                    <div class="fabric-item">
                        <img class="img w-100" src="{{ env('APP_URL').'/storage/'.$item['image'] }}" alt="" loading="lazy">
                        <p class="pt-2 m-0" id="design-name">{{ $item['name'] }}</p>
                        <p class="pt-2 m-0" id="design-code">{{ $designs->name }}</p>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    @if (count($products) > 12)
        <div class="button-showmore pt-5">
            <button type="button" class="btn btn-primary" id="btn-showmore">
                SHOW MORE PRODUCTS
            </button>
        </div>
    @endif
</section>
