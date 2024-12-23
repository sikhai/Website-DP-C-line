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
                                <input class="form-check-input" type="checkbox" value="{{ $value }}"
                                    data-products="{{ count($data['list_ids']) }}" id="check_{{ucfirst($name)}}_{{ $value }}"
                                    attribute-name="{{ ucfirst($name) }}">
                                <label class="form-check-label" for="check_{{ucfirst($name)}}_{{ $value }}">
                                    {{ $value }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach



            <!-- button apply  -->
            <div class="row mt-5 mb-5">
                <button type="button" class="btn btn-secondary btn-filter-action">
                    APPLY
                </button>
                <div id="arrow">
                    <img src="{{ asset('images/arrow-down-right.svg') }}" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.querySelector('.btn-filter-action').addEventListener('click', function() {
        // Lấy tất cả các checkbox đã checked
        const checkedCheckboxes = Array.from(document.querySelectorAll('.form-check-input:checked'));

        // Tạo chuỗi cặp `attribute-name` và `value`, ngăn cách từng cặp bằng dấu phẩy
        const attributePairs = checkedCheckboxes.map(checkbox => {
            const attributeName = checkbox.getAttribute('attribute-name');
            const value = checkbox.value;
            return `${attributeName}-${value}`;
        }).join(',');

        // Tạo URL với tham số mới
        const urlParams = `?attribute=${attributePairs}`;

        // Điều hướng đến URL với tham số đã tạo
        window.location.href = urlParams;
    });
</script>
