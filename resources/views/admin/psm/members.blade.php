@extends('layouts.dashboard')

@section('title', 'Data Anggota PSM')

@php
    use App\Support\Format;
@endphp

@section('content')
    <section class="admin-partner-view">
        <header class="admin-partner-heading">
            <div><small>SiDaya · Admin Dinas Sosial</small><h1 id="view-title" tabindex="-1">Data Anggota PSM</h1><p>Seluruh anggota terdaftar yang ditambahkan langsung oleh Ketua PSM.</p></div>
            {{-- TODO(Tahap 2): ekspor dari admin/partner-export-psm.js. --}}
            <div class="admin-partner-heading-actions"><button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh PDF</button><button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh Excel</button></div>
        </header>
        <p class="admin-partner-source-note">Daftar memuat data organisasi dan pendaftaran publik yang telah disetujui.</p>
        @include('admin.organizations.controls', ['searchLabel' => 'Cari nama, WhatsApp, atau wilayah', 'placeholder' => 'Cari nama, WhatsApp, wilayah…'])
        <section class="admin-partner-card" aria-label="Data Anggota PSM">
            <div class="admin-partner-list-heading"><strong role="status" aria-live="polite">{{ $members->total() }} anggota</strong><span>Anggota terdaftar</span></div>
            <div>
                @if ($members->count())
                    <div class="admin-partner-table-scroll table-scroll" tabindex="0" role="region" aria-label="Data Anggota PSM">
                        <table class="admin-partner-table reports-table q-table q-table-numbered">
                            <caption class="sr-only">Data Anggota PSM</caption>
                            <thead><tr><th scope="col" class="q-col-number">No.</th><th scope="col" class="q-col-person">Nama Anggota</th><th scope="col">Pekerjaan</th><th scope="col">Kecamatan</th><th scope="col">Kelurahan</th><th scope="col">WhatsApp</th><th scope="col">Status</th><th scope="col" class="q-col-action">Tindakan</th></tr></thead>
                            <tbody>
                                @foreach ($members as $index => $item)
                                    <tr>
                                        <td class="q-col-number" data-label="No.">{{ $members->firstItem() + $index }}</td>
                                        <td class="q-col-person" data-label="Nama Anggota"><div class="q-person-cell"><span class="q-initials" aria-hidden="true">{{ Format::initials($item['title']) }}</span><div><strong>{{ $item['title'] }}</strong><span class="cell-meta">{{ $item['position'] ?: 'Anggota PSM' }}</span></div></div></td>
                                        <td data-label="Pekerjaan">{{ $item['occupation'] ?: '—' }}</td>
                                        <td data-label="Kecamatan"><span class="q-district-badge">{{ $item['district'] }}</span></td>
                                        <td data-label="Kelurahan">{{ $item['village'] }}</td>
                                        <td data-label="WhatsApp">{{ $item['contact'] }}</td>
                                        <td data-label="Status"><span class="admin-partner-badge is-muted">Terdaftar</span></td>
                                        <td class="q-col-action" data-label="Tindakan"><a class="row-action" href="/admin/member/{{ $item['id'] }}" aria-label="Lihat anggota {{ $item['title'] }}">Detail<x-dashboard.icon name="arrow" /></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="admin-partner-empty"><x-dashboard.icon name="building" /><h2>Belum ada data sesuai pilihan</h2><p>Ubah pencarian atau filter untuk melihat anggota lainnya.</p></div>
                @endif
            </div>
            @include('admin.organizations.footer', ['paginator' => $members, 'noun' => 'anggota'])
        </section>
    </section>
@endsection
