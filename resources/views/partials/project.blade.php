<!-- /* project */ -->
<section class="project position-relative">
    <div class="container">
        <h2 class="title text-center">
            Projects we are proud of
        </h2>
    </div>
    <a href="/our-project">
        <div class="d-flex img-area">
            @foreach ($projects_home as $key => $item)
                @php
                    $images = json_decode($item['images'], true);
                @endphp
                <div class="img-wrapper image{{ $key + 1 }}">
                    <img src="{{ Voyager::image($images[0]) }}" alt="{{ $item['project_name'] }}" loading="lazy"
                        class="hover-image ">
                    <div class="overlay"></div>
                </div>
            @endforeach
        </div>
    </a>
</section>
