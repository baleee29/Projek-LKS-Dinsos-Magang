@extends('layouts.dashboard')

@section('title', $row['subject'])

@php
    use App\Support\Format;
    $extra = [
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 11h18M7 15h2m4 0h2M7 18h2"/>',
        'pin' => '<path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'attachment' => '<path d="m9 13 6-6a3 3 0 0 1 4 4l-8 8a5 5 0 0 1-7-7l9-9"/>',
        'back' => '<path d="M20 12H4m7-7-7 7 7 7"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'badge' => '<rect x="4" y="5" width="16" height="16" rx="2"/><path d="M9 3h6v4H9zM8 17c0-3 8-3 8 0"/><circle cx="12" cy="11" r="2"/>',
        'quote' => '<path d="M4 6h6v6H5c0 3 2 5 4 6M14 6h6v6h-5c0 3 2 5 4 6"/>',
        'external' => '<path d="M14 3h7v7m0-7L10 14M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5"/>',
    ];
    $icon = fn (string $name) => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">'.($extra[$name] ?? '<use href="#icon-'.$name.'"/>').'</svg>';
    $hasPoint = $row['latitude'] !== null;
    $lat = $hasPoint ? number_format($row['latitude'], 6, '.', '') : '';
    $lng = $hasPoint ? number_format($row['longitude'], 6, '.', '') : '';
    $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";
    $embed = $hasPoint ? 'https://www.openstreetmap.org/export/embed.html?bbox='.($row['longitude'] - 0.005).','.($row['latitude'] - 0.005).','.($row['longitude'] + 0.005).','.($row['latitude'] + 0.005)."&layer=mapnik&marker={$lat},{$lng}" : '';
    $phone = '62'.ltrim($row['member_phone'], '0');
    $message = "Halo {$row['member_name']}, terkait pengaduan {$row['complaint_number']} ({$row['subject']}):";
    $hasClient = $row['client_name'] !== '' || $row['client_nik'] !== '';
@endphp

@section('content')
    <section class="psm-complaint-detail-view" id="admin-complaint-detail-view" aria-labelledby="admin-detail-title">
        <div class="psm-complaint-detail-content" id="admin-dlg-content">
            <nav class="psm-complaint-navigation" aria-label="Navigasi detail pengaduan"><a class="text-button" id="admin-detail-back" href="{{ $backUrl }}">{!! $icon('back') !!}Kembali ke Daftar Pengaduan PSM</a><p>Dashboard <span aria-hidden="true">/</span> Pengaduan Anggota PSM <span aria-hidden="true">/</span> <strong>{{ $row['complaint_number'] }}</strong></p></nav>
            <section class="psm-complaint-summary"><div class="psm-complaint-reference"><code>{{ $row['complaint_number'] }}</code><span>{!! $icon('clock') !!}Diajukan pada: {{ Format::dateTime($row['created_at']) }}</span></div><h1 id="admin-detail-title" tabindex="-1">{{ $row['subject'] }}</h1><p>Informasi lengkap laporan kendala lapangan yang disampaikan oleh anggota Pekerja Sosial Masyarakat (PSM).</p></section>
            <div class="psm-complaint-grid">
                <section class="psm-complaint-section">
                    <header class="psm-complaint-card-heading"><span class="psm-complaint-card-icon is-profile">{!! $icon('badge') !!}</span><div><h2>Profil Anggota PSM Pengadu</h2><p>Identitas petugas sosial pencatat di lapangan</p></div></header>
                    <div class="psm-complaint-card-body">
                        <dl class="psm-complaint-identity"><div><dt>Nama Lengkap</dt><dd><strong>{{ $row['member_name'] }}</strong><span class="psm-complaint-member-tag">Anggota PSM</span></dd></div><div><dt>Wilayah Kelurahan</dt><dd>{{ $row['village'] }}</dd></div></dl>
                        <div class="psm-complaint-inset"><span class="psm-complaint-label">Wilayah Pengadu</span><p class="psm-complaint-region">{!! $icon('pin') !!}Kecamatan {{ $row['district'] }} · Kelurahan {{ $row['village'] }}</p><small>Wilayah yang tercatat pada pengaduan ini.</small></div>
                        <div class="psm-complaint-contact"><dl><dt>Kontak / Seluler</dt><dd class="psm-complaint-phone">{{ $row['member_phone'] }}</dd></dl><a class="psm-complaint-whatsapp" href="https://wa.me/{{ $phone }}?text={{ rawurlencode($message) }}" target="_blank" rel="noopener noreferrer">{!! $icon('external') !!}Buka WhatsApp</a></div>
                    </div>
                </section>
                <section class="psm-complaint-section">
                    <header class="psm-complaint-card-heading"><span class="psm-complaint-card-icon">{!! $icon('pin') !!}</span><div><h2>Objek &amp; Titik Lokasi Masalah</h2><p>Identitas Pemerlu Pelayanan Kesejahteraan Sosial (PPKS)</p></div></header>
                    <div class="psm-complaint-card-body">
                        <div class="psm-complaint-inset">
                            <h3>Data Diri Klien / PPKS</h3>
                            @if ($hasClient)
                                <dl class="psm-complaint-client"><div><dt>Nama Klien</dt><dd>{{ $row['client_name'] ?: 'Tidak dicantumkan' }}</dd></div><div><dt>NIK</dt><dd class="psm-complaint-phone">{{ $row['client_nik'] ?: 'Tidak dicantumkan' }}</dd></div><div class="psm-complaint-ktp"><dt>Lampiran KTP</dt><dd>@if ($row['client_ktp'])<button type="button" class="text-button" data-demo-download>{!! $icon('file') !!}Lihat / unduh KTP · {{ $row['client_ktp_original_name'] }}</button>@else Tidak dilampirkan @endif</dd></div></dl>
                            @else
                                <p class="psm-complaint-absent">Tidak dicantumkan oleh anggota.</p>
                            @endif
                        </div>
                        <div class="psm-complaint-location-grid">
                            <div class="psm-complaint-inset">
                                <span class="psm-complaint-label">Titik Koordinat GPS</span>
                                @if ($hasPoint)
                                    <a class="psm-complaint-coordinate" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">{!! $icon('pin') !!}<span>{{ $lat }}, {{ $lng }}</span>{!! $icon('external') !!}</a><small>Buka lokasi di Google Maps</small>
                                @else
                                    <p class="psm-complaint-absent">Tidak dilampirkan</p><small>Koordinat lokasi tidak terekam.</small>
                                @endif
                            </div>
                            <div class="psm-complaint-inset"><span class="psm-complaint-label">Wilayah Laporan</span><p>{{ $row['village'] }}</p><small>{{ $row['district'] }}</small></div>
                        </div>
                        @if ($hasPoint)
                            <iframe class="psm-complaint-map" loading="lazy" title="Peta lokasi pengaduan {{ $row['complaint_number'] }}" referrerpolicy="no-referrer" src="{{ $embed }}"></iframe>
                        @endif
                    </div>
                </section>
            </div>
            <section class="psm-complaint-section psm-complaint-narrative">
                <header class="psm-complaint-card-heading"><span class="psm-complaint-card-icon">{!! $icon('quote') !!}</span><div><h2>Uraian Masalah / Pengaduan Anggota</h2><p>Teks laporan asli yang dikirimkan melalui aplikasi SiDaya</p></div><span class="psm-complaint-field-note">Penyampaian Lapangan</span></header>
                <blockquote class="psm-complaint-quote">{!! $icon('quote') !!}<div><p class="psm-complaint-description">{{ $row['description'] }}</p><footer>{!! $icon('users') !!}{{ $row['member_name'] }} (PSM {{ $row['village'] }})</footer></div></blockquote>
            </section>
            @if (count($row['attachments_list']))
                <section class="psm-complaint-section psm-complaint-evidence">
                    <header class="psm-complaint-card-heading"><span class="psm-complaint-card-icon">{!! $icon('attachment') !!}</span><div><h2>Lampiran Lapangan</h2><p>{{ count($row['attachments_list']) }} berkas yang disertakan anggota dalam pengaduan ini.</p></div></header>
                    <div class="psm-complaint-attachments">
                        @foreach ($row['attachments_list'] as $file)
                            <button type="button" class="psm-complaint-attachment" data-demo-download><span class="psm-complaint-attachment-icon">{!! $icon('file') !!}</span><strong>{{ $file['name'] }}</strong><span>{{ number_format($file['size'] / 1024, 1, ',', '.') }} KB · Buka berkas</span></button>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
