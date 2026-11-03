@extends('layouts.dashboard')

@section('title', 'Pengurus & SDM')

@php
    use App\Support\Format;
@endphp

@section('content')
    <section class="lks-workspace lks-staff-view">
        <div class="lks-staff-metrics">
            @foreach ([
                ['users', 'Total Personel', $members->count(), 'Orang terdaftar'],
                ['shield', 'Kepengurusan', $organization['term'] ? 'Tercatat' : 'Belum dicatat', $organization['term'] ?: 'Periode belum diisi'],
                ['check', 'Penambahan SDM', 'Langsung Terdaftar', 'Personel baru langsung tercatat'],
                ['heart', 'Rasio Layanan / Klien', $ratio, 'Personel layanan : penghuni panti'],
            ] as [$symbol, $label, $value, $note])
                <article class="lks-card lks-staff-metric"><span class="lks-icon-tile"><x-dashboard.lks-icon :name="$symbol" /></span><div><h2>{{ $label }}</h2><strong>{{ $value }}</strong><small>{{ $note }}</small></div></article>
            @endforeach
        </div>
        <x-dashboard.lks-heading title="Pengurus & SDM" :description="'Kelola pengurus, staf, tenaga layanan, dan relawan di '.$organization['title'].'.'">
            <button type="button" class="lks-button" data-export-url="/ekspor/pengurus" data-export-format="pdf"><x-dashboard.lks-icon name="download" /> Unduh PDF</button>
            <button type="button" class="lks-button" data-export-url="/ekspor/pengurus" data-export-format="xlsx"><x-dashboard.lks-icon name="download" /> Unduh Excel</button>
            <a class="lks-button lks-button-primary" href="/lks/new-member"><x-dashboard.lks-icon name="plus" />Tambah Personel</a>
        </x-dashboard.lks-heading>
        <div class="lks-info"><x-dashboard.lks-icon name="info" /><p><strong>Pengelolaan SDM lembaga:</strong> personel baru langsung tercatat dalam daftar Pengurus & SDM. Data personel yang sudah terdaftar tetap dapat diperbarui. Data Pengurus & SDM terpisah dari biodata klien.</p></div>
        <form class="lks-card lks-staff-controls" aria-label="Pencarian dan kategori personel" method="get" data-filter-form>
            <label class="lks-search"><x-dashboard.lks-icon name="search" /><span class="sr-only">Cari nama, NIK, atau peran personel</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama atau peran…" @if ($filters['q']) data-autofocus @endif></label>
            <input type="hidden" name="category" value="{{ $filters['category'] }}">
            <div class="lks-category-filters" role="group" aria-label="Kategori personel">
                <button @class(['lks-filter', 'is-active' => $filters['category'] === '']) type="button" aria-pressed="{{ $filters['category'] === '' ? 'true' : 'false' }}" data-href="{{ request()->fullUrlWithQuery(['category' => null]) }}">Semua Kategori</button>
                @foreach ($categories as $category)
                    <button @class(['lks-filter', 'is-active' => $filters['category'] === $category]) type="button" aria-pressed="{{ $filters['category'] === $category ? 'true' : 'false' }}" data-href="{{ request()->fullUrlWithQuery(['category' => $category]) }}">{{ $category === 'Tenaga layanan' ? 'Tenaga Layanan' : $category }}</button>
                @endforeach
            </div>
            <span class="lks-roster-count">{{ $members->count() }} personel terdaftar</span>
        </form>
        <section class="lks-card lks-roster" aria-label="Daftar Pengurus dan SDM">
            <div>
                @if ($shown->isNotEmpty())
                    <div class="lks-table-scroll" tabindex="0" role="region" aria-label="Tabel Pengurus dan SDM">
                        <table class="lks-table lks-staff-table">
                            <caption class="sr-only">Pengurus dan SDM {{ $organization['title'] }}</caption>
                            <thead><tr><th scope="col" class="lks-col-number">No.</th><th scope="col">Nama Lengkap & NIK</th><th scope="col">Peran / Kategori</th><th scope="col">Bergabung</th><th scope="col">Keterangan / Penugasan</th><th scope="col">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($shown as $member)
                                    <tr>
                                        <td class="lks-col-number" data-label="No.">{{ $loop->iteration }}</td>
                                        <td><div class="lks-person"><span class="lks-initials" aria-hidden="true">{{ Format::initials($member['title']) }}</span><div><strong>{{ $member['title'] }}</strong>@if ($member['isChair'])<span class="lks-chair-label">Penanggung Jawab</span>@endif<small>NIK {{ Format::maskNik($member['nik']) }}</small></div></div></td>
                                        <td>{{ $member['position'] ?: 'Belum dicatat' }}<small class="lks-cell-meta">{{ $member['category'] ?: 'Belum dicatat' }}</small></td>
                                        <td>{{ Format::date($member['joined']) }}</td>
                                        <td>{{ $member['notes'] ?: 'Belum dicatat' }}</td>
                                        <td><a class="lks-row-action" href="/lks/member/{{ $member['id'] }}" aria-label="Detail personel {{ $member['title'] }}">Detail <x-dashboard.lks-icon name="arrow" /></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state variant="lks" title="Tidak ada personel yang sesuai" description="Ubah pencarian atau pilih kategori lainnya." />
                @endif
            </div>
            <p class="lks-list-count" role="status" aria-live="polite">{{ $shown->count() }} dari {{ $members->count() }} personel ditampilkan</p>
        </section>
        <section class="lks-card lks-staff-notes" aria-label="Catatan pengelolaan personel">
            <article><span class="lks-icon-tile"><x-dashboard.lks-icon name="file" /></span><div><h2>Identitas Personel</h2><p>NIK yang diisi diperiksa formatnya. Daftar ini menyamarkan NIK; biodata lengkap tersedia pada halaman detail.</p></div></article>
            <article><span class="lks-icon-tile"><x-dashboard.lks-icon name="history" /></span><div><h2>Riwayat Laporan</h2><p>Identitas yang sudah dicatat dalam laporan tidak berubah saat data personel diperbarui.</p></div></article>
            <article><span class="lks-icon-tile"><x-dashboard.lks-icon name="lock" /></span><div><h2>Penanggung Jawab</h2><p>Ketua lembaga tidak dapat dihapus melalui daftar personel. Perbarui penanggung jawab melalui profil organisasi.</p></div></article>
        </section>
    </section>
@endsection
