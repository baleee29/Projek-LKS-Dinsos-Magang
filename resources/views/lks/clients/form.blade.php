@extends('layouts.dashboard')

@php
    $pageTitle = $item ? 'Edit Data Klien' : 'Tambah Klien & Anak Asuh LKS';
    $back = $item ? '/lks/resident/'.$item['id'] : '/lks/occupancy';
    $vulnerability = ['orphanFather', 'orphanMother', 'economicallyVulnerable'];
    $schemaJson = collect($fields)
        ->map(fn ($field) => ['label' => $field['label'], 'required' => $field['required'] ?? false, 'conditional' => $field['conditional'] ?? null, 'type' => $field['type'] ?? 'text'])
        ->toJson(JSON_HEX_TAG | JSON_UNESCAPED_UNICODE);
@endphp

@section('title', $pageTitle)

@push('scripts')
    @vite(['resources/js/pages/client-form.js'])
@endpush

@section('content')
    <div class="lks-resident-form-page" data-client-form-page>
        <a class="lrf-back-link" href="{{ $back }}">← Kembali ke {{ $item ? 'klien' : 'rekap klien' }}</a>
        <div class="lrf-heading">
            <div>
                <p class="lrf-meta"><span class="lrf-module">KLIEN LKS</span><span>{{ $organization['title'] }}</span></p>
                <h1 id="view-title" tabindex="-1">{{ $pageTitle }}</h1>
                <p class="lrf-description">{{ $item ? 'Perubahan biodata klien langsung disimpan dan diperbarui di sistem Admin.' : 'Lengkapi identitas klien. Biodata klien langsung terdaftar aktif tanpa verifikasi Admin.' }}</p>
            </div>
            <div class="lrf-heading-actions">
                @unless ($item)
                    <button class="lrf-secondary" type="button" data-resident-demo>Isi data contoh</button>
                @endunless
            </div>
        </div>
        @if ($item)
            <p class="lrf-note" style="border-left-color:#22c55e;">Biodata klien ini sudah terdaftar. Perubahan data akan langsung disimpan dan terlihat di akun Admin.</p>
        @endif
        <nav aria-label="Bagian formulir klien">
            <ol class="lrf-steps">
                @foreach ($sections as $index => $section)
                    <li><button @class(['lrf-step', 'is-active' => $index === 0]) type="button" data-resident-section="{{ $section['key'] }}" aria-controls="lrf-section-{{ $section['key'] }}" @if ($index === 0) aria-current="step" @endif><span class="lrf-step-number" aria-hidden="true">{{ $index + 1 }}</span><span class="lrf-step-copy"><strong>Bagian {{ $section['letter'] }}</strong><span>{{ $section['short'] }}</span></span></button></li>
                @endforeach
            </ol>
        </nav>
        <div class="lrf-layout">
            <form id="resident-form" class="lrf-form" novalidate>
                <div class="q-errors" id="resident-form-errors" role="alert" tabindex="-1" hidden></div>
                @foreach ($sections as $section)
                    <fieldset class="lrf-card" id="lrf-section-{{ $section['key'] }}" data-section-fields="{{ implode(',', $section['fields']) }}">
                        <legend class="sr-only">Bagian {{ $section['letter'] }}: {{ $section['title'] }}</legend>
                        <div class="lrf-card-heading">
                            <span class="lrf-section-letter" aria-hidden="true">{{ $section['letter'] }}</span>
                            <div class="lrf-section-copy"><h2 id="lrf-title-{{ $section['key'] }}" tabindex="-1">{{ $section['title'] }}</h2><p>{{ $section['description'] }}</p></div>
                            <span class="lrf-section-tag">{{ $section['tag'] }}</span>
                        </div>
                        <div class="lrf-fields">
                            @if ($section['key'] === 'a')
                                <div class="lrf-field lrf-field-wide lks-photo-upload-group">
                                    <div class="lrf-label-row"><label>Foto Profil Klien <small style="font-weight:normal;color:var(--lrf-muted);">(opsional)</small></label></div>
                                    <div class="lks-photo-uploader">
                                        <div class="lks-photo-preview" id="client-photo-preview"><div class="lks-photo-placeholder"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div></div>
                                        <div class="lks-photo-uploader-controls">
                                            <input type="file" id="client-photo-input" accept="image/jpeg,image/png,image/webp" class="sr-only" style="width:1px;min-width:0;">
                                            <div class="lks-photo-btn-row">
                                                <label for="client-photo-input" class="lrf-secondary lks-button-upload" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;">Pilih Foto Profil</label>
                                                <button type="button" class="lrf-secondary" id="client-photo-remove" style="display:none;">Hapus Foto</button>
                                            </div>
                                            <p class="lrf-field-hint" style="margin:4px 0 0;">Format JPG, PNG, atau WebP (maks. 5MB). Foto akan disesuaikan otomatis untuk profil klien.</p>
                                        </div>
                                    </div>
                                    <p class="lrf-field-error" id="client-photo-error" hidden></p>
                                </div>
                            @endif
                            @php($riskRendered = false)
                            @foreach ($section['fields'] as $key)
                                @if (in_array($key, $vulnerability, true))
                                    @continue($riskRendered)
                                    @php($riskRendered = true)
                                    <div class="lrf-field-wide">
                                        <p class="lrf-field-hint">Kategori kerentanan (opsional)</p>
                                        <div class="lrf-options">
                                            @foreach ($vulnerability as $vulnerabilityKey)
                                                @include('lks.clients.field', ['key' => $vulnerabilityKey])
                                            @endforeach
                                        </div>
                                        <p class="lrf-note">Jika kedua orang tua telah meninggal, pilihan Yatim dan Piatu dapat dicatat bersama.</p>
                                    </div>
                                @else
                                    @include('lks.clients.field', ['key' => $key])
                                @endif
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
                <div class="lrf-footer">
                    <div class="lrf-footer-note"><strong>{{ $item ? 'Pembaruan Data Klien' : 'Pencatatan Klien' }}</strong><p>Biodata klien langsung tersimpan dan tercatat di sistem tanpa memerlukan verifikasi Admin.</p></div>
                    <div class="lrf-actions">
                        @if ($item)
                            <button class="lrf-danger" type="button" data-open-dialog="resident-delete-dialog">Hapus Klien</button>
                        @endif
                        <a class="lrf-secondary" href="{{ $back }}">Batal</a>
                        <button class="lrf-primary" type="submit">{{ $item ? 'Simpan Perubahan' : 'Simpan Klien' }}</button>
                    </div>
                </div>
            </form>
            <aside class="lrf-sidebar" aria-label="Pratinjau dan panduan biodata">
                <section class="lrf-preview-card"><div class="lrf-panel-heading"><h2>Pratinjau Kartu Klien</h2><span class="lrf-section-tag" data-resident-preview-tag>Pratinjau Kartu</span></div><div data-resident-preview></div></section>
                <section class="lrf-progress-card">
                    <div class="lrf-progress-heading"><h2>Kelengkapan Formulir</h2><strong data-resident-progress-text>0%</strong></div>
                    <div class="lrf-progress-track" role="progressbar" aria-label="Kelengkapan isian wajib" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span data-resident-progress-bar></span></div>
                    <p class="lrf-field-hint" data-resident-progress-note></p>
                    <ul class="lrf-progress-list">
                        @foreach ($sections as $section)
                            <li data-resident-progress-section="{{ $section['key'] }}"><span data-resident-progress-mark aria-hidden="true">○</span><span>Bagian {{ $section['letter'] }}: {{ $section['short'] }}</span></li>
                        @endforeach
                    </ul>
                </section>
                <section class="lrf-help-card"><h2>Panduan Petugas LKS</h2><p>NIK dan nomor KK dapat dikosongkan jika belum tersedia. Jika tanggal lahir belum diketahui, isi perkiraan usia.</p><p>Bedakan layanan tinggal di panti dan nonresidensial. Data klien langsung masuk ke rekap klien terdaftar.</p></section>
            </aside>
        </div>
    </div>
    @if ($item)
        <dialog class="lrf-dialog" id="resident-delete-dialog" aria-labelledby="lrf-delete-title" aria-describedby="lrf-delete-description">
            <h2 id="lrf-delete-title">Hapus klien?</h2>
            <p id="lrf-delete-description">Biodata <strong>{{ $item['name'] }}</strong> beserta foto dan berkasnya akan dihapus permanen dari sistem dan tidak lagi tampil di rekap klien maupun akun Admin.</p>
            <p class="lrf-dialog-note">Jika klien hanya berhenti menerima layanan, ubah Status Tinggal menjadi "Sudah keluar" agar riwayatnya tetap tersimpan.</p>
            <div class="lrf-actions"><button class="lrf-secondary" type="button" data-close-dialog>Batal</button><button class="lrf-danger" type="button" data-demo-action>Ya, Hapus Klien</button></div>
        </dialog>
    @endif
    <script type="application/json" id="client-form-schema">{!! $schemaJson !!}</script>
@endsection
