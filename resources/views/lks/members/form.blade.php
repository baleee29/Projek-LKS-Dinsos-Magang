@extends('layouts.dashboard')

@php
    $pageTitle = $member ? 'Perbarui Data SDM' : 'Tambah Personel LKS';
    $labels = [
        'title' => 'Nama lengkap', 'nik' => 'NIK', 'birthPlace' => 'Tempat lahir', 'birthDate' => 'Tanggal lahir',
        'gender' => 'Jenis kelamin', 'contact' => 'Nomor HP / WhatsApp', 'ktpAddress' => 'Alamat KTP', 'npwp' => 'NPWP',
        'category' => 'Kategori personel', 'position' => 'Jabatan / peran', 'joined' => 'Tanggal bergabung', 'notes' => 'Keterangan',
    ];
    $attachments = $member['attachments'] ?? [];
@endphp

@section('title', $pageTitle)

@push('scripts')
    @vite(['resources/js/pages/member-form.js'])
@endpush

@section('content')
    <section class="lm-workspace">
        <header class="lm-heading">
            <div><h1 id="view-title" tabindex="-1">{{ $pageTitle }}</h1><p>Lengkapi identitas personel LKS.</p></div>
            <div class="lm-actions"><a class="lm-button" href="/lks/members">Kembali</a></div>
        </header>
        <p class="lm-demo-note">Personel yang disimpan langsung masuk daftar Pengurus & SDM.</p>
        <form class="lm-card lm-form q-member-form" novalidate data-form-url="{{ $member ? '/lks/members/'.$member['id'] : '/lks/members' }}" data-member-form>
            @if ($member)<input type="hidden" name="version" value="{{ $member['version'] }}">@endif
            <input type="hidden" name="removePhoto" value="0" id="lm-remove-photo">
            <div class="lm-error" role="alert" tabindex="-1" data-lm-error hidden></div>
            <div class="lm-photo-section">
                <div class="lm-photo-preview-wrapper">
                    <div class="lm-photo-preview" id="lm-photo-preview"><span class="lm-photo-placeholder" aria-hidden="true">{{ mb_strtoupper(mb_substr($values['title'] ?? 'SDM', 0, 2)) }}</span></div>
                </div>
                <div class="lm-photo-meta">
                    <label for="lm-photo-input" class="lm-photo-label">Foto Profil Personel</label>
                    <p>Format JPG, PNG, atau WebP, maksimal 5 MB. Foto akan tampil di halaman pengurus & SDM.</p>
                    <div class="lm-photo-actions">
                        <label class="lm-button lm-button-upload" for="lm-photo-input"><input id="lm-photo-input" name="photo" type="file" accept="image/jpeg,image/png,image/webp" style="display:none"><span>Upload Foto Profil</span></label>
                        <button type="button" class="lm-button lm-button-danger" id="lm-photo-remove" hidden>Hapus Foto</button>
                    </div>
                    <small class="lm-field-error" id="lm-photo-error" hidden></small>
                </div>
            </div>
            <fieldset>
                <legend>Identitas personel</legend>
                <p>Lengkapi identitas personel. NPWP dan berkas pendukung dapat dikosongkan.</p>
                <div class="lm-form-grid">
                    @include('lks.members.field', ['key' => 'title', 'attrs' => 'maxlength=150 autocomplete=name'])
                    @include('lks.members.field', ['key' => 'nik', 'attrs' => 'inputmode=numeric maxlength=16 data-numeric=nik data-exact-length=16'])
                    @include('lks.members.field', ['key' => 'birthPlace', 'attrs' => 'maxlength=150'])
                    @include('lks.members.field', ['key' => 'birthDate', 'type' => 'date', 'attrs' => 'max='.$today])
                    @include('lks.members.field', ['key' => 'gender', 'options' => ['Laki-laki', 'Perempuan']])
                    @include('lks.members.field', ['key' => 'contact', 'type' => 'tel', 'attrs' => 'maxlength=40 autocomplete=tel data-numeric=phone'])
                    @include('lks.members.field', ['key' => 'ktpAddress', 'type' => 'textarea', 'maxlength' => 1000])
                    @include('lks.members.field', ['key' => 'npwp', 'required' => false, 'attrs' => 'maxlength=150'])
                </div>
            </fieldset>
            <fieldset>
                <legend>Keanggotaan</legend>
                <div class="lm-form-grid">
                    @include('lks.members.field', ['key' => 'category', 'options' => ['Pengurus', 'Staf', 'Tenaga layanan', 'Relawan']])
                    @include('lks.members.field', ['key' => 'position', 'required' => false, 'attrs' => 'maxlength=150'])
                    @include('lks.members.field', ['key' => 'joined', 'type' => 'date', 'required' => false, 'attrs' => 'max='.$today])
                    @include('lks.members.field', ['key' => 'notes', 'type' => 'textarea', 'required' => false, 'maxlength' => 2000])
                </div>
            </fieldset>
            <fieldset>
                <legend>Berkas pendukung (jika ada)</legend>
                <p>PDF, JPG, atau PNG, maksimal 5 MB per berkas. Anda dapat memilih beberapa berkas sekaligus (bisa ditumpuk).</p>
                @if (count($attachments))
                    <p>Berkas tersimpan ({{ count($attachments) }}): {{ collect($attachments)->pluck('name')->join(', ') }}</p>
                    <label class="lm-checkbox"><input type="checkbox" name="removeAttachment" value="1"> Hapus berkas tersimpan</label>
                @endif
                <label for="lm-attachment">Pilih berkas pendukung (bisa lebih dari 1)<input id="lm-attachment" name="attachments[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" data-max-mb="5"><small class="lm-field-error" data-lm-field-error="attachments" hidden></small></label>
            </fieldset>
            <div class="lm-actions"><a class="lm-button" href="/lks/members">Batal</a><button type="submit" class="lm-button lm-button-primary">Simpan Personel</button></div>
        </form>
    </section>
@endsection
