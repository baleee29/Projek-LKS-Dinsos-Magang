@extends('layouts.dashboard')

@section('title', $organization['title'])

@php
    use App\Support\Format;
    $org = $organization;
    $location = 'Kel. '.$org['village'].', Kec. '.$org['district'];
    $chair = $members->firstWhere('isChair', true);
@endphp

@section('content')
    <section class="kt-profile-view">
        <header class="kt-profile-heading">
            <div>
                <nav class="kt-profile-breadcrumb" aria-label="Navigasi halaman"><a href="/admin/organizations/karang-taruna">Data Kelompok</a><span aria-hidden="true">/</span><span aria-current="page">Profil Organisasi</span></nav>
                <h1 id="view-title" tabindex="-1">{{ $org['title'] }}</h1>
                <p>{{ $location }}</p>
            </div>
        </header>
        <div class="kt-profile-layout">
            <div class="kt-profile-main">
                <section class="kt-profile-card" aria-labelledby="kt-profile-identity-title">
                    <header class="kt-profile-card-header"><span class="kt-profile-icon"><x-dashboard.lks-icon name="building" /></span><div><h2 id="kt-profile-identity-title">Identitas Organisasi</h2><p>Profil dan keterangan lembaga pada akun SiDaya.</p></div><span class="kt-profile-id">{{ $org['id'] }}</span></header>
                    <dl class="kt-profile-fields">
                        <div data-kt-profile-field="title"><dt>Nama organisasi</dt><dd>{{ $org['title'] }}</dd></div>
                        <div data-kt-profile-field="chair"><dt>Penanggung jawab</dt><dd>{{ $org['chair'] }}@if ($chair)<span class="kt-profile-chair-role">{{ $chair['position'] }}</span>@endif</dd></div>
                        <div data-kt-profile-field="location"><dt>Wilayah kerja</dt><dd>{{ $location }}</dd></div>
                        <div data-kt-profile-field="registeredFrom"><dt>Terdaftar sejak</dt><dd>{{ Format::date($org['registeredFrom']) }}</dd></div>
                        <div class="is-wide" data-kt-profile-field="term"><dt>Masa bakti / kepengurusan</dt><dd>{{ $org['term'] ?: 'Belum dicatat' }}</dd></div>
                        <div class="is-wide" data-kt-profile-field="address"><dt>Alamat sekretariat</dt><dd>{{ $org['address'] ?: 'Belum dicatat' }}</dd></div>
                        <div class="is-wide" data-kt-profile-field="contact"><dt>Kontak resmi organisasi</dt><dd class="kt-profile-contact"><span>{{ $org['contact'] ?: 'Belum dicatat' }}</span></dd></div>
                        <div class="is-wide" data-kt-profile-field="coordinates"><dt>Titik Koordinat Sekretariat</dt><dd class="kt-profile-contact"><span @class(['is-empty' => ! $map['has']])>{{ $map['text'] }}</span>@if ($map['has'])<a class="kt-profile-inline-link" href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" style="margin-left:8px;font-size:0.875rem;font-weight:600;color:var(--color-primary,#1d4ed8);text-decoration:underline;">📍 Buka di Google Maps ↗</a>@endif</dd></div>
                    </dl>
                    @if ($map['has'])
                        <div class="kt-profile-map" style="margin-top:1rem;border:1px solid var(--color-border,#e2e8f0);border-radius:8px;overflow:hidden;background:var(--color-bg-muted,#f8fafc);">
                            <div style="padding:8px 12px;font-size:0.825rem;font-weight:600;background:var(--color-bg-subtle,#f1f5f9);display:flex;justify-content:space-between;align-items:center;"><span>📍 Peta Lokasi Sekretariat ({{ $map['text'] }})</span><a href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" style="font-size:0.75rem;color:var(--color-primary,#1d4ed8);text-decoration:underline;">Buka Google Maps Fullscreen ↗</a></div>
                            <iframe width="100%" height="220" src="{{ $map['embedUrl'] }}" style="border:0;display:block;" title="Peta Lokasi Sekretariat" loading="lazy"></iframe>
                        </div>
                    @endif
                    <div class="kt-profile-legal"><h3>Keterangan SK / legalitas</h3><p>{{ $org['legal'] ?: 'Belum dicatat' }}</p></div>
                    <p class="kt-profile-updated">Terakhir diperbarui: {{ Format::date($org['registeredFrom']) }}</p>
                </section>
                <section class="kt-profile-card" aria-labelledby="kt-profile-members-title">
                    <header class="kt-profile-card-header"><span class="kt-profile-icon"><x-dashboard.lks-icon name="users" /></span><div><h2 id="kt-profile-members-title">Daftar Anggota Operasional</h2><p>Pengurus dan anggota yang terdaftar pada organisasi ini.</p></div></header>
                    <form class="kt-profile-controls" method="get" data-filter-form>
                        <input type="hidden" name="category" value="{{ $filters['category'] }}">
                        <label class="kt-profile-search"><x-dashboard.lks-icon name="search" /><span class="sr-only">Cari nama, jabatan, atau kategori anggota</span><input type="search" name="q" placeholder="Cari nama atau peran…" value="{{ $filters['q'] }}" @if ($filters['q']) data-autofocus @endif></label>
                        <div class="kt-profile-filters" role="group" aria-label="Kategori anggota">
                            <button type="button" data-href="{{ request()->fullUrlWithQuery(['category' => null]) }}" aria-pressed="{{ $filters['category'] === '' ? 'true' : 'false' }}">Semua</button>
                            @foreach ($categories as $category)
                                <button type="button" data-href="{{ request()->fullUrlWithQuery(['category' => $category]) }}" aria-pressed="{{ $filters['category'] === $category ? 'true' : 'false' }}">{{ $category }}</button>
                            @endforeach
                        </div>
                    </form>
                    <p class="kt-profile-results-count" role="status" aria-live="polite">{{ $shown->count() }} dari {{ $members->count() }} anggota ditampilkan</p>
                    <div class="kt-profile-member-results">
                        @if ($shown->isNotEmpty())
                            <div class="kt-profile-table-scroll" tabindex="0" role="region" aria-label="Daftar anggota operasional">
                                <table class="kt-profile-table">
                                    <caption class="sr-only">Anggota operasional organisasi yang sedang dibuka</caption>
                                    <thead><tr><th scope="col">Nama personel</th><th scope="col">Peran & kategori</th><th scope="col">Bergabung</th><th scope="col">Keterangan tugas</th><th scope="col">Detail</th></tr></thead>
                                    <tbody>
                                        @foreach ($shown as $member)
                                            <tr>
                                                <td><div class="kt-profile-person"><span class="kt-profile-avatar" aria-hidden="true">{{ Format::initials($member['title']) }}</span><strong>{{ $member['title'] }}</strong></div></td>
                                                <td><div class="kt-profile-role"><strong>{{ $member['position'] ?: 'Belum dicatat' }}</strong><small>{{ $member['category'] }}</small></div></td>
                                                <td>{{ Format::date($member['joined']) }}</td>
                                                <td>{{ $member['notes'] ?: 'Belum dicatat' }}</td>
                                                <td><a class="kt-profile-row-link" href="/admin/member/{{ $member['id'] }}" aria-label="Lihat biodata {{ $member['title'] }}">Detail<x-dashboard.lks-icon name="arrow" /></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="kt-profile-empty"><x-dashboard.lks-icon name="users" /><h3>{{ $members->isNotEmpty() ? 'Anggota tidak ditemukan' : 'Belum ada anggota terdaftar' }}</h3><p>{{ $members->isNotEmpty() ? 'Coba nama, jabatan, atau kategori lainnya.' : 'Anggota yang disetujui akan muncul dalam daftar operasional organisasi.' }}</p></div>
                        @endif
                    </div>
                    <div class="kt-profile-note"><x-dashboard.lks-icon name="info" /><p>Daftar ini mencatat pengurus dan anggota yang berkegiatan. Jumlahnya tidak mewakili seluruh Warga Karang Taruna di kelurahan.</p></div>
                </section>
            </div>
            <aside class="kt-profile-aside">
                <section class="kt-profile-card" aria-labelledby="kt-profile-summary-title">
                    <header class="kt-profile-card-header"><span class="kt-profile-icon"><x-dashboard.lks-icon name="users" /></span><div><h2 id="kt-profile-summary-title">Anggota Organisasi</h2><p>Jumlah anggota yang sudah terdaftar.</p></div></header>
                    <div class="kt-profile-stats">
                        @foreach ($counts as $key => $count)
                            <div><strong data-kt-profile-stat="{{ $key }}">{{ $count }}</strong><span>{{ $key }}</span></div>
                        @endforeach
                    </div>
                    <div class="kt-profile-total"><x-dashboard.lks-icon name="users" /><strong>Total terdaftar <span>{{ $members->count() }}</span> anggota</strong></div>
                </section>
                <section class="kt-profile-card kt-profile-sidebar-note"><h2><x-dashboard.lks-icon name="info" />Cakupan Keanggotaan</h2><p>Daftar operasional memuat anggota organisasi yang telah disetujui. Gunakan direktori anggota untuk membuka biodata dan keterangan masing-masing personel.</p></section>
            </aside>
        </div>
    </section>
    @if ($organization['registrationId'])
        <section class="detail-card"><h2>Pendaftaran mitra</h2><p class="muted">Organisasi ini berasal dari pendaftaran publik yang disetujui Admin.</p><a class="text-button" href="/admin/registration/{{ $organization['registrationId'] }}">Lihat data dan berkas pendaftaran</a></section>
    @endif
    <section class="reports-panel">
        <div class="panel-heading">
            <h2>Unduh daftar anggota</h2>
            {{-- TODO(Tahap 2): ekspor dari admin/partner-export-kt.js. --}}
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;"><button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh PDF</button><button type="button" class="export-button" data-demo-download><x-dashboard.icon name="download" /> Unduh Excel</button></div>
        </div>
    </section>
    <section class="reports-panel">
        <div class="panel-heading"><h2>Arsip laporan lama</h2></div>
        <p class="notice">Arsip PSM/Karang Taruna dipertahankan sebagai riwayat. Laporan ini tidak menjadi kewajiban atau indikator aktif.</p>
        @include('shared.reports.table', ['reports' => $reports])
    </section>
@endsection
