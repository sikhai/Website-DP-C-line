@extends('layouts.main')

@php
    $image = json_decode($project->images_with_captions, true)[0]['path'] ?? null;
    $image_1 = json_decode($project->images_with_captions, true)[1]['path'] ?? null;
    $caption_image_1 = json_decode($project->images_with_captions, true)[1]['caption'] ?? null;
    $image_2 = json_decode($project->images_with_captions, true)[2]['path'] ?? null;
    $caption_image_2 = json_decode($project->images_with_captions, true)[2]['caption'] ?? null;
    $image_3 = json_decode($project->images_with_captions, true)[3]['path'] ?? null;
    $image_4 = json_decode($project->images_with_captions, true)[4]['path'] ?? null;
@endphp

@section('title', $project->name)
@section('meta_description', $project->short_description)
@section('meta_keywords', 'product, item, details')
@section('meta_image', Voyager::image($image))

@section('structured_data')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "https://www.bachhoaxanh.com/kinh-nghiem-hay/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-1564161"
        },
        "headline": "Thông báo chính sách hạng thành viên mới 2024 của Bách hóa XANH",
        "image": {
            "@type": "ImageObject",
            "url": "https://www.bachhoaxanh.com/images/2024/04/03/1564161/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-cua-bach-hoa-xanh-202404031348329042.jpg",
            "width": 760,
            "height": 442
        },
        "description": "Bách hóa XANH hân hạnh thông báo chính sách hạng thành viên mới 2024, áp dụng từ ngày 1/4/2024 dành cho khách hàng mua hàng và tích điểm tại Bách hóa XANH.",
        "name": "Thông báo chính sách hạng thành viên mới 2024 của Bách hóa XANH",
        "inLanguage": "vi-vn",
        "datePublished": "2024-04-03T11:09:48",
        "dateModified": "2024-04-03T11:09:47",
        "author": {
            "@type": "Person",
            "name": "Jolie",
            "url": "https://www.bachhoaxanh.com/kinh-nghiem-hay/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-1564161#author",
            "memberOf": {
                "@id": "https://www.bachhoaxanh.com/#Organization"
            }
        },
        "publisher": {
            "@id": "https://www.bachhoaxanh.com/#Organization"
        }
    }
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/our-project-detail.css') }}">
    <style>
        .header .container:before {
            background: url('{{ Voyager::image($image) }}');
        }
    </style>
@endsection

@section('top_head')
    <div class="row pt-3">
        <a href="/our-project">
            <div class="col-2 d-flex align-items-center back-btn">
                <img src="{{ asset('images/arr-back.png') }}" alt="">
                <h3 id="back">BACK</h3>
            </div>
        </a>
    </div>

    <div class="row">
        <div class="content w-75">
            <p>PROJECT OF THE MONTH</p>
            <h3>{{ $project->name }}</h3>
        </div>
    </div>
@endsection

@section('content')
    <section class="project-info">
        <div class="container">
            <div class="row mt-3 d-flex p-0">
                <div class="col-6">
                    <p>{{ $project->short_description }}</p>
                </div>
                <div class="col-6">
                    <div class="line1"></div>
                    <div class="row d-flex p-0">
                        <div class="col-3">
                            <h3>Products</h3>
                        </div>
                        <div class="col-9">
                            <p>A paragraph is a unit of text that consists of a group of sentences related to a central
                                topic or idea. </p>
                        </div>
                    </div>
                    <div class="line1"></div>
                    <div class="row d-flex p-0">
                        <div class="col-3">
                            <h3>Project</h3>
                        </div>
                        <div class="col-9">
                            <p>{{ $project->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="description_project">
                <div class="row">
                    <div class="col-8">
                        <img class="img-1"
                            src="{{ Voyager::image($image_1) }}" alt="">
                        </div>
                </div>
                <div class="row">
                    <div class="col-6">&nbsp;</div>
                    <div class="col-6">
                        <p>{{$caption_image_1}}</p>
                    </div>
                </div>
                <div class="row">
                    <img class="img-1"
                        src="{{ Voyager::image($image_2) }}" alt=""></div>
                <div class="row mt-5">
                    <div class="col-6">
                        <p>{{$caption_image_2}}</p>
                    </div>
                    <div class="col"><img src="{{ Voyager::image($image_3) }}"
                            alt=""></div>
                </div>
                <div class="row">
                    <div class="col-7"><img class="img-1"
                            src="{{ Voyager::image($image_4) }}" alt=""></div>
                </div>
            </div>
            @php
                $uniqueCategories = $project->products
                    ->map(function ($product) {
                        return optional($product->category)->parentCategory;
                    })
                    ->filter()
                    ->unique('id');
            @endphp
            @if (count($uniqueCategories))
                <div class="products">
                    <div class="row">
                        <h2>in this project</h2>
                    </div>
                    <div class="row d-flex">

                        @foreach ($uniqueCategories as $category)
                            <div class="col-3">
                                <img src="{{ Voyager::image($category->image) }}" class="img-2" alt="">
                                <h5>{{ $category->name }}</h5>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="next-project">
        <div class="container">
            <div class="row">
                @php
                    $image_other = json_decode($project_other->images_with_captions, true)[0]['path'] ?? null;
                @endphp
                <div class="col-6">
                    <img src="{{ Voyager::image($image_other) }}" alt="{{ $project_other->name }}">
                </div>
                <div class="col-6" style="margin-top: 80px;padding-left:30px">
                    <a href="/our-project/{{ $project_other->slug }}">READ MORE</a>
                    <p>Next project</p>
                    <h2>{{ $project_other->name }}</h2>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/menu-script.js') }}"></script>
@endpush
