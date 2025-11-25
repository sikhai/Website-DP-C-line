@foreach ($designs as $item)
    <div class="fabric-item">
        <a class="text-decoration-none" href="{{ route('designs.show', $item) }}">
            <img class="img w-100" src="{{ Voyager::image($item->image) }}" alt="{{ $item->name }}" loading="lazy">
            <p class="pt-2 m-0" id="design-name">{{ $item->name }}</p>
        </a>
    </div>
@endforeach
