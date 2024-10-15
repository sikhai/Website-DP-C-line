<section class="tab-general-fabric position-relative">
    <div class="row">
        <div class="d-flex align-items-center tab-collection-design">
            <a class="nav_item" id="current-tab">Collection</a>
            <a class="nav_item" id="next-tab" href="product-leather-design.html">Design</a>
        </div>
    </div>
    <div class="row pt-5 pb-3">
        <div class="col-1 d-flex align-items-center all-category-dropdown">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu-AllCategory"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    All Categories <img src="{{ asset('images/chevron-down.svg') }}" id="chevron-down" alt="">
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-sortby">
                    @foreach ($categories as $item)
                        <li>
                            <a class="dropdown-item"
                                href="{{ $category && $item->id != $category->id ? '/' . $item->slug : 'javascript:void(0);' }}">
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
                    loading="lazy">
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
