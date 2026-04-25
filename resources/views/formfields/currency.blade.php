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
    const currencyRateInput = document.querySelector('input[name="currency_rate"]');

    if (!select || !currencyRateInput) return;

    function updateCurrencyRate() {
        const selected = select.options[select.selectedIndex];
        const rate = selected.getAttribute('data-rate');

        if (rate) {
            currencyRateInput.value = parseFloat(rate);
        }
    }

    select.addEventListener('change', updateCurrencyRate);

    // khi edit form (load sẵn)
    updateCurrencyRate();
});
</script>