<section class="banner">
    <div class="image-container">
        <img src="images/spacejoy-YI2YkyaREHk-unsplash.jpg" alt="">
        <div class="overlay"></div>
    </div>

    @foreach (json_decode($projects_of_mounth['images_with_captions'] ?? '[]', true) as $item)
        @if (isset($item['path']))
            <img src="{{ Voyager::image($item['path']) }}" alt="{{ $projects_of_mounth['name'] }}" class="overlay-image">
        @endif
    @endforeach

    <div class="content">
        <p>PROJECT OF THE MONTH</p>
        <a href="/our-project/{{ $projects_of_mounth['slug'] }}">
            <h3>{{ $projects_of_mounth['name'] }}</h3>
        </a>
    </div>
</section>
