{{-- fieldMarkup() dari lks/client-form-markup.js. --}}
@php
    $field = $fields[$key];
    $type = $field['type'] ?? 'text';
    $value = $data[$key] ?? '';
    $controlId = 'resident-'.$key;
    $describedBy = "{$controlId}-help {$controlId}-error";
    $choiceCopy = [
        'orphanFather' => ['Yatim', 'Ayah telah meninggal dunia'],
        'orphanMother' => ['Piatu', 'Ibu telah meninggal dunia'],
        'economicallyVulnerable' => ['Rentan secara ekonomi', 'Keluarga membutuhkan dukungan ekonomi'],
    ];
    $wide = $type === 'textarea' || in_array($key, ['name', 'guardianStatus', 'birthDateUnknown', 'healthCoverageStatus'], true);
    $mode = match ($key) {
        'nik' => 'inputmode=numeric data-numeric=nik data-exact-length=16',
        'familyCardNumber' => 'inputmode=numeric data-numeric=kk data-exact-length=16',
        'healthCoverageNumber' => 'inputmode=numeric data-exact-length=13',
        'guardianContact' => 'inputmode=tel data-numeric=phone',
        default => '',
    };
@endphp
<div @class(['lrf-field', 'lrf-field-wide' => $wide, 'lrf-bpjs' => $key === 'healthCoverageStatus']) data-resident-field="{{ $key }}" @isset($field['conditional']) data-conditional="{{ $field['conditional'] }}" @endisset>
    @if ($type !== 'checkbox')
        <div class="lrf-label-row">
            <label id="{{ $controlId }}-label" for="{{ $controlId }}">{{ $field['label'] }} <span class="lrf-required" data-resident-required @unless ($field['required'] ?? false) hidden @endunless aria-label="wajib diisi">*</span></label>
            @if (in_array($key, ['nik', 'familyCardNumber'], true))
                <span class="lrf-field-hint" data-resident-counter="{{ $key }}">{{ strlen((string) $value) }}/16</span>
            @endif
        </div>
    @endif
    @if ($type === 'checkbox')
        @php($copy = $choiceCopy[$key] ?? [$field['label'], ''])
        <label @class(['lrf-option', 'is-selected' => $value]) for="{{ $controlId }}">
            <input id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" type="checkbox" @checked($value)>
            <span class="lrf-choice-text"><strong>{{ $copy[0] }}</strong>@if ($copy[1])<small>{{ $copy[1] }}</small>@endif</span>
        </label>
    @elseif ($key === 'gender' || $key === 'healthCoverageStatus')
        <div class="{{ $key === 'gender' ? 'lrf-options' : 'lrf-toggle' }}" role="group" aria-labelledby="{{ $controlId }}-label">
            @foreach ($field['options'] as $index => $choice)
                @php($choiceId = $index ? "{$controlId}-{$index}" : $controlId)
                <label @class(['lrf-option', 'is-selected' => $value === $choice]) for="{{ $choiceId }}">
                    <input id="{{ $choiceId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" type="radio" value="{{ $choice }}" @checked($value === $choice)>
                    @if ($key === 'gender')
                        <span class="lrf-choice-text"><strong>{{ $choice }}</strong></span>
                    @else
                        <span>{{ $choice }}</span>
                    @endif
                </label>
            @endforeach
        </div>
    @elseif ($type === 'select')
        <select id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false">
            <option value="">Pilih…</option>
            @foreach ($field['options'] as $choice)
                <option value="{{ $choice }}" @selected($value === $choice)>{{ $choice }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" rows="3" maxlength="{{ $field['maxLength'] ?? 2000 }}">{{ $value }}</textarea>
    @elseif ($type === 'number')
        <input id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" type="number" value="{{ $value }}" min="{{ $field['min'] ?? 0 }}" max="{{ $field['max'] ?? 125 }}" step="1">
    @elseif ($type === 'date')
        <input id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" type="date" value="{{ $value }}" max="{{ $today }}">
    @else
        <input id="{{ $controlId }}" name="{{ $key }}" aria-describedby="{{ $describedBy }}" aria-invalid="false" type="text" value="{{ $value }}" maxlength="{{ $field['maxLength'] ?? 150 }}" {!! $mode !!}>
    @endif
    <span class="lrf-field-hint" id="{{ $controlId }}-help">{{ $field['hint'] ?? '' }}</span>
    <p class="lrf-field-error" id="{{ $controlId }}-error" hidden></p>
</div>
