<section class="Our_Services">
    <div class="container">
        <h2 class="title">
            Our Services
        </h2>
        <div class="swiper services_list">
            <div class="swiper-wrapper">
                @foreach ($categories as $key => $category)
                    <div class="swiper-slide item" data-shape="{{$category['shape']}}">
                        <a href="/{{ $category->slug }}" style="text-decoration: none;">
                            <div class="img-wrapper-service">
                                <img class="w-100" src="{{ env('APP_URL').'/storage/'.$category['image'] }}" alt="{{ $category['category_name'] }}"
                                    loading="lazy">
                                <div class="overlay_service"></div>
                            </div>
                            <p class="pt-2 m-0">{{ $category['category_name'] }}</p>
                        </a>
                    </div>
                @endforeach


            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
