@extends('layouts.dashboard')

@php
    $pageTitle = $item ? 'Edit draf surat' : 'Buat surat balasan';
@endphp

@section('title', $pageTitle)

@section('content')
    <section class="admin-correspondence-view">
        @include('admin.correspondence.heading', ['title' => $pageTitle, 'description' => 'Isi metadata surat yang sebenarnya dan unggah berkas. Draf belum dapat dilihat penerima.', 'back' => 'reply-letters/karang-taruna'])
        {{-- TODO(Tahap 2): POST /admin/department-replies. --}}
        <form class="admin-correspondence-card admin-correspondence-form" data-demo-form novalidate>
            <div class="admin-correspondence-form-grid">
                <label><span>Organisasi penerima <b>*</b></span>
                    <select name="organizationId" required data-label="Organisasi penerima" aria-describedby="reply-organization-error">
                        <option value="">Pilih organisasi</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org['id'] }}" @selected($values['organizationId'] === $org['id'])>{{ $org['title'] }}</option>
                        @endforeach
                    </select>
                    <small class="field-error" id="reply-organization-error" hidden></small>
                </label>
                <label><span>Pengajuan kegiatan terkait, jika ada</span>
                    <select name="activityReference">
                        <option value="">Tidak terkait pengajuan</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity['id'] }}" @selected($values['activityId'] === $activity['id'])>{{ $activity['title'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="is-wide"><span>Judul / perihal surat <b>*</b></span>
                    <input name="title" value="{{ $values['subject'] }}" maxlength="180" required data-label="Judul surat" aria-describedby="reply-title-error">
                    <small class="field-error" id="reply-title-error" hidden></small>
                </label>
                <label><span>Nomor surat, jika ada</span><input name="number" value="{{ $values['number'] }}" maxlength="120" placeholder="Masukkan nomor yang telah ditetapkan Dinas"></label>
                <label><span>Tanggal surat <b>*</b></span>
                    <input type="date" name="issuedAt" value="{{ $values['date'] }}" required data-label="Tanggal surat" aria-describedby="reply-date-error">
                    <small class="field-error" id="reply-date-error" hidden></small>
                </label>
                <label class="is-wide"><span>Berkas surat <b>*</b></span>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" data-max-mb="5">
                    <small>PDF, JPG, atau PNG; maksimal 5 MB. {{ ($item['file']['name'] ?? null) ? 'Berkas tersimpan: '.$item['file']['name'].'. Pilih berkas baru untuk menggantinya.' : 'Pilih berkas asli dari perangkat Anda.' }}</small>
                </label>
            </div>
            <p>Kolom bertanda * harus lengkap sebelum penerbitan. Draf boleh disimpan terlebih dahulu. Nomor surat dan tanda tangan tidak dibuat otomatis.</p>
            <div class="admin-correspondence-actions">
                <button class="admin-correspondence-button is-primary" type="submit">Simpan draf surat</button>
                <a class="admin-correspondence-button" href="/admin/{{ $item ? 'reply-letter/'.$item['id'] : 'reply-letters/karang-taruna' }}">Batal</a>
            </div>
        </form>
    </section>
@endsection
