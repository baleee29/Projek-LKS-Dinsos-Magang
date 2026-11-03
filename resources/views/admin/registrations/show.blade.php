@extends('layouts.dashboard')

@section('title', 'Pendaftaran '.$label)

@php
    use App\Support\Format;
    use App\Support\Demo\DemoLetters;
    use App\Support\Demo\DemoRegistrations;
    $pending = $row['status'] === 'Menunggu Verifikasi';
    $approved = $row['status'] === 'Disetujui';
    $isLks = $row['pillar'] === 'lks';
    $fields = $isLks
        ? ['Nama Yayasan / LKS' => $row['organization_name'], 'Nama Pengisi Data' => $row['leader_name'], 'Nomor WhatsApp yang Bisa Dihubungi' => $row['whatsapp'], 'Nomor Izin Operasional Lama' => $row['sk_number'], 'Fokus Pelayanan / Bidang Garap' => $row['focus_area'], 'Kecamatan' => $row['district'], 'Kelurahan' => $row['village'], 'Alamat Sekretariat' => $row['address']]
        : ['Nama Kelompok' => $row['organization_name'], 'Tanggal Berdiri' => Format::date($row['establishment_date']), 'Nama Ketua' => $row['leader_name'], 'No. HP / WhatsApp' => $row['whatsapp'], 'Alamat Sekretariat' => $row['address'], 'Kecamatan' => $row['district'], 'Kelurahan' => $row['village'], 'Nomor SK Kelurahan' => $row['sk_number']];
    $paths = [
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/>',
        'back' => '<path d="M20 12H4m7-7-7 7 7 7"/>',
        'message' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/>',
        'building' => '<path d="M4 21V4h12v17M16 10h4v11M2 21h20M8 8h4M8 12h4"/>',
        'key' => '<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8m-3 3 3 3"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
        'download' => '<path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
    ];
    $icon = fn (string $name) => '<svg class="lrd-icon" viewBox="0 0 24 24" aria-hidden="true">'.$paths[$name].'</svg>';
@endphp

@push('scripts')
    @vite(['resources/js/pages/registration-review.js'])
@endpush

@section('content')
    <section class="admin-registration-view admin-lks-registration-detail" aria-labelledby="view-title">
        <header class="lrd-heading"><div><p class="lrd-eyebrow">SiDaya &middot; Admin Dinas Sosial</p><h1 id="view-title" tabindex="-1">Pendaftaran {{ $label }}</h1><p class="lrd-organization">{{ $row['organization_name'] }}</p></div><span class="lrd-verification-badge"><span aria-hidden="true"></span>Verifikasi Berkas</span></header>
        <div class="lrd-notice">{!! $icon('info') !!}<p>Pengajuan dan berkas berasal dari pendaftaran mandiri pilar sosial. Persetujuan menambahkan lembaga ke direktori organisasi.</p></div>
        <a class="admin-registration-button lrd-back" href="/admin/registrations/{{ $row['pillar'] }}">{!! $icon('back') !!}Daftar pendaftaran</a>
        <section class="lrd-card lrd-identity" aria-labelledby="lrd-reference">
            <header class="lrd-record-heading"><div><div class="lrd-reference-line"><h2 id="lrd-reference">Status pendaftaran</h2>@include('admin.registrations.status', ['status' => $row['status']])</div><p>Dikirim {{ Format::dateTime($row['created_at']) }} &middot; Versi 1</p></div></header>
            @if ($row['notes'])
                <div class="lrd-latest-note">{!! $icon('message') !!}<div><h3>Catatan terakhir:</h3><p>{{ $row['notes'] }}</p></div></div>
            @endif
            <dl class="lrd-fields">
                @foreach ($fields as $title => $value)
                    <div><dt>{{ $title }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                @endforeach
            </dl>
            <footer class="lrd-identity-actions">
                @if ($approved && $organizationId)
                    <a class="admin-registration-button" href="/admin/organization/{{ $organizationId }}">{!! $icon('building') !!}Lihat lembaga di direktori organisasi</a>
                @endif
                @if ($approved)
                    <a class="admin-registration-button is-primary" href="/admin/partner-accounts?from_reg={{ $row['id'] }}">{!! $icon('key') !!}Buat Akun Mitra</a>
                @endif
            </footer>
        </section>
        <section class="lrd-document-section" aria-labelledby="lrd-documents-title">
            <header class="lrd-section-heading"><h2 id="lrd-documents-title">Dokumen pendaftaran</h2><p>{{ count($row['files']) }} Berkas Tersedia</p></header>
            <ul class="lrd-documents">
                @foreach ($row['files'] as $file)
                    <li><span class="lrd-document-icon">{!! $icon('file') !!}</span><div><h3>{{ $file['label'] }}</h3><p>{{ $file['name'] }} &middot; {{ DemoLetters::fileSize($file['size']) }}</p></div><button type="button" class="admin-registration-button" data-demo-download aria-label="Unduh {{ $file['label'] }}">{!! $icon('download') !!}Unduh</button></li>
                @endforeach
            </ul>
        </section>
        @if ($pending)
            {{-- TODO(Tahap 2): POST /admin/partner-registrations/{id}/review. Alasan penolakan wajib minimal 10 karakter. --}}
            <section class="lrd-card lrd-review" aria-labelledby="lrd-review-title" data-registration-review data-whatsapp="{{ $whatsappUrl }}">
                <h2 id="lrd-review-title">Keputusan Admin</h2>
                <p>Periksa identitas dan seluruh berkas, lalu terima atau tolak. Penolakan wajib menyertakan alasan; alasan ini dikirim ke pendaftar melalui WhatsApp.</p>
                <label class="admin-registration-review-label" for="registration-review-note">Alasan penolakan<textarea id="registration-review-note" rows="4" maxlength="2000" placeholder="Wajib diisi bila menolak, minimal 10 karakter"></textarea></label>
                <div class="admin-registration-actions">
                    <button type="button" class="admin-registration-button" data-action="reject">Tolak Pendaftaran</button>
                    <button type="button" class="admin-registration-button is-primary" data-action="approve">Terima Pendaftaran</button>
                </div>
                <p data-review-error role="alert"></p>
                <p data-review-whatsapp hidden><a class="admin-registration-button is-primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Kirim alasan via WhatsApp</a></p>
            </section>
        @else
            <div class="lrd-notice lrd-completion" data-status="{{ $row['status'] }}">
                {!! $icon($approved ? 'check' : 'info') !!}
                <p>
                    @if ($approved)
                        Pendaftaran diterima. Buat akun di menu Akun Mitra, lalu kirim data login melalui WhatsApp.
                    @else
                        Pendaftaran ditolak. <a class="admin-registration-button is-primary" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Kirim alasan via WhatsApp</a>
                    @endif
                </p>
            </div>
        @endif
        <section class="lrd-card lrd-history-card" aria-labelledby="lrd-history-title">
            <h2 id="lrd-history-title">Riwayat pendaftaran</h2>
            <ol class="lrd-history">
                @foreach ($row['history'] as $event)
                    <li data-status="{{ $event['status'] }}"><span class="lrd-timeline-dot" aria-hidden="true"></span><h3>{{ $loop->iteration }}. {{ DemoRegistrations::statusLabel($event['status']) }}</h3><p class="lrd-event-date">{{ Format::dateTime($event['at']) }} &middot; {{ $event['actor'] }}</p>@if ($event['note'])<p class="lrd-event-note">{{ $event['note'] }}</p>@endif</li>
                @endforeach
            </ol>
        </section>
    </section>
@endsection
