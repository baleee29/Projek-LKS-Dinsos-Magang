@extends('layouts.dashboard')

@section('title', 'Pengaduan Anggota PSM')

@php
    use App\Support\Format;
    $extra = [
        'refresh' => '<path d="M20 7v5h-5M4 17v-5h5"/><path d="M6 7a7 7 0 0 1 12-2l2 2M4 17l2 2a7 7 0 0 0 12-2"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 11h18M7 15h2m4 0h2M7 18h2"/>',
        'pin' => '<path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'phone' => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M10 18h4"/>',
        'attachment' => '<path d="m9 13 6-6a3 3 0 0 1 4 4l-8 8a5 5 0 0 1-7-7l9-9"/>',
        'left' => '<path d="m15 6-6 6 6 6"/>',
        'right' => '<path d="m9 6 6 6-6 6"/>',
    ];
    $icon = fn (string $name) => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">'.($extra[$name] ?? '<use href="#icon-'.$name.'"/>').'</svg>';
    $hasFilters = $filters['q'] !== '' || $filters['district'] !== '' || $filters['village'] !== '';
    $current = $complaints->currentPage();
    $last = $complaints->lastPage();
    $pageButtons = collect([1, $current - 1, $current, $current + 1, $last])->filter(fn ($page) => $page >= 1 && $page <= $last)->unique()->sort()->values();
@endphp

@section('content')
    <div class="admin-psm-complaints" id="admin-complaints-list">
        <header class="admin-complaints-heading">
            <div>
                <div class="admin-complaints-heading-meta"><a href="/admin/overview">Dashboard</a><span aria-hidden="true">/</span><span>Pengaduan Anggota PSM</span><span class="admin-complaints-pillar">Pilar PSM · Kota Banjarbaru</span></div>
                <h1 id="view-title" tabindex="-1">Pengaduan Anggota PSM</h1>
                <p>Pantau dan rekap seluruh laporan atau pengaduan yang disampaikan oleh anggota Pekerja Sosial Masyarakat (PSM) di wilayah Kota Banjarbaru.</p>
            </div>
            <div class="admin-complaints-heading-actions">
                {{-- TODO(Tahap 2): GET /admin/psm-complaints/export. --}}
                <button class="export-button" id="btn-admin-export-excel" type="button" data-demo-download>{!! $icon('download') !!}Unduh Excel</button>
                <button class="text-button" id="btn-admin-refresh-complaints" type="button" data-href="{{ request()->fullUrl() }}">{!! $icon('refresh') !!}Muat ulang</button>
            </div>
        </header>
        <div id="admin-complaints-main" style="min-width:0;">
            <section class="admin-complaints-stats" aria-label="Statistik pengaduan">
                @foreach ($metrics as [$title, $note, $symbol, $tone, $value])
                    <article class="admin-complaints-stat"><div><h2>{{ $title }}</h2><strong>{{ $value }}</strong><p>{{ $note }}</p></div><span class="admin-complaints-stat-icon is-{{ $tone }}">{!! $icon($symbol) !!}</span></article>
                @endforeach
            </section>
            <section class="admin-complaints-panel" aria-labelledby="admin-complaints-table-title">
                <header class="admin-complaints-panel-heading"><div><h2 id="admin-complaints-table-title">Daftar Pengaduan Anggota PSM</h2><p>Gunakan filter wilayah atau pencarian untuk mempersempit daftar pengaduan anggota lapangan.</p></div><span id="admin-complaints-total" role="status" aria-live="polite">Total {{ $complaints->total() }} entri data</span></header>
                <form class="admin-complaints-filters" method="get" data-filter-form>
                    <label class="admin-complaints-search">{!! $icon('search') !!}<span class="sr-only">Cari pengaduan</span><input type="search" id="admin-search-complaints" name="q" placeholder="Cari no. pengaduan, nama, klien, atau pokok masalah…" value="{{ $filters['q'] }}" @if ($filters['q']) data-autofocus @endif></label>
                    <label><span class="sr-only">Kecamatan</span>
                        <select id="admin-filter-district" name="district">
                            <option value="">Semua Kecamatan</option>
                            @foreach ($districts as $district)
                                <option @selected($filters['district'] === $district)>{{ $district }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label><span class="sr-only">Kelurahan</span>
                        <select id="admin-filter-village" name="village" @disabled(! $filters['district'])>
                            <option value="">Semua Kelurahan</option>
                            @foreach ($villages as $village)
                                <option @selected($filters['village'] === $village)>{{ $village }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="text-button" id="btn-admin-reset-filters" type="button" @if ($hasFilters) data-href="{{ request()->url() }}" @else disabled @endif>{!! $icon('close') !!}Reset filter</button>
                </form>
                <div id="admin-complaints-table-container">
                    <div class="admin-complaints-table-scroll" tabindex="0" role="region" aria-label="Daftar pengaduan; geser untuk melihat seluruh kolom">
                        <table class="admin-complaints-table">
                            <caption class="sr-only">Daftar Pengaduan Anggota PSM</caption>
                            <colgroup><col class="col-no"><col class="col-reference"><col class="col-date"><col class="col-member"><col class="col-client"><col class="col-location"><col class="col-subject"><col class="col-action"></colgroup>
                            <thead><tr>@foreach (['No', 'No. Pengaduan', 'Tanggal', 'Anggota Pengadu', 'Data Klien / PPKS', 'Titik Koordinat', 'Pokok Masalah', 'Aksi'] as $index => $title)<th scope="col" @if ($index === 7) class="admin-complaints-action" @endif>{{ $title }}</th>@endforeach</tr></thead>
                            <tbody>
                                @forelse ($complaints as $index => $row)
                                    @php($hasPoint = $row['latitude'] !== null)
                                    <tr>
                                        <td class="admin-complaints-number">{{ $complaints->firstItem() + $index }}</td>
                                        <td><code class="admin-complaints-reference">{{ $row['complaint_number'] }}</code></td>
                                        <td>{{ Format::date($row['created_at']) }}</td>
                                        <td><strong>{{ $row['member_name'] }}</strong><span class="admin-complaints-meta">{!! $icon('pin') !!}Kec. {{ $row['district'] }}, Kel. {{ $row['village'] }}</span><span class="admin-complaints-meta">{!! $icon('phone') !!}{{ $row['member_phone'] }}</span></td>
                                        <td>
                                            @if ($row['client_name'])
                                                <strong>{{ $row['client_name'] }}</strong><span class="admin-complaints-meta">NIK: {{ $row['client_nik'] }}</span>
                                                @if ($row['client_ktp'])
                                                    <button class="admin-complaints-file-link" type="button" data-demo-download>{!! $icon('file') !!}Lihat KTP</button>
                                                @else
                                                    <span class="admin-complaints-muted">Tanpa lampiran KTP</span>
                                                @endif
                                            @else
                                                <span class="admin-complaints-muted">— (Tidak diisi)</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($hasPoint)
                                                <a class="admin-complaints-location" href="https://www.google.com/maps?q={{ $row['latitude'] }},{{ $row['longitude'] }}" target="_blank" rel="noopener noreferrer" aria-label="Buka lokasi pengaduan {{ $row['complaint_number'] }} di Google Maps">{!! $icon('pin') !!}{{ number_format($row['latitude'], 4) }}, {{ number_format($row['longitude'], 4) }}</a>
                                            @else
                                                <span class="admin-complaints-muted">—</span>
                                            @endif
                                        </td>
                                        <td><span class="admin-complaints-subject">{{ $row['subject'] }}</span>@if (count($row['attachments_list']))<span class="admin-complaints-file-count">{!! $icon('attachment') !!}{{ count($row['attachments_list']) }} lampiran</span>@endif</td>
                                        <td class="admin-complaints-action"><a class="text-button admin-complaints-detail btn-admin-view-detail" href="{{ request()->fullUrlWithQuery(['complaint' => $row['id']]) }}" aria-label="Lihat detail pengaduan {{ $row['complaint_number'] }} dari {{ $row['member_name'] }}">{!! $icon('eye') !!}Lihat detail</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8"><div class="admin-complaints-empty">{!! $icon('file') !!}<h3>{{ $hasFilters ? 'Tidak ada pengaduan yang sesuai' : 'Belum ada pengaduan' }}</h3><p>{{ $hasFilters ? 'Coba kata kunci atau pilihan wilayah lain.' : 'Pengaduan anggota PSM akan tampil setelah dikirim.' }}</p></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <footer class="admin-complaints-table-footer">
                        <p role="status">{{ $complaints->total() ? 'Menampilkan '.$complaints->firstItem().'–'.$complaints->lastItem().' dari '.$complaints->total().' pengaduan' : 'Menampilkan 0 pengaduan' }}</p>
                        <nav class="admin-complaints-pagination" aria-label="Halaman pengaduan">
                            <button class="text-button" type="button" aria-label="Halaman sebelumnya" @if ($current === 1) disabled @else data-href="{{ $complaints->url($current - 1) }}" @endif>{!! $icon('left') !!}</button>
                            @foreach ($pageButtons as $index => $page)
                                @if ($index && $page - $pageButtons[$index - 1] > 1)<span aria-hidden="true">…</span>@endif
                                <button @class(['text-button', 'is-current' => $page === $current]) type="button" data-href="{{ $complaints->url($page) }}" aria-label="Halaman {{ $page }}" @if ($page === $current) aria-current="page" @endif>{{ $page }}</button>
                            @endforeach
                            <button class="text-button" type="button" aria-label="Halaman berikutnya" @if ($current === $last) disabled @else data-href="{{ $complaints->url($current + 1) }}" @endif>{!! $icon('right') !!}</button>
                        </nav>
                    </footer>
                </div>
            </section>
        </div>
    </div>
@endsection
