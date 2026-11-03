@extends('layouts.dashboard')

@section('title', 'Laporan Semester LKS')

@php
    use App\Support\Format;
@endphp

@section('content')
    <x-dashboard.section-heading title="Laporan Semester LKS" description="Laporan semester yang dikirim oleh LKS beserta berkasnya, tanpa proses persetujuan." />
    <form method="get" data-filter-form>
        <div class="q-period-bar" aria-label="Periode pelaporan">
            <label class="filter-field">Tahun
                <select name="year">
                    <option value="all" @selected($filters['year'] === 'all')>Semua tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected($filters['year'] === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </label>
            <label class="filter-field">Semester
                <select name="quarter">
                    <option value="all" @selected($filters['quarter'] === 'all')>Semua semester</option>
                    <option value="1" @selected($filters['quarter'] === '1')>Semester 1 (Januari – Juni)</option>
                    <option value="2" @selected($filters['quarter'] === '2')>Semester 2 (Juli – Desember)</option>
                </select>
            </label>
            <p class="q-inline-note">{{ $period ? Format::date($period['start']).' – '.Format::date($period['end']) : 'Semua periode yang sesuai filter' }}@if ($period && $period['interim']) · Rekap sementara @endif</p>
        </div>
        <section class="report-controls" aria-label="Pencarian dan filter">
            <div class="search-row">
                <label class="list-search"><x-dashboard.icon name="search" /><span class="sr-only">Cari mitra atau nomor laporan</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama mitra atau nomor laporan…" @if ($filters['q']) data-autofocus @endif></label>
                <button class="text-button" type="button" data-href="{{ request()->url() }}">Reset filter</button>
            </div>
            <div class="workspace-filters">
                <label class="filter-field">Kecamatan
                    <select name="district">
                        <option value="">Semua kecamatan</option>
                        @foreach ($districts as $district)
                            <option @selected($filters['district'] === $district)>{{ $district }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="filter-field">Kelurahan
                    <select name="village">
                        <option value="">Semua kelurahan</option>
                        @foreach ($villages as $village)
                            <option @selected($filters['village'] === $village)>{{ $village }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="filter-field">Status laporan
                    <select name="status">
                        <option value="">Semua status</option>
                        <option @selected($filters['status'] === 'Terkirim')>Terkirim</option>
                    </select>
                </label>
            </div>
        </section>
    </form>
    <section class="reports-panel">
        <div class="panel-heading">
            <div><h2>Daftar laporan</h2><p class="panel-description" id="report-count" role="status" aria-live="polite">{{ $reports->total() }} laporan</p></div>
            <div class="panel-actions" style="display:inline-flex;align-items:center;gap:8px;">
                {{-- TODO(Tahap 2): ekspor dari shared/report-recap-export.js. --}}
                <button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh PDF</button>
                <button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh Excel</button>
                <a class="export-button" href="/assets/documents/Template_Laporan_LKS.pdf" download="Template_Laporan_LKS.pdf" target="_blank" rel="noopener" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;"><x-dashboard.icon name="file" /> Template Laporan</a>
            </div>
        </div>
        <div id="quarterly-results">
            @include('shared.reports.table', ['reports' => $reports, 'offset' => $reports->firstItem() ? $reports->firstItem() - 1 : 0])
            @if ($reports->lastPage() > 1)
                <div class="pagination">
                    <p>{{ $reports->total() }} laporan ditemukan</p>
                    <div>
                        <button type="button" @if ($reports->currentPage() <= 1) disabled @else data-href="{{ $reports->previousPageUrl() }}" @endif>Sebelumnya</button>
                        <span>{{ $reports->currentPage() }} / {{ $reports->lastPage() }}</span>
                        <button type="button" @if ($reports->currentPage() >= $reports->lastPage()) disabled @else data-href="{{ $reports->nextPageUrl() }}" @endif>Berikutnya</button>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
