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
                $images = json_decode($item['images'], true);
            @endphp
            <a href="/our-project/{{ $item['slug'] }}">
                <div class="img-wrapper image{{ $key + 1 }}">
                    <img src="{{ Voyager::image($images[0]) }}" alt="{{ $item['project_name'] }}" loading="lazy"
                        class="hover-image ">
                    <div class="overlay"></div>
                </div>
            </a>
        @endforeach
    </div>
</section>
