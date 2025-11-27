@extends('layouts.main')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-design.css') }}">
@endsection

@section('content')
    <section class="table-products position-relative">

        <div class="row">
            <div class="col-4 ms-5 mt-4">
                <p id="sum-products">{{ $totalProducts }} Products</p>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row" id="productList">
                @if ($products)
                    @include('partials.product-items', ['products' => $products])
                @endif
            </div>
        </div>

        @if ($totalProducts > 20)
            <div class="button-showmore">
                <button type="button" class="btn btn-primary" id="btnLoadMore" data-design-id="{{ $design->id }}">
                    <span id="btn-loading" class="spinner-border spinner-border-sm d-none"></span>
                    SHOW MORE PRODUCTS
                </button>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/loadmore.js') }}"></script>
    <script>
        new LoadMore({
            button: "btnLoadMore",
            container: "productList",
            endpoint: "{{ route('designs.products.loadMore', $design) }}",
        });
    </script>
@endpush
