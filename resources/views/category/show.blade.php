@extends('layouts.main')

@section('content')
    <h1>{{ $category->category_name }}</h1>
    <p>{{ $category->description }}</p>
    <img src="{{ $category->image }}" alt="{{ $category->category_name }}">

    <h2>Products in this Category</h2>
    <ul>
        @foreach ($category->products as $product)
            <li><a href="{{ route('product.show', ['category_slug' => $category->slug, 'product_slug' => $product->slug]) }}">{{ $product->ProductName }}</a></li>
        @endforeach
    </ul>
@endsection
