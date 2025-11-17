<section class="tab-general-fabric position-relative">
    <div class="row">
        <div class="d-flex align-items-center tab-collection-design">
            <a class="nav_item" id="{{ !isset($title_head) ? 'current-tab' : ($title_head == 'Collection' ? 'current-tab' : 'next-tab') }}" href="/collection">Collection</a>
            <a class="nav_item" id="{{ !isset($title_head) ? 'next-tab' : ($title_head == 'All Products' ? 'current-tab' : 'next-tab') }}" href="/products">Design</a>
        </div>
    </div>
    <div class="row pt-5 pb-3">
        <div class="col-1 d-flex align-items-center all-category-dropdown">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu-AllCategory"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{ isset($category->name) ? $category->name : 'All Categories' }}<img src="{{ asset('images/chevron-down.svg') }}" id="chevron-down" alt="">
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-sortby">
                    @foreach ($categories as $item)
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('categories.show', $item->slug) }}">
                                {{ $item->name }}
                            </a>
                        </li>
                    @endforeach

                </ul>

            </div>
        </div>
        <div class="col-8">

        </div>
        <div class="col-2 d-flex align-items-center menu_right_filter">
            <button type="button" class="btn btn-primary" id="btn-filter" onclick="open_filterbar()">
                Filters <img class="w-24 mt-1" src="{{ asset('images/filter-icon.svg') }}" alt=""
                    loading="lazy" id="filter-icon">
                <img class="w-24 mt-1" src="{{ asset('images/Icon-Filter_mobile.png') }}" alt=""
                    loading="lazy" id="filter-icon2">
            </button>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu-sortby"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Sort by: Trending <img src="{{ asset('images/chevron-down.svg') }}" id="chevron-down"
                        alt="">
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-sortby">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>

        </div>
    </div>
</section>
