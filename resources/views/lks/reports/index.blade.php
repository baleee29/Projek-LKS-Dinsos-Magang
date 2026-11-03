@extends('layouts.dashboard')

@section('title', $pageTitle)

@php
    use App\Support\Data\DataLaporan;
    use App\Support\Format;
@endphp

@section('content')
    <section class="lks-workspace lks-report-view">
        <x-dashboard.lks-heading :title="$pageTitle" :description="$description" group="Pelaporan">
            <button class="lks-button" type="button" data-export-url="/ekspor/laporan{{ $forcedStatus ? '?status='.$forcedStatus : '' }}" data-export-format="pdf"><x-dashboard.lks-icon name="download" />Unduh PDF</button>
            <button class="lks-button" type="button" data-export-url="/ekspor/laporan{{ $forcedStatus ? '?status='.$forcedStatus : '' }}" data-export-format="xlsx"><x-dashboard.lks-icon name="download" />Unduh Excel</button>
            <a class="lks-button" href="/assets/documents/Template_Laporan_LKS.pdf" download="Template_Laporan_LKS.pdf" target="_blank" rel="noopener" title="Unduh format template laporan semester LKS"><x-dashboard.lks-icon name="file" />Template Laporan</a>
            <button class="lks-button lks-button-primary" type="button" data-open-dialog="new-report-dialog"><x-dashboard.lks-icon name="plus" />Buat Laporan Baru</button>
        </x-dashboard.lks-heading>
        <section class="lks-report-metrics" aria-label="Ringkasan laporan">
            @foreach ($metrics as [$label, $count, $note, $symbol, $type])
                <article @class(['lks-card', 'lks-report-metric', 'lks-report-metric-'.$type => $type])><div><h2>{{ $label }}</h2><p><strong>{{ $count }}</strong> <span>Laporan</span></p><small>{{ $note }}</small></div><span class="lks-icon-tile"><x-dashboard.lks-icon :name="$symbol" /></span></article>
            @endforeach
        </section>
        @if ($forcedStatus)
            <p class="lks-info lks-report-scope"><x-dashboard.lks-icon name="edit" /><span>Daftar ini hanya menampilkan laporan berstatus <strong>{{ $forcedStatus }}</strong>. Semua periode tersedia melalui filter tahun dan semester.</span></p>
        @endif
        <form class="lks-card lks-report-controls" aria-label="Pencarian dan filter laporan" method="get" data-filter-form>
            <label class="lks-search"><x-dashboard.lks-icon name="search" /><span class="sr-only">Cari kegiatan atau nomor laporan</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari kegiatan, ID laporan, atau kata kunci…" @if ($filters['q']) data-autofocus @endif></label>
            <x-dashboard.lks-select class="lks-report-select" name="year" label="Tahun laporan" all="Semua Tahun" :value="$filters['year']" :options="$years->map(fn ($year) => [$year, 'Tahun '.$year])->all()" />
            @unless ($forcedStatus)
                <x-dashboard.lks-select class="lks-report-select" name="status" label="Status laporan" all="Semua Status" :value="$filters['status']" :options="$statuses" />
            @endunless
            <x-dashboard.lks-select class="lks-report-select" name="quarter" label="Semester laporan" all="Semua Semester" :value="$filters['quarter']" :options="[[1, 'Semester 1 (Jan – Jun)'], [2, 'Semester 2 (Jul – Des)']]" />
            <a class="lks-button lks-button-plain" href="{{ request()->url() }}"><x-dashboard.lks-icon name="reset" />Reset</a>
        </form>
        <section class="lks-card lks-report-results" aria-label="Daftar laporan">
            <div>
                @if ($reports->count())
                    <div class="lks-table-scroll" tabindex="0" role="region" aria-label="Tabel laporan semester">
                        <table class="lks-table lks-report-table">
                            <caption class="sr-only">{{ $pageTitle }} — {{ $organization['title'] }}</caption>
                            <thead><tr><th scope="col" class="lks-col-number">No.</th><th scope="col">Periode &amp; ID Laporan</th><th scope="col">Judul / Perihal Laporan</th><th scope="col">Berkas Terlampir</th><th scope="col">Status Pengiriman</th><th scope="col">Tindakan</th></tr></thead>
                            <tbody>
                                @foreach ($reports as $index => $item)
                                    @php($filesCount = count($item['attachments']))
                                    @php($isDraft = $item['status'] === 'Draf')
                                    <tr>
                                        <td class="lks-col-number" data-label="No.">{{ $reports->firstItem() + $index }}</td>
                                        <td>
                                            <div class="lks-report-period">
                                                <span class="lks-icon-tile lks-report-icon-{{ $isDraft ? 'draft' : 'approved' }}"><x-dashboard.lks-icon :name="$isDraft ? 'edit' : 'file'" /></span>
                                                <div>
                                                    <a class="lks-report-name" href="/lks/report/{{ $item['id'] }}">{{ DataLaporan::periodLabel($item) }}</a>
                                                    <small class="lks-cell-meta lks-report-id">{{ $item['id'] }}</small>
                                                    <small class="lks-cell-meta">{{ $isDraft ? 'Disimpan '.Format::date($item['updated']) : 'Dikirim '.Format::date($item['submittedAt'], 'belum tercatat') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><strong>{{ $item['title'] ?: 'Belum ada judul' }}</strong><p class="lks-cell-meta">Semester {{ $item['quarter'] <= 2 ? 1 : 2 }} Tahun {{ $item['year'] }}</p></td>
                                        <td><strong>{{ $filesCount }} berkas</strong><small class="lks-cell-meta">{{ $filesCount ? 'Dokumen terunggah' : 'Belum ada lampiran' }}</small></td>
                                        <td><x-dashboard.status-badge :status="$item['status']" variant="lks" />@if ($item['hasDraftChanges'])<small class="lks-cell-meta">Ada perubahan belum dikirim</small>@endif</td>
                                        <td>
                                            <div class="lks-report-table-actions">
                                                <a class="lks-button" href="/lks/report/{{ $item['id'] }}"><x-dashboard.lks-icon name="eye" />Detail</a>
                                                <a class="lks-button lks-button-primary" href="/lks/edit/{{ $item['id'] }}"><x-dashboard.lks-icon name="edit" />{{ $isDraft ? 'Lanjutkan Isi' : 'Perbarui Laporan' }}</a>
                                                @if ($isDraft)
                                                    <button class="lks-button lks-button-danger lks-report-delete" type="button" data-action-url="/lks/reports/{{ $item['id'] }}" data-action-method="DELETE" data-confirm="Draf {{ DataLaporan::periodLabel($item) }} milik {{ $organization['title'] }}, termasuk isian dan lampirannya, akan dihapus. Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus draf laporan?" data-confirm-text="Ya, hapus draf" aria-haspopup="dialog" aria-label="Hapus draf {{ DataLaporan::periodLabel($item) }}"><x-dashboard.lks-icon name="trash" />Hapus draf</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state variant="lks" title="Belum ada laporan sesuai pilihan" description="Ubah filter untuk melihat periode lain." />
                @endif
            </div>
            <x-dashboard.pagination :paginator="$reports" variant="lks-report" noun="laporan" label="Halaman daftar laporan" />
        </section>
        <section class="lks-card lks-report-calendar" aria-label="Periode pelaporan">
            <header class="lks-card-heading"><span class="lks-icon-tile"><x-dashboard.lks-icon name="calendar" /></span><div><h2>Periode Pelaporan Semester (6 Bulan)</h2><p>Draf dapat disusun selama periode berjalan. Pengiriman tersedia setelah periode berakhir.</p></div><span class="lks-report-year">Tahun {{ $calendarYear }}</span></header>
            <div class="lks-report-periods">
                @foreach ($semesters as $semester)
                    @php($period = $semester['period'])
                    @php($future = $period['start'] > $today)
                    <article @class(['lks-report-quarter', 'is-current' => $period['interim'] && ! $future])>
                        <span class="lks-quarter-number">S{{ $semester['number'] }}</span>
                        <div>
                            <h3>{{ $period['label'] }}</h3>
                            <p>{{ Format::date($period['start']) }} – {{ Format::date($period['end']) }}</p>
                            @if ($semester['existing'])
                                <a href="/lks/edit/{{ $semester['existing']['id'] }}">{{ $semester['existing']['status'] }} <x-dashboard.lks-icon name="arrow" /></a>
                            @else
                                <span class="lks-quarter-state">{{ $future ? 'Periode belum dimulai' : ($period['interim'] ? 'Periode berjalan · dapat menyusun draf' : 'Periode selesai · belum ada laporan') }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="lks-report-calendar-note">Rentang ini menunjukkan periode laporan per semester. Batas waktu resmi mengikuti arahan Dinas Sosial.</p>
        </section>
    </section>
    @include('lks.reports.new-report-dialog', ['organization' => $organization, 'years' => $dialogYears])
@endsection
