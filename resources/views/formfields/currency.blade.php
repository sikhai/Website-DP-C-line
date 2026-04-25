<select name="currency_id" class="form-control" id="currency_select">
    <option value="">-- Chọn currency --</option>

    @foreach($currencies as $currency)
        <option 
            value="{{ $currency->id }}"
            data-rate="{{ $currency->exchange_rate }}"
            {{ $selected == $currency->id ? 'selected' : '' }}
        >
            {{ $currency->name }} ({{ $currency->code }})
        </option>
    @endforeach
</select>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('currency_select');
    const priceInput = document.querySelector('input[name="price"]');

    if (!select || !priceInput) return;

    function updatePrice() {
        const selected = select.options[select.selectedIndex];
        const rate = selected.getAttribute('data-rate');

        if (rate) {
            priceInput.value = parseFloat(rate);
        }
    }

    select.addEventListener('change', updatePrice);

    // khi edit form (load sẵn)
    updatePrice();
});
</script>