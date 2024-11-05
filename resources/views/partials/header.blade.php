<section class="header">
    <div class="container">
        <div class="top_head pt-3 pb-3">
            <div class="row">
                <div class="col-2 d-flex align-items-center">
                    <a class="nav_item" href="#contact-mail">CONTACT</a>
                </div>
                <div class="col-8 text-center">
                    <a href="{{ env('APP_URL') }}">
                        <img src="{{ Voyager::image(setting('site.logo')) }}" width="55" id="img-logo-ref"
                            alt="logo" loading="lazy">
                    </a>
                </div>
                <div class="col-2 d-flex align-items-center menu_right">
                    <a class="nav_item" id="search" onclick="opensearch()">SEARCH</a>
                    <a class="nav_item" id="open-menu" onclick="openmenu()">MENU</a>
                </div>
            </div>
            @yield('top_head')
        </div>
        <div class="body_head">
            @home
                <h1>Li<span>v</span>e <span>A</span>rtf<span>u</span>lly</h1>
            @else
                @if (isset($category) || isset($title_head))
                    <h1>{{ isset($category) ? $category->name : (isset($title_head) ? $title_head : '') }}</h1>
                    <div class="d-flex align-items-center menu_fabric">
                        <a class="nav_item" id="next-tab" href="{{ route('home') }}">Home</a>
                        <p class="nav_item">/</p>
                        @isset($category)
                            <a class="nav_item" id="next-tab" href="/products">All Products</a>
                            <p class="nav_item">/</p>
                            <a class="nav_item" id="current-tab">{{ $category->name }}</a>
                        @else
                            <a class="nav_item" id="current-tab">{{ isset($title_head) ? $title_head : '' }}</a>
                        @endisset
                    </div>
                    <div id="line"></div>
                @endif
            @endhome
        </div>
    </div>
</section>
<section class="menu-area">
    <div class="header-menu">
        <div class="row mt-3">
            <div class="col-2 d-flex align-items-center">

            </div>
            <div class="col-8 text-center">
                <a>
                    <img src="{{ Voyager::image(setting('site.logo')) }}" width="55" id="img-logo-ref"
                        alt="logo" loading="lazy">
                </a>
            </div>
            <div class="col-2 d-flex align-items-center menu_right">
                <a class="nav_item" onclick="closemenu()">CLOSE</a>
                <img src="{{ asset('images/x-close-12x12.svg') }}" style="cursor:pointer;" onclick="closemenu()"
                    alt="">
            </div>
        </div>

    </div>
    <div class="body-menu">
        <div class="row p-0">
            <div class="col-4 d-flex flex-column">
                @foreach ($categories->take(3) as $key => $category)
                    <a class="option-menu" href="/{{ $category->slug }}">{{ $category->name }}</a>
                @endforeach
            </div>
            <div class="col-4 d-flex flex-column">
                @foreach ($categories->skip(3)->take(3) as $key => $category)
                    <a class="option-menu" href="/{{ $category->slug }}">{{ $category->name }}</a>
                @endforeach
            </div>
            <div class="col-4 d-flex flex-column">
                @foreach ($categories->skip(6)->take(2) as $key => $category)
                    <a class="option-menu" href="/{{ $category->slug }}">{{ $category->name }}</a>
                @endforeach
                <a class="option-menu" href="#">About Us</a>
            </div>
        </div>
    </div>
</section>
<section class="search-area">
    <div class="header-search">
        <div class="row mt-3">
            <div class="col-2 d-flex align-items-center">

            </div>
            <div class="col-8 text-center">
                <a>
                    <img src="{{ Voyager::image(setting('site.logo')) }}" width="55" id="img-logo-ref"
                        alt="logo" loading="lazy">
                </a>
            </div>
            <div class="col-2 d-flex align-items-center menu_right">
                <a class="nav_item" onclick="closemenu()">CLOSE</a>
                <img src="{{ asset('images/x-close-12x12.svg') }}" id="img-close-ref" style="cursor:pointer;"
                    onclick="closemenu()" alt="">
            </div>
        </div>

    </div>
    <div class="body-search">
        <div class="row p-0">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search" id="search-item">
            </div>
        </div>
        <div class="row p-0 mt-2 text-search" style="display: none;">
            <h4>Press Enter to search</h4>
        </div>
    </div>
</section>
