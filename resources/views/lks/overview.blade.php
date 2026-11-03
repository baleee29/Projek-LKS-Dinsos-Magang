@extends('layouts.dashboard')

@section('title', 'Ringkasan LKS')

@php
    use App\Support\Format;
@endphp

@section('content')
    <section class="lks-workspace lks-overview">
        <x-dashboard.lks-heading title="Ringkasan LKS" :description="'Pengelolaan SDM, layanan klien, dan laporan '.$organization['title'].'.'" group="Ringkasan" />
        <div class="lks-overview-metrics" aria-label="Ringkasan data lembaga">
            @foreach ([
                ['members', 'Pengurus & SDM', $members->count(), 'Personel dalam daftar SDM', '/lks/members'],
                ['clients', 'Klien terdaftar', $clients->count(), $summary['residents'].' masih tinggal di panti', '/lks/occupancy'],
                ['staff', 'Pengurus', $memberCategories['Pengurus'], 'Pengurus yang tercatat di lembaga', '/lks/members'],
                ['reports', 'Laporan terkirim', $reportCounts['Terkirim'], 'Sudah masuk ke Admin · seluruh periode', '/lks/reports'],
            ] as [$key, $title, $value, $note, $route])
                <article class="lks-card lks-overview-metric" data-lks-overview-metric="{{ $key }}">
                    <h2>{{ $title }}</h2>
                    <strong>{{ $value }}</strong>
                    <p>{{ $note }}</p>
                    <a href="{{ $route }}">Lihat daftar <x-dashboard.lks-icon name="arrow" /></a>
                </article>
            @endforeach
        </div>
        <div class="lks-overview-primary-grid">
            <section class="lks-card lks-overview-section" aria-labelledby="lks-current-report-heading" data-lks-overview-current-report>
                <header><span class="lks-icon-tile"><x-dashboard.lks-icon name="calendar" /></span><div><h2 id="lks-current-report-heading">Laporan periode berjalan</h2><p>{{ $period['label'] }} · {{ Format::date($period['start']) }} – {{ Format::date($period['end']) }}</p></div></header>
                <div class="lks-overview-current-content">
                    @if ($currentReport)
                        <x-dashboard.status-badge :status="$currentReport['status']" variant="lks" />
                    @else
                        <span class="lks-status lks-status-draft">Belum ada laporan</span>
                    @endif
                    <p>{{ $period['interim'] ? 'Susun draf selama periode berjalan. Pengiriman tersedia setelah '.Format::date($period['end']).'.' : 'Periode telah berakhir. Lengkapi laporan dan periksa sebelum mengirim.' }}</p>
                    <div class="lks-actions">
                        @if ($currentReport)
                            <a class="lks-button" href="/lks/report/{{ $currentReport['id'] }}">Detail laporan</a>
                            <a class="lks-button" href="/lks/edit/{{ $currentReport['id'] }}">{{ $currentReport['status'] === 'Draf' ? 'Lanjutkan draf' : 'Perbarui laporan' }}</a>
                        @else
                            <button class="lks-button lks-button-primary" type="button" data-open-dialog="new-report-dialog">Buat / buka laporan</button>
                        @endif
                    </div>
                </div>
            </section>
            <section class="lks-card lks-overview-section" aria-labelledby="lks-follow-up-heading">
                <header><span class="lks-icon-tile"><x-dashboard.lks-icon name="edit" /></span><div><h2 id="lks-follow-up-heading">Tindak lanjut</h2><p>Draf, perbaikan, dan penyelarasan yang perlu diselesaikan.</p></div></header>
                @if ($reportCounts['Draf'] > 0)
                    <ul class="lks-overview-tasks">
                        <li><span class="lks-overview-task-count">{{ $reportCounts['Draf'] }}</span><div><a href="/lks/drafts">Lanjutkan draf laporan <x-dashboard.lks-icon name="arrow" /></a><p>Seluruh periode laporan lembaga.</p></div></li>
                    </ul>
                @else
                    <p class="lks-overview-empty">Tidak ada draf, perbaikan, atau penyelarasan tertunda.</p>
                @endif
            </section>
        </div>
        <div class="lks-overview-feature-grid">
            <section class="lks-card lks-overview-section" aria-labelledby="lks-sdm-overview-heading" data-lks-overview-members>
                <header><div><h2 id="lks-sdm-overview-heading">Pengurus & SDM</h2><p>Personel yang tercatat di lembaga.</p></div><a href="/lks/members">Semua personel <x-dashboard.lks-icon name="arrow" /></a></header>
                <dl class="lks-overview-statuses">
                    @foreach ($memberCategories as $category => $count)
                        <div><dt>{{ $category }}</dt><dd>{{ $count }}</dd></div>
                    @endforeach
                </dl>
            </section>
            <section class="lks-card lks-overview-section" aria-labelledby="lks-clients-overview-heading" data-lks-overview-clients>
                <header><div><h2 id="lks-clients-overview-heading">Biodata klien</h2><p>Biodata tersimpan terbaru dari penerima layanan lembaga.</p></div><a href="/lks/occupancy">Rekap klien <x-dashboard.lks-icon name="arrow" /></a></header>
                <dl class="lks-overview-statuses">
                    @foreach (['Total klien' => $clients->count(), 'Tinggal di panti' => $summary['residents'], 'Nonresidensial' => $summary['nonresidential'], 'Sudah keluar' => $summary['exited']] as $label => $count)
                        <div><dt>{{ $label }}</dt><dd>{{ $count }}</dd></div>
                    @endforeach
                </dl>
            </section>
            <section class="lks-card lks-overview-section" aria-labelledby="lks-reports-overview-heading" data-lks-overview-reports>
                <header><div><h2 id="lks-reports-overview-heading">Laporan semester</h2><p>Seluruh periode laporan kegiatan, layanan, dan keuangan lembaga.</p></div><a href="/lks/reports">Semua laporan <x-dashboard.lks-icon name="arrow" /></a></header>
                <dl class="lks-overview-statuses">
                    @foreach ($reportCounts as $status => $count)
                        <div><dt>{{ $status }}</dt><dd>{{ $count }}</dd></div>
                    @endforeach
                </dl>
            </section>
            <section class="lks-card lks-overview-section" aria-labelledby="lks-location-overview-heading">
                <header><div><h2 id="lks-location-overview-heading">Titik Lokasi Lembaga</h2><p>Peta posisi sekretariat LKS yang dipantau oleh Admin.</p></div><a href="/lks/profile">Buka profil &amp; peta <x-dashboard.lks-icon name="arrow" /></a></header>
                <div style="padding:4px 0;">
                    @if ($map['has'])
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;font-size:12px;flex-wrap:wrap;gap:6px;">
                            <span>📍 <code>{{ $map['text'] }}</code></span>
                            <a href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" style="color:var(--lks-primary,#0284c7);text-decoration:none;font-weight:600;">Buka Google Maps ↗</a>
                        </div>
                        <div style="height:140px;border-radius:8px;overflow:hidden;border:1px solid var(--border-color,#cbd5e1);background:#f8fafc;">
                            <iframe src="{{ $map['embedUrl'] }}" style="width:100%;height:100%;border:none;display:block;" loading="lazy" title="Peta Lokasi LKS"></iframe>
                        </div>
                    @else
                        <p class="lks-overview-empty" style="margin:8px 0 12px 0;">Titik koordinat belum diatur.</p>
                        <a href="/lks/profile" class="lks-button lks-button-secondary" style="font-size:12px;display:inline-block;">Atur Titik Lokasi</a>
                    @endif
                </div>
            </section>
        </div>
        <p class="lks-overview-storage-note">Data SDM, klien, dan laporan semester terintegrasi pada sistem SiDaya Kota Banjarbaru.</p>
    </section>
    @include('lks.reports.new-report-dialog', ['organization' => $organization, 'years' => $years])
@endsection
