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
                $attributes = old($row->field, json_decode($dataTypeContent->{$row->field} ?? '[]', true));
            @endphp

            @if(is_array($attributes))
                @foreach($attributes as $name => $value)
                    <tr>
                        <td><input type="text" name="attributes_keys[]" class="form-control" value="{{ $name }}"></td>
                        <td><input type="text" name="attributes_values[]" class="form-control" value="{{ $value }}"></td>
                        <td><button type="button" class="btn btn-danger m-0 remove-attr" style="margin:0;">X</button></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <button type="button" class="btn btn-primary" id="add-attribute">+ Add Attribute</button>
</div>

<input type="hidden" name="{{ $row->field }}" id="attributes-json">

@push('javascript')
<script>
    document.addEventListener("DOMContentLoaded", function () {
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

        addBtn.addEventListener("click", function () {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td><input type="text" name="attributes_keys[]" class="form-control"></td>
                <td><input type="text" name="attributes_values[]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger remove-attr" style="margin:0;">X</button></td>
            `;
            rows.appendChild(row);
        });

        container.addEventListener("click", function (e) {
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
