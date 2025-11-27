@extends('layouts.main')

@section('content')
    <!-- tab-general-fabric collection design -->
    @include('partials.tab_list')

    <!-- body -->
    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/product-fabric-collection.css') }}">
    @endsection

    <section class="table-products position-relative mb-5">
        <div class="row">
            <div class="col-4 sum" style="margin:27px 70px 0px">
                <p id="sum-products">{{ $category->total_products }} Products, {{ $category->total_collections }} collections</p>
            </div>
        </div>


        <div class="container">
            <div class="row">
                @foreach ($category->collections as $item)
                    <div class="fabric-item">
                        <a href="{{ route('collections.show', $item) }}">
                            <img class="img w-100" src="{{ Voyager::image($item->image) }}"
                                alt="{{ $item['name'] }}" loading="lazy">
                            <div class="d-flex m-0" id="block-collection-lable">
                                <p id="collection-name">{{ $item['name'] }}</p>
                                <p id="collection-quantity">({{ $item->products_count }})</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- Filter bar -->
    @include('partials.filter_bar')
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
@endpush
