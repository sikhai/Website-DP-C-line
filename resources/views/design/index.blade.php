@extends('layouts.main')

@section('content')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-fabric-design.css') }}">
@endsection

<section class="table-products position-relative">
    <div class="row">
        <div class="col-4 sum" style="margin:27px 70px 0px">
            <p id="sum-products">{{ $totalProducts }} Products, {{ $totalDesigns }} designs</p>
        </div>
    </div>
    <div class="container mb-5">
        <div class="row" id="design-list">
            @include('partials.design-items', ['designs' => $designs])
        </div>
    </div>

    @if ($designs->total() > 20)
        <div class="button-showmore">
            <button type="button" class="btn btn-primary" id="btnLoadMore">
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
        container: "design-list",
        endpoint: "{{ route('designs.loadMore') }}"
    });
</script>
@endpush
