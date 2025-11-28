<div id="attributes-container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="attributes-rows">

            @php
                // Lấy raw value từ old() hoặc DB (đảm bảo có chuỗi JSON mặc định)
                $raw = old($row->field, $dataTypeContent->{$row->field} ?? '[]');

                // Nếu đã là array thì dùng luôn
                if (is_array($raw)) {
                    $attributes = $raw;
                } else {
                    // Thử decode lần 1
                    $attributes = json_decode($raw, true);

                    // Nếu kết quả decode lần 1 vẫn là string => có khả năng bị double-encoded -> decode lần 2
                    if (is_string($attributes)) {
                        $attributes = json_decode($attributes, true);
                    }
                }

                // Fallback: đảm bảo luôn là array
                $attributes = is_array($attributes) ? $attributes : [];

                // Nếu không có dữ liệu và có designAttributes -> build mảng rỗng value
                if (empty($attributes) && isset($designAttributes) && $designAttributes->count()) {
                    $attributes = collect($designAttributes)->mapWithKeys(fn($name) => [$name => ''])->toArray();
                }
            @endphp


            @foreach ($attributes as $name => $value)
                <tr>
                    <td>
                        <input type="text" name="attributes_keys[]" class="form-control" value="{{ $name }}">
                    </td>
                    <td>
                        @if (strtolower($name) === 'supplier' && isset($suppliers))
                            <select name="attributes_values[]" class="form-control">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->name }}" @selected($value == $supplier->name)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            @php
                                $inputType = in_array(strtolower($name), ['roll width', 'roll length'])
                                    ? 'number'
                                    : 'text';
                            @endphp
                            <input type="{{ $inputType }}" name="attributes_values[]" class="form-control"
                                value="{{ $value ?? '' }}">
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-attr">X</button>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <button type="button" class="btn btn-primary" id="add-attribute">+ Add Attribute</button>
</div>

<input type="hidden" name="{{ $row->field }}" id="attributes-json">

@push('javascript')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("attributes-container");
            const addBtn = document.getElementById("add-attribute");
            const rows = document.getElementById("attributes-rows");
            const jsonInput = document.getElementById("attributes-json");

            function updateJSON() {
                const keys = document.getElementsByName("attributes_keys[]");
                const values = document.getElementsByName("attributes_values[]");
                const result = {};

                for (let i = 0; i < keys.length; i++) {
                    const key = keys[i].value.trim();
                    const val = values[i].value.trim();
                    if (key) result[key] = val;
                }

                jsonInput.value = JSON.stringify(result);
            }

            addBtn.addEventListener("click", function() {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td><input type="text" name="attributes_keys[]" class="form-control"></td>
                    <td><input type="text" name="attributes_values[]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger remove-attr">X</button></td>
                `;
                rows.appendChild(row);
                updateJSON();
            });

            container.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-attr")) {
                    e.target.closest("tr").remove();
                    updateJSON();
                }
            });

            container.addEventListener("input", updateJSON);

            updateJSON();
        });
    </script>
@endpush
