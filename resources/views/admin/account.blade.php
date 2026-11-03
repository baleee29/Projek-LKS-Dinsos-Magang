@extends('layouts.dashboard')

@section('title', 'Pengaturan Akun')

@php
    $fields = [
        ['key' => 'name', 'label' => 'Nama lengkap', 'type' => 'text', 'autocomplete' => 'name', 'maximum' => 100, 'help' => ''],
        ['key' => 'email', 'label' => 'Email kontak', 'type' => 'email', 'autocomplete' => 'email', 'maximum' => 254, 'help' => 'Digunakan sebagai kontak pada biodata admin.'],
        ['key' => 'position', 'label' => 'Jabatan', 'type' => 'text', 'autocomplete' => 'organization-title', 'maximum' => 100, 'help' => ''],
    ];
@endphp

@push('scripts')
    @vite(['resources/js/pages/account-settings.js'])
@endpush

@section('content')
    <x-dashboard.section-heading title="Pengaturan Akun" description="Perbarui biodata dan tampilan jumlah notifikasi." />
    {{-- TODO(Tahap 2): PUT /account/profile. --}}
    <form id="admin-account-form" class="detail-card q-account-form" novalidate data-demo-form>
        <div id="account-error-summary" class="q-errors" role="alert" tabindex="-1" hidden></div>
        <fieldset class="q-account-section">
            <legend>Biodata</legend>
            <p class="q-inline-note">Isi nama, email kontak, dan jabatan Anda.</p>
            <div class="q-row-grid">
                @foreach ($fields as $field)
                    <div class="q-field">
                        <label for="account-{{ $field['key'] }}">{{ $field['label'] }} <span class="q-account-required">(wajib)</span></label>
                        <input id="account-{{ $field['key'] }}" name="{{ $field['key'] }}" type="{{ $field['type'] }}" value="{{ $account[$field['key']] }}" autocomplete="{{ $field['autocomplete'] }}" maxlength="{{ $field['maximum'] }}" required data-label="{{ $field['label'] }}" aria-describedby="{{ $field['help'] ? "account-{$field['key']}-help " : '' }}account-{{ $field['key'] }}-error" aria-invalid="false">
                        @if ($field['help'])
                            <span id="account-{{ $field['key'] }}-help" class="q-field-help">{{ $field['help'] }}</span>
                        @endif
                        <p class="q-field-error" id="account-{{ $field['key'] }}-error" hidden></p>
                    </div>
                @endforeach
            </div>
        </fieldset>
        <fieldset class="q-account-section" aria-describedby="account-notification-help">
            <legend>Notifikasi</legend>
            <p class="q-inline-note" id="account-notification-help">Seluruh data masuk, perubahan, penghapusan, dan perubahan status dicatat otomatis. Pilihan ini mengatur tampilan jumlah belum dibaca.</p>
            <div class="q-account-preferences">
                <label class="q-account-preference" for="account-showUnreadBadge"><input id="account-showUnreadBadge" name="showUnreadBadge" type="checkbox" checked><span><strong>Tampilkan jumlah belum dibaca di sidebar</strong><span class="q-field-help">Tampilkan angka pada menu Notifikasi.</span></span></label>
            </div>
        </fieldset>
        <p id="account-unsaved" class="q-inline-note" role="status" aria-live="polite" hidden>Ada perubahan yang belum disimpan.</p>
        <div class="q-form-footer"><span id="account-updated"></span><button class="text-button" id="account-reset" type="button">Batalkan perubahan</button><button class="export-button" type="submit">Simpan perubahan</button></div>
    </form>
@endsection
