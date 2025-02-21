@php
    use App\Services\ProductService;

    $productService = app(ProductService::class);
    $result_attributes = $productService->getAttributesWithProductCount();

    // Kiểm tra nếu $dataTypeContent là một Collection, lấy bản ghi đầu tiên
    if ($dataTypeContent instanceof \Illuminate\Database\Eloquent\Collection) {
        $dataTypeContent = $dataTypeContent->first();
    }

    // Lấy dữ liệu đã lưu và giải mã JSON
    $attributes_value = old($row->field, optional($dataTypeContent)->{$row->field} ?? '[]');
    $selected_values = json_decode($attributes_value, true) ?? [];

    // Giải mã lần hai nếu phần tử đầu tiên vẫn là chuỗi JSON
    $selected_keys = [];
    if (is_array($selected_values) && isset($selected_values[0])) {
        $first_value = json_decode($selected_values[0], true);
        if (is_array($first_value)) {
            $selected_keys = array_keys($first_value);
        }
    }
@endphp

<select name="attributes_value[]" class="form-control" multiple>
    @foreach ($result_attributes as $name => $values)
        @php
            $json_value = json_encode([$name => $values]);

            // Kiểm tra nếu option có được chọn
            $isSelected = in_array($name, $selected_keys);
        @endphp

        <option value="{{ $json_value }}" {{ $isSelected ? 'selected' : '' }}>
            {{ ucfirst($name) }}
        </option>
    @endforeach
</select>
