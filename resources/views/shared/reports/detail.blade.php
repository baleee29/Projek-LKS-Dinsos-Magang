@extends('layouts.dashboard')

@section('title', 'Detail Laporan Semester LKS')

@php
    use App\Support\Format;
    $icons = [
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/>',
        'clip' => '<path d="m8 12 6-6a3 3 0 0 1 4 4l-8 8a5 5 0 0 1-7-7l8-8"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'send' => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'building' => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h1M14 17h1"/>',
        'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
    ];
    $icon = fn (string $name) => '<svg class="sr-detail-icon" viewBox="0 0 24 24" aria-hidden="true">'.$icons[$name].'</svg>';
    $files = $report['attachments'];
    $prefix = $admin ? '/admin' : '/lks';
    $info = [
        ['Nama lembaga', $organization['title']],
        ['Penanggung jawab', $organization['chair']],
        ['Wilayah', $organization['village'].', '.$organization['district']],
        ['Kontak', $organization['contact']],
        ['Legalitas', $organization['legal']],
    ];
    $sent = $report['status'] !== 'Draf';
    $history = $sent
        ? [['status' => 'Terkirim', 'at' => $report['submittedAt'], 'actor' => $organization['title'], 'version' => $report['version']]]
        : [['status' => 'Draf', 'at' => $report['updated'], 'actor' => $organization['title'], 'version' => $report['version']]];
@endphp

@section('content')
    <section class="sr-detail">
        <nav class="sr-detail-breadcrumb" aria-label="Navigasi halaman"><a href="{{ $prefix }}/overview">Dashboard</a><span aria-hidden="true">/</span><a href="{{ $admin ? '/admin/reports/lks' : '/lks/reports' }}">{{ $admin ? 'Laporan LKS' : 'Laporan Semester' }}</a><span aria-hidden="true">/</span><span aria-current="page">Detail</span></nav>
        <header class="sr-detail-hero">
            <span class="sr-detail-hero-icon">{!! $icon('file') !!}</span>
            <div class="sr-detail-hero-text">
                <p class="sr-detail-eyebrow">{{ $period['label'] }} · {{ $report['id'] }}</p>
                <h1 id="view-title" tabindex="-1">Detail Laporan Semester LKS</h1>
                <p class="sr-detail-subject">{{ $report['title'] ?: 'Belum ada judul laporan' }}</p>
                <p>{{ $organization['title'] }}</p>
            </div>
            <div class="sr-detail-hero-actions">
                <x-dashboard.status-badge :status="$report['status']" />
                @if ($admin)
                    {{-- TODO(Tahap 2): admin/report-download.js. --}}
                    <button type="button" class="export-button" data-demo-download>Unduh PDF</button>
                @else
                    <a class="export-button" href="/lks/edit/{{ $report['id'] }}">{{ $sent ? 'Perbarui laporan' : 'Lanjutkan draf' }}</a>
                    @unless ($sent)
                        <button type="button" class="psm-delete-button" data-demo-action data-confirm="Draf {{ $period['label'] }} milik {{ $organization['title'] }}, termasuk isian dan lampirannya, akan dihapus. Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus draf laporan?" data-confirm-text="Ya, hapus draf" aria-haspopup="dialog">Hapus draf</button>
                    @endunless
                @endif
            </div>
        </header>
        @if (! $admin && $report['hasDraftChanges'])
            <p class="notice">Ada perubahan yang belum dikirim. Admin tetap melihat laporan kiriman terakhir.</p>
        @endif
        <div class="sr-detail-stats">
            @foreach ([
                ['calendar', 'Periode', $period['label'], Format::date($period['start']).' – '.Format::date($period['end'])],
                ['clip', 'Berkas Lampiran', count($files).' berkas', count($files) ? 'Dokumen terunggah' : 'Belum ada lampiran'],
                ['send', 'Dikirim', Format::date($report['submittedAt'], 'Belum diajukan'), $sent ? 'Versi '.$report['version'] : 'Belum dikirim ke Admin'],
                ['clock', 'Pembaruan Terakhir', Format::date($report['updated']), ''],
            ] as [$symbol, $label, $value, $note])
                <article class="sr-detail-stat">{!! $icon($symbol) !!}<div><span>{{ $label }}</span><strong>{{ $value }}</strong>@if ($note)<small>{{ $note }}</small>@endif</div></article>
            @endforeach
        </div>
        <div class="sr-detail-layout">
            <div class="sr-detail-main">
                <section class="sr-detail-card">
                    <h2>{!! $icon('building') !!}Informasi Lembaga</h2>
                    <dl class="sr-detail-grid">
                        @foreach ($info as [$label, $value])
                            <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                        @endforeach
                        <div class="sr-detail-wide"><dt>Alamat sekretariat</dt><dd>{{ $organization['address'] ?: 'Belum dicatat' }}</dd></div>
                    </dl>
                </section>
                <section class="sr-detail-card">
                    <h2>{!! $icon('clip') !!}Berkas Dokumen Laporan <span class="sr-detail-count">{{ count($files) }}</span></h2>
                    @if (count($files))
                        <ul class="sr-detail-files">
                            @foreach ($files as $file)
                                <li><span class="sr-detail-file-type" aria-hidden="true">{{ strtoupper(substr(pathinfo($file['name'], PATHINFO_EXTENSION), 0, 4)) ?: 'DOK' }}</span><div><strong>{{ $file['name'] }}</strong><small>{{ $file['type'] }} · {{ number_format(ceil($file['size'] / 1024), 0, ',', '.') }} KB</small></div><button type="button" class="sr-detail-button" data-demo-download aria-label="Lihat {{ $file['name'] }}">{!! $icon('eye') !!}Lihat</button></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="sr-detail-empty">Belum ada berkas laporan yang diunggah.</p>
                    @endif
                </section>
            </div>
            <aside class="sr-detail-side">
                <section class="sr-detail-card">
                    <h2>{!! $icon('send') !!}Informasi Pengiriman</h2>
                    <dl class="sr-detail-grid sr-detail-grid-single">
                        <div><dt>Status</dt><dd>{{ $report['status'] }}</dd></div>
                        <div><dt>Kiriman terakhir</dt><dd>{{ Format::date($report['submittedAt'], 'Belum diajukan') }}</dd></div>
                        <div><dt>Versi</dt><dd>{{ $report['version'] }}</dd></div>
                    </dl>
                    <p class="sr-detail-note">Laporan yang dikirim langsung masuk ke Admin tanpa proses persetujuan.</p>
                </section>
                <section class="sr-detail-card">
                    <h2>{!! $icon('history') !!}Riwayat Laporan</h2>
                    <ol class="status-timeline">
                        @foreach ($history as $entry)
                            <li><strong>{{ $entry['status'] }}</strong><time datetime="{{ $entry['at'] }}">{{ Format::dateTime($entry['at']) }}</time><span>{{ $entry['actor'] }} · Versi {{ $entry['version'] }}</span></li>
                        @endforeach
                    </ol>
                </section>
            </aside>
        </div>
    </section>
@endsection
