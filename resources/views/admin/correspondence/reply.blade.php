@extends('layouts.dashboard')

@section('title', $item['subject'])

@php
    use App\Support\Format;
    $fields = [
        'Organisasi penerima' => $organization['title'],
        'Pengajuan terkait' => $activity['title'] ?? null,
        'Nomor surat' => $item['number'],
        'Tanggal surat' => Format::date($item['date']),
        'Diterbitkan' => $item['publishedAt'] ? Format::dateTime($item['publishedAt']) : null,
        'Nama berkas' => $item['file']['name'] ?? null,
    ];
    $history = [['status' => 'Draf', 'at' => $item['updated'], 'actor' => 'Admin', 'note' => 'Draf surat disimpan.']];
    if ($item['publishedAt']) {
        $history[] = ['status' => 'Diterbitkan', 'at' => $item['publishedAt'], 'actor' => 'Admin', 'note' => 'Surat diterbitkan kepada organisasi penerima.'];
    }
    $action = $ownDraft ? '<a class="admin-correspondence-button" href="/admin/edit-reply-letter/'.e($item['id']).'">Edit draf</a>' : null;
@endphp

@push('scripts')
    @vite(['resources/js/pages/correspondence.js'])
@endpush

@section('content')
    <section class="admin-correspondence-view">
        @include('admin.correspondence.heading', ['title' => $item['subject'], 'description' => 'Metadata, berkas asli, dan riwayat surat balasan.', 'action' => $action, 'back' => 'reply-letters/karang-taruna'])
        <div class="admin-correspondence-grid">
            <div>
                <section class="admin-correspondence-card">
                    <h2>Surat balasan</h2>
                    @include('admin.correspondence.status', ['status' => $item['status']])
                    <dl class="admin-correspondence-detail">
                        @foreach ($fields as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                        @endforeach
                    </dl>
                    @if ($item['file'])
                        <button class="admin-correspondence-button" type="button" data-demo-download>Unduh berkas</button>
                    @else
                        <p>Berkas belum diunggah.</p>
                    @endif
                </section>
                @include('admin.correspondence.history', ['history' => $history])
            </div>
            <aside>
                {{-- TODO(Tahap 2): POST /admin/department-replies/{id}/publish dan DELETE /admin/department-replies/{id}. --}}
                <section class="admin-correspondence-card" data-reply-actions>
                    <h2>Penerbitan surat</h2>
                    <p>Surat yang diterbitkan muncul pada Surat Balasan Dinas organisasi penerima. Penerbitan tidak mengubah keputusan pengajuan kegiatan.</p>
                    @if ($ownDraft)
                        <label class="admin-correspondence-confirm"><input type="checkbox" data-correspondence-confirm><span>Saya telah memeriksa penerima, metadata, dan berkas surat.</span></label>
                        <button class="admin-correspondence-button is-primary" type="button" data-guarded-by="data-correspondence-confirm" data-demo-action data-confirm="Surat akan terlihat oleh organisasi penerima." data-confirm-title="Terbitkan surat?" data-confirm-text="Ya, terbitkan" data-confirm-safe disabled>Terbitkan surat</button>
                        <hr>
                        <label class="admin-correspondence-confirm"><input type="checkbox" data-correspondence-delete-confirm><span>Hapus draf ini beserta berkas yang tersimpan.</span></label>
                        <button class="admin-correspondence-button is-danger" type="button" data-guarded-by="data-correspondence-delete-confirm" data-demo-action disabled>Hapus draf</button>
                    @else
                        <p>Surat telah diterbitkan. Metadata dan berkas tidak dapat diubah.</p>
                        <hr>
                        <label class="admin-correspondence-confirm"><input type="checkbox" data-correspondence-delete-confirm><span>Hapus surat balasan ini beserta berkas yang tersimpan.</span></label>
                        <button class="admin-correspondence-button is-danger" type="button" data-guarded-by="data-correspondence-delete-confirm" data-demo-action disabled>Hapus surat</button>
                    @endif
                </section>
            </aside>
        </div>
    </section>
@endsection
