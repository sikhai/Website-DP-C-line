<!-- /* Trending */ -->
<section class="trending">
    <div class="container">
        <h2 class="title text-center line_1">
            <span>W</span>hat’s
        </h2>
        <h2 class="title text-center line_2">
            Trending
        </h2>

        <div class="box_trending d-flex">

            <div class="left">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        @foreach ($designs_is_trending as $key => $item)
                            @php
                                $images = json_decode($item['images'], true);
                            @endphp
                            <div class="flip-card-{{ $key == 0 ? 'front' : 'back' }}">
                                <a href="/{{ isset($item->parentCategory['slug']) ? $item->parentCategory['slug'] : '' }}">
                                    @if (!empty($images) && isset($images[0]))
                                        <img class="img-flip w-100" src="{{ Voyager::image($images[0]) }}"
                                            alt="{{ $item['name'] }}">
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="right d-grid">
                <div class="context">
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            @foreach ($designs_is_trending as $key => $item)
                                <div class="flip-card-{{ $key == 0 ? 'front' : 'back' }}">
                                    <p>{{ isset($item->parentCategory['name']) ? $item->parentCategory['name'] : '' }}</p>
                                    <h3>{{ $item['name'] }}</h3>
                                    <a href="/{{ isset($item->parentCategory['slug']) ? $item->parentCategory['slug'] : '' }}">SEE COLLECTION <img
                                            src="{{ asset('images/arrow-right-beige800.svg') }}" alt="arrow"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="img-rightbottom">
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            @foreach ($designs_is_trending as $key => $item)
                                @php
                                    $images = json_decode($item['images'], true);
                                @endphp
                                <div class="flip-card-{{ $key == 0 ? 'front' : 'back' }}">
                                    <a href="/{{ isset($item->parentCategory['slug']) ? $item->parentCategory['slug'] : '' }}">
                                        @if (!empty($images) && isset($images[1]))
                                            <img class="w-100 img" src="{{ Voyager::image($images[1]) }}"
                                                alt="{{ $item['name'] }}">
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
