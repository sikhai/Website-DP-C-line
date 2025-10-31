<div class="vendor-attributes-wrapper" 
     data-field="{{ $fieldName }}" 
     data-initial='{{ $jsonAttributes }}'>

    <div class="attribute-list"></div>

    <div class="mt-2">
        <button type="button" class="btn btn-sm btn-primary btn-add-attribute">
            <i class="voyager-plus"></i> Thêm thuộc tính
        </button>
    </div>

    <!-- template -->
    <template class="vendor-attribute-template">
        <div class="attribute-item row" style="margin-bottom:6px;">
            <div class="col-xs-5">
                <input type="text" class="form-control attr-name" placeholder="Tên thuộc tính">
            </div>
            <div class="col-xs-6">
                <select class="form-control attr-value select2-accessory">
                    <option value="">-- Chọn phụ kiện --</option>
                    @foreach ($accessories as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-1 text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-attribute" title="Xóa">
                    <i class="voyager-trash"></i>
                </button>
            </div>
        </div>
    </template>
</div>

@push('javascript')
<script>
(function() {
    const wrapper = document.querySelector('.vendor-attributes-wrapper[data-field="{{ $fieldName }}"]');
    if (!wrapper) return;

    const fieldName = wrapper.dataset.field;
    let items = [];

    try {
        items = JSON.parse(wrapper.dataset.initial || '[]');
        if (!Array.isArray(items)) items = [];
    } catch (e) {
        items = [];
    }

    const listEl = wrapper.querySelector('.attribute-list');
    const template = wrapper.querySelector('.vendor-attribute-template');

    function render() {
        listEl.innerHTML = '';
        items.forEach((it, idx) => {
            const node = template.content.firstElementChild.cloneNode(true);
            const inputName = node.querySelector('.attr-name');
            const selectValue = node.querySelector('.attr-value');
            const btnRemove = node.querySelector('.btn-remove-attribute');

            inputName.value = it.name ?? '';
            selectValue.value = it.value ?? '';
            inputName.name = `${fieldName}[${idx}][name]`;
            selectValue.name = `${fieldName}[${idx}][value]`;

            btnRemove.addEventListener('click', () => {
                items.splice(idx, 1);
                render();
            });

            listEl.appendChild(node);
        });

        $(listEl).find('.select2-accessory').select2({
            width: '100%',
            placeholder: '-- Chọn phụ kiện --',
            allowClear: true,
        });
    }

    wrapper.querySelector('.btn-add-attribute').addEventListener('click', () => {
        items.push({ name: '', value: '' });
        render();
    });

    render();
})();
</script>
@endpush
