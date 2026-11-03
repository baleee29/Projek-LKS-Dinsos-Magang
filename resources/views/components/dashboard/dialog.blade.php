@props(['id', 'title', 'class' => 'detail-dialog q-dialog', 'actions' => null])
{{-- Sama dengan dialog() di shared/workspace-profile.js. Isi dan tombol dirender server, dibuka lewat data-open-dialog. --}}
<dialog id="{{ $id }}" class="{{ $class }}" aria-labelledby="{{ $id }}-title">
    <h2 id="{{ $id }}-title">{{ $title }}</h2>
    {{ $slot }}
    @if ($actions)
        <div class="psm-form-actions">{{ $actions }}</div>
    @endif
</dialog>
