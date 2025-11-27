@foreach ($products as $product)
    <div class="col-md-3 col-6 fabric-item">
        <a class="text-decoration-none" href="{{ route('products.show', $product) }}">
            <img class="img w-100" src="{{ Voyager::image($product->image) }}" alt="{{ $product->name }}" loading="lazy">
            <p class="pt-2 m-0">{{ $product->name }}</p>
        </a>
    </div>
@endforeach
