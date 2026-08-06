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
                                value="{{ $value ?? '' }}"
                                @if ($inputType === 'number') step="0.01" min="0" @endif>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-attr">X</button>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <div class="mb-2" style="margin-bottom: 15px;">
        <button type="button" class="btn btn-primary" id="add-attribute">+ Add Attribute</button>
        @if (($dataType->slug ?? '') === 'designs')
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#manageDesignAttributesModal" id="btn-open-manage-attributes">
                <i class="voyager-settings"></i> Manage Suggestions
            </button>
        @endif
    </div>
</div>

@if (($dataType->slug ?? '') === 'designs')
    <!-- Modal Manage Design Attributes -->
    <div class="modal fade" id="manageDesignAttributesModal" tabindex="-1" role="dialog" aria-labelledby="manageDesignAttributesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="manageDesignAttributesModalLabel">Manage Design Attribute Suggestions</h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Toggle whether each Design Attribute is suggested when creating a new Design.</p>
                    <div id="design-attributes-loading" class="text-center" style="display: none; padding: 20px;">
                        <i class="voyager-refresh spin" style="font-size: 24px;"></i> Loading attributes...
                    </div>
                    <table class="table table-hover" id="design-attributes-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>Attribute Name</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th style="width: 100px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="design-attributes-modal-list">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif

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

            // Manage Design Attributes modal logic
            const openModalBtn = document.getElementById("btn-open-manage-attributes");
            if (openModalBtn) {
                const modalList = document.getElementById("design-attributes-modal-list");
                const loadingSpinner = document.getElementById("design-attributes-loading");
                const attrTable = document.getElementById("design-attributes-table");

                function loadDesignAttributes() {
                    loadingSpinner.style.display = "block";
                    attrTable.style.display = "none";
                    modalList.innerHTML = "";

                    fetch("{{ route('design-attributes.index') }}")
                        .then(response => response.json())
                        .then(data => {
                            loadingSpinner.style.display = "none";
                            attrTable.style.display = "table";

                            if (data.success && data.attributes) {
                                if (data.attributes.length === 0) {
                                    modalList.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No design attributes found.</td></tr>';
                                    return;
                                }

                                data.attributes.forEach(attr => {
                                    const tr = document.createElement("tr");
                                    const isActive = parseInt(attr.status) === 1;
                                    tr.innerHTML = `
                                        <td><strong>${attr.name}</strong></td>
                                        <td class="text-center">
                                            <span class="label ${isActive ? 'label-success' : 'label-danger'}" id="status-badge-${attr.id}">
                                                ${isActive ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs ${isActive ? 'btn-warning' : 'btn-success'} toggle-attr-btn" 
                                                data-id="${attr.id}" data-status="${attr.status}">
                                                ${isActive ? 'Disable' : 'Enable'}
                                            </button>
                                        </td>
                                    `;
                                    modalList.appendChild(tr);
                                });
                            }
                        })
                        .catch(err => {
                            loadingSpinner.style.display = "none";
                            modalList.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Failed to load attributes.</td></tr>';
                        });
                }

                openModalBtn.addEventListener("click", function() {
                    loadDesignAttributes();
                });

                if (modalList) {
                    modalList.addEventListener("click", function(e) {
                        const target = e.target.closest(".toggle-attr-btn");
                        if (!target) return;

                        const attrId = target.getAttribute("data-id");
                        const currentStatus = parseInt(target.getAttribute("data-status"));
                        const newStatus = currentStatus === 1 ? 0 : 1;

                        target.disabled = true;

                        fetch(`{{ url('admin/design-attributes') }}/${attrId}/toggle-status`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(res => res.json())
                        .then(resData => {
                            target.disabled = false;
                            if (resData.success) {
                                target.setAttribute("data-status", newStatus);
                                const badge = document.getElementById(`status-badge-${attrId}`);
                                if (newStatus === 1) {
                                    badge.className = "label label-success";
                                    badge.textContent = "Active";
                                    target.className = "btn btn-xs btn-warning toggle-attr-btn";
                                    target.textContent = "Disable";
                                } else {
                                    badge.className = "label label-danger";
                                    badge.textContent = "Inactive";
                                    target.className = "btn btn-xs btn-success toggle-attr-btn";
                                    target.textContent = "Enable";
                                }
                            }
                        })
                        .catch(err => {
                            target.disabled = false;
                            alert("Failed to toggle attribute status.");
                        });
                    });
                }
            }
        });
    </script>
@endpush
