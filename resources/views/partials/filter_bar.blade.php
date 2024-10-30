<section class="filter-bar-section" style="display: none;">
    <div class="opacity-layer"></div>
    <div class="filter-bar">
        <div class="container">
            <!-- top title filter-bar -->
            <div class="row">
                <div class="col-2 p-0">
                    <h3>Filters</h3>
                </div>
                <div class="col-10 p-0" style="display: flex; justify-content:flex-end; align-items:center">
                    <img id="x-close-1" src="{{ asset('images/x-close.png') }}" onclick="hide_filterbar()" alt=""
                        style="cursor:pointer">
                </div>
            </div>
            <div class="row p-0">
                <div id="line-8"></div>
            </div>

            <!-- selected Filters -->
            <div class="row" style="margin-top: 40px;">
                <div class="col-6 p-0">
                    <h4>Selected Filters</h4>
                </div>
                <div class="col-6 p-0" style="display: flex; justify-content:flex-end;">
                    <h5 onclick="clear_sellected_item()">CLEAR ALL</h5>
                </div>
            </div>
            <div class="row d-flex mt-3 p-0" id="row-items-selected" style="gap: 8px;">
            </div>

            @foreach ($result_attributes as $name => $values)
                <div class="row mt-5">
                    <div class="col-12 p-0">
                        <h4>{{ ucfirst($name) }}</h4>
                    </div>
                </div>
                <div class="row mt-2">
                    @foreach ($values as $value => $data)
                        <div class="col-3 p-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $value }}" data-products="{{ count($data['list_ids']) }}"
                                    id="check_{{ $value }}">
                                <label class="form-check-label" for="check_{{ $value }}">
                                    {{ $value }} 
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach



            <!-- button apply  -->
            <div class="row mt-5">
                <button type="button" class="btn btn-secondary">
                    APPLY
                </button>
                <div id="arrow">
                    <img src="{{ asset('images/arrow-down-right.svg') }}" alt="">
                </div>
            </div>

        </div>
    </div>
</section>
