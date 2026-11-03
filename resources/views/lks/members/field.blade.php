@php
    $required = $required ?? true;
    $type = $type ?? 'text';
    $value = $values[$key] ?? '';
    $id = 'lm-'.$key;
@endphp
<label class="{{ in_array($key, ['ktpAddress', 'notes'], true) ? 'lm-wide' : '' }}" for="{{ $id }}">{{ $labels[$key] }}@if ($required)<span aria-hidden="true"> *</span>@else (jika ada)@endif
    @if (isset($options))
        <select id="{{ $id }}" name="{{ $key }}" data-label="{{ $labels[$key] }}" aria-describedby="{{ $id }}-error" @required($required)>
            <option value="">Pilih {{ mb_strtolower($labels[$key]) }}</option>
            @foreach ($options as $option)
                <option @selected($value === $option)>{{ $option }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $key }}" rows="3" maxlength="{{ $maxlength }}" data-label="{{ $labels[$key] }}" aria-describedby="{{ $id }}-error" @required($required)>{{ $value }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $key }}" type="{{ $type }}" value="{{ $value }}" data-label="{{ $labels[$key] }}" aria-describedby="{{ $id }}-error" @required($required) {!! $attrs ?? '' !!}>
    @endif
    <small class="lm-field-error" id="{{ $id }}-error" data-lm-field-error="{{ $key }}" hidden></small>
</label>
