@props(['name', 'label', 'options', 'all', 'value' => '', 'class' => 'lks-select-field'])
{{-- options: daftar nilai, atau pasangan [nilai, teks]. --}}
<label class="{{ $class }}">
    <span class="sr-only">{{ $label }}</span>
    <select name="{{ $name }}" aria-label="{{ $label }}">
        <option value="">{{ $all }}</option>
        @foreach ($options as $option)
            @php([$optionValue, $text] = is_array($option) ? $option : [$option, $option])
            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $text }}</option>
        @endforeach
    </select>
</label>
