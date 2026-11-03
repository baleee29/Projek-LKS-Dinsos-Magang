@extends('layouts.dashboard')

@section('title', $pageTitle)

@php
    use App\Support\Format;
    $downloadIcon = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>';
@endphp

@section('content')
    <section class="admin-correspondence-view">
        @include('admin.correspondence.heading', ['title' => $pageTitle, 'description' => $description, 'action' => $letters ? '<a class="admin-correspondence-button is-primary" href="/admin/new-reply-letter">Buat surat balasan</a>' : null])
        <form class="admin-correspondence-controls" aria-label="Pencarian dan filter" method="get" data-filter-form>
            <label><span>Cari</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Judul, nomor, organisasi, atau kegiatan" @if ($filters['q']) data-autofocus @endif></label>
            <label><span>Bulan</span><select name="month"><option value="">Semua bulan</option>@foreach ($months as $index => $month)<option value="{{ $index + 1 }}" @selected($filters['month'] === (string) ($index + 1))>{{ $month }}</option>@endforeach</select></label>
            <label><span>Tahun</span><select name="year"><option value="">Semua tahun</option>@foreach ($years as $year)<option value="{{ $year }}" @selected($filters['year'] === (string) $year)>{{ $year }}</option>@endforeach</select></label>
            <label><span>Status</span><select name="status"><option value="">Semua status</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>@endforeach</select></label>
            <label><span>Organisasi</span><select name="org"><option value="">Semua organisasi</option>@foreach ($organizations as $org)<option value="{{ $org['id'] }}" @selected($filters['org'] === $org['id'])>{{ $org['title'] }}</option>@endforeach</select></label>
            <button class="admin-correspondence-button" type="button" data-href="{{ request()->url() }}">Hapus filter</button>
        </form>
        <section class="admin-correspondence-card">
            <div class="admin-correspondence-list-header">
                <p role="status" aria-live="polite">{{ $rows->total() }} {{ $letters ? 'surat' : 'pengajuan' }}</p>
                {{-- TODO(Tahap 2): ekspor dari admin/correspondence-export.js. --}}
                <div class="admin-correspondence-export-actions">
                    <button type="button" class="export-button" data-demo-download>{!! $downloadIcon !!}<span>Unduh PDF</span></button>
                    <button type="button" class="export-button" data-demo-download>{!! $downloadIcon !!}<span>Unduh Excel</span></button>
                </div>
            </div>
            <div>
                @if ($rows->count())
                    <div class="admin-correspondence-table-scroll" role="region" aria-label="{{ $pageTitle }}" tabindex="0">
                        <table class="admin-correspondence-table q-table-numbered">
                            <caption class="sr-only">{{ $pageTitle }}</caption>
                            <thead><tr><th scope="col" class="q-col-number">No.</th><th scope="col">{{ $letters ? 'Surat' : 'Kegiatan' }}</th><th scope="col">Organisasi</th><th scope="col">Tanggal</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($rows as $index => $row)
                                    <tr>
                                        <td class="q-col-number" data-label="No.">{{ $rows->firstItem() + $index }}</td>
                                        <td data-label="{{ $letters ? 'Surat' : 'Kegiatan' }}">
                                            <strong>{{ $letters ? $row['subject'] : $row['title'] }}</strong>
                                            <small>{{ $letters ? 'No. '.($row['number'] ?: 'Belum dicatat') : ($kinds[$row['type']] ?? $row['type']) }}</small>
                                            @if ($letters && ($activity = $activityOf($row['activityId'])))
                                                <small>{{ $activity['title'] }}</small>
                                            @endif
                                        </td>
                                        <td data-label="Organisasi">{{ $organizationOf($row['organizationId'])['title'] }}</td>
                                        <td data-label="Tanggal">{{ Format::date($row[$dateKey]) }}</td>
                                        <td data-label="Status">@include('admin.correspondence.status', ['status' => $row['status']])</td>
                                        <td data-label="Aksi">
                                            <div style="display:inline-flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                                <a class="admin-correspondence-button" href="/admin/{{ $letters ? 'reply-letter' : 'incoming-letter' }}/{{ $row['id'] }}">Detail</a>
                                                @if ($letters)
                                                    <button type="button" class="admin-correspondence-button is-danger" data-demo-action data-confirm="Surat &quot;{{ $row['subject'] }}&quot; beserta berkasnya akan dihapus." data-confirm-title="Hapus surat balasan?" data-confirm-text="Ya, hapus surat">Hapus</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="admin-correspondence-empty">
                        <h2>{{ $hasRows ? 'Tidak ada hasil yang sesuai' : ($letters ? 'Belum ada surat balasan' : 'Belum ada pengajuan terkirim') }}</h2>
                        <p>{{ $hasRows ? 'Ubah pencarian atau filter untuk melihat data lainnya.' : ($letters ? 'Buat draf surat, lengkapi metadata dan berkas, lalu terbitkan kepada organisasi penerima.' : 'Pengajuan muncul setelah Karang Taruna mengirim kegiatan melalui akun penerima.') }}</p>
                    </div>
                @endif
            </div>
            <nav class="admin-correspondence-pages" aria-label="Halaman daftar">
                <button class="admin-correspondence-button" type="button" @if ($rows->currentPage() === 1) disabled @else data-href="{{ $rows->previousPageUrl() }}" @endif>Sebelumnya</button>
                <span>Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
                <button class="admin-correspondence-button" type="button" @if ($rows->currentPage() === $rows->lastPage()) disabled @else data-href="{{ $rows->nextPageUrl() }}" @endif>Berikutnya</button>
            </nav>
        </section>
    </section>
@endsection
