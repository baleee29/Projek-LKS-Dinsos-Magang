@extends('layouts.dashboard')

@section('title', 'Detail Anggota')

@php
    use App\Support\Format;
    $value = fn (string $label, ?string $text) => '<div class="psm-value"><dt>'.e($label).'</dt><dd>'.e($text ?: 'Belum dicatat').'</dd></div>';
    $hasExtra = collect(['nik', 'birthPlace', 'birthDate', 'gender', 'npwp'])->contains(fn ($key) => $item[$key]);
    $attachments = $item['attachments'];
    $phone = '62'.ltrim($item['contact'], '0');
    $status = '<span class="psm-status psm-status-approved">Terdaftar</span>';
@endphp

@section('content')
    <section class="psm-members-view psm-detail-view">
        <div class="psm-back"><a class="psm-button" href="/admin/members/psm"><x-dashboard.lks-icon name="arrow-left" />Kembali ke daftar anggota</a></div>
        <header class="psm-page-heading"><div><p class="psm-eyebrow">PSM · Kota Banjarbaru</p><div class="psm-title-line"><h1 id="view-title" tabindex="-1">Detail Anggota</h1>{!! $status !!}</div><p>Identitas dan domisili anggota PSM.</p></div></header>
        <div class="psm-detail-grid">
            <div class="psm-detail-main">
                <section class="psm-card psm-detail-card">
                    <h2><span class="psm-section-letter">A</span>Identitas Anggota</h2>
                    <div class="psm-detail-person"><span class="psm-initials psm-initials-large" aria-hidden="true">{{ Format::initials($item['title']) }}</span><div><h3>{{ $item['title'] }}</h3><p>{{ $item['position'] ?: 'Anggota PSM' }}</p></div></div>
                    <dl class="psm-values">
                        {!! $value('Nama lengkap', $item['title']) !!}
                        {!! $value('Pekerjaan', $item['occupation']) !!}
                        <div class="psm-value"><dt>Nomor WhatsApp</dt><dd><span data-psm-phone>{{ $item['contact'] }}</span><div class="psm-contact-actions"><a class="psm-button psm-button-primary" href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener noreferrer">Hubungi WhatsApp</a></div></dd></div>
                        {!! $value('Tanggal terdaftar', Format::date($item['joined'])) !!}
                        {!! $value('Organisasi', $organization['title']) !!}
                    </dl>
                </section>
                <section class="psm-card psm-detail-card">
                    <h2><span class="psm-section-letter">B</span>Domisili</h2>
                    <dl class="psm-values">
                        {!! $value('Kecamatan', $item['district']) !!}
                        {!! $value('Kelurahan', $item['village']) !!}
                        <div class="psm-value psm-value-wide"><dt>Alamat</dt><dd>{{ $item['ktpAddress'] ?: 'Belum dicatat' }}</dd></div>
                    </dl>
                </section>
                @if ($hasExtra)
                    <section class="psm-card psm-detail-card">
                        <h2>Biodata tambahan</h2>
                        <dl class="psm-values">
                            {!! $value('NIK jika ada', $item['nik']) !!}
                            {!! $value('Tempat lahir', $item['birthPlace']) !!}
                            {!! $value('Tanggal lahir', $item['birthDate'] ? Format::date($item['birthDate']) : '') !!}
                            {!! $value('Jenis kelamin', $item['gender']) !!}
                            {!! $value('NPWP jika ada', $item['npwp']) !!}
                        </dl>
                    </section>
                @endif
                @if ($item['notes'])
                    <section class="psm-card psm-detail-card"><h2>Catatan</h2><p class="psm-preserve-lines">{{ $item['notes'] }}</p></section>
                @endif
                <section class="psm-card psm-detail-card">
                    <h2><span class="psm-section-letter">{{ $hasExtra ? 'D' : 'C' }}</span>Berkas Pendukung @if (count($attachments))({{ count($attachments) }})@endif</h2>
                    @if (count($attachments))
                        <div class="psm-attachment-stack">
                            @foreach ($attachments as $attachment)
                                <div class="psm-attachment-card"><div class="psm-attachment-icon"><x-dashboard.lks-icon name="file" /></div><div class="psm-attachment-info"><strong>{{ $attachment['name'] }}</strong><small>{{ ceil($attachment['size'] / 1024) }} KB · {{ $attachment['type'] }}</small></div><div class="psm-attachment-actions"><button class="psm-button psm-button-primary psm-button-sm" type="button" data-demo-download><x-dashboard.lks-icon name="download" />Unduh Berkas</button></div></div>
                            @endforeach
                        </div>
                    @else
                        <p class="psm-help">Tidak ada berkas pendukung yang dilampirkan.</p>
                    @endif
                </section>
            </div>
            <aside class="psm-detail-aside">
                <section class="psm-card psm-detail-card">
                    <h2><span class="psm-section-letter">{{ $hasExtra ? 'E' : 'D' }}</span>Keanggotaan</h2>
                    {!! $status !!}
                    <dl class="psm-values psm-values-single">
                        {!! $value('Wilayah akun', $organization['district']) !!}
                        {!! $value('Kelurahan', $item['village']) !!}
                        {!! $value('Jabatan', $item['position']) !!}
                    </dl>
                </section>
                <section class="psm-process-note"><x-dashboard.lks-icon name="info" /><h2>Pembaruan data</h2><p>Pastikan nomor WhatsApp masih aktif agar anggota dapat dihubungi.</p></section>
            </aside>
        </div>
        <div class="psm-detail-actions"><a class="psm-button" href="/admin/members/psm"><x-dashboard.lks-icon name="arrow-left" />Kembali</a></div>
    </section>
@endsection
