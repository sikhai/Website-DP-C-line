<!-- /* project */ -->
<section class="project position-relative">
    <div class="container">
        <h2 class="title text-center">
            Projects we are proud of
        </h2>
    </div>

    <div class="d-flex img-area">
        @foreach ($projects_home as $key => $item)
            @php
                $images = json_decode($item['images_with_captions'], true);
            @endphp
            @if (!empty($images) && isset($images[0]['path']))
                <div class="img-wrapper image{{ $key + 1 }}">
                    <img src="{{ Voyager::image($images[0]['path']) }}" alt="{{ $item['project_name'] }}" loading="lazy"
                        class="hover-image ">
                    <a href="/our-project/{{ $item['slug'] }}">
                        <div class="overlay"></div>
                    </a>
                </div>
            @endif
        @endforeach
    </div>

</section>
