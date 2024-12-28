{{-- <section class="banner" id="section2">
    <div class="image-container">
        <img src="images/spacejoy-YI2YkyaREHk-unsplash.jpg" alt="">
        <div class="overlay"></div>
    </div>
    <!-- <div class="surrounding-images">
        <img src="images/component1.jpg" class="surround-img" alt="">
        <img src="images/component2.jpg" class="surround-img" alt="">
        <img src="images/component3.jpg" class="surround-img" alt="">
        <img src="images/component4.jpg" class="surround-img" alt="">
        <img src="images/component5.jpg" class="surround-img" alt="">
    </div> -->
    <div class="content" >
        <p>PROJECT OF THE MONTH</p>
        <h3>JW MARRIOT PHU QUOC RESORT</h3>
    </div>
</section> --}}

<section class="banner">
    <div class="image-container">
        <img src="images/spacejoy-YI2YkyaREHk-unsplash.jpg" alt="">
        <div class="overlay"></div>
    </div>
    @foreach (json_decode($projects_of_mounth['images'] ?? '[]') as $item)
        <img src="{{ Voyager::image($item) }}" alt="{{ $projects_of_mounth['name'] }}" class="overlay-image">
    @endforeach
    <div class="content">
        <p>PROJECT OF THE MONTH</p>
        <h3>JW MARRIOT PHU QUOC RESORT</h3>
    </div>
</section>
