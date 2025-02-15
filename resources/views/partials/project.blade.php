<!-- /* project */ -->
<section class="project position-relative">
    <div class="container">
        <h2 class="title text-center">
            Projects we are proud of
        </h2>
    </div>
    <a href="./our-project">
        <div class="d-flex img-area">
            @foreach ($projects_home as $key => $item)
                @php
                    $images = json_decode($item['images_with_captions'], true);
                @endphp
                @if (!empty($images) && isset($images[0]['path']))
                    <div class="img-wrapper image{{ $key + 1 }}">
                        <img src="{{ Voyager::image($images[0]['path']) }}" alt="{{ $item['project_name'] }}"
                            loading="lazy" class="hover-image ">
                        <div class="overlay"></div>
                    </div>
                @endif
            @endforeach
        </div>
    </a>
</section>
