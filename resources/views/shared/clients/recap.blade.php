@extends('layouts.dashboard')

@php
    use App\Support\Format;
    $pageTitle = $admin ? 'Rekap Penghuni' : 'Rekap Klien';
    $rolePrefix = $admin ? '/admin' : '/lks';
    $active = $counts['active'];
@endphp

@section('title', $pageTitle)

@section('content')
    <section @class(['lks-workspace', 'lks-client-view', 'lks-occupancy-view', 'lks-client-admin-view' => $admin])>
        <x-dashboard.lks-heading :title="$pageTitle" description="Jumlah dan daftar klien terdaftar. Penghuni panti dihitung terpisah dari layanan nonresidensial." :group="$admin ? 'LKS' : 'Pendataan'">
            <button type="button" class="lks-button" data-export-url="/ekspor/klien" data-export-format="pdf"><x-dashboard.lks-icon name="download" /> Unduh PDF</button>
            <button type="button" class="lks-button" data-export-url="/ekspor/klien" data-export-format="xlsx"><x-dashboard.lks-icon name="download" /> Unduh Excel</button>
            @unless ($admin)
                <a class="lks-button lks-button-primary" href="/lks/new-resident"><x-dashboard.lks-icon name="plus" />Tambah Klien</a>
            @endunless
        </x-dashboard.lks-heading>
        <section class="lks-card lks-client-summary" aria-label="Ringkasan klien">
            <div class="lks-client-metrics">
                <article><h2><span class="lks-metric-dot" aria-hidden="true"></span>Masih tinggal di panti</h2><strong>{{ $counts['residents'] }}</strong><p>Status aktif bertempat tinggal di panti</p></article>
                <article><h2><span class="lks-metric-dot" aria-hidden="true"></span>Layanan nonresidensial</h2><strong>{{ $counts['nonresidential'] }}</strong><p>Menerima layanan tanpa tinggal di panti</p></article>
                <article><h2><span class="lks-metric-dot lks-metric-dot-muted" aria-hidden="true"></span>Sudah keluar</h2><strong>{{ $counts['exited'] }}</strong><p>Tidak dihitung sebagai penghuni aktif</p></article>
                <article><h2><span class="lks-metric-dot lks-metric-dot-alert" aria-hidden="true"></span>Total klien</h2><strong>{{ $total }}</strong><p>Seluruh penerima layanan terdaftar</p></article>
            </div>
            <div class="lks-demographics">
                <h3>Demografi Penghuni Panti:</h3>
                <span>Laki-laki: <strong>{{ $active->where('gender', 'Laki-laki')->count() }} orang</strong></span>
                <span>Perempuan: <strong>{{ $active->where('gender', 'Perempuan')->count() }} orang</strong></span>
                <span>Belum diketahui: <strong>{{ $active->whereNotIn('gender', ['Laki-laki', 'Perempuan'])->count() }} orang</strong></span>
                <p><x-dashboard.lks-icon name="info" />Jumlah ini berbeda dari rekap penerima manfaat dalam laporan semester.</p>
            </div>
        </section>
        <form class="lks-card lks-client-controls" aria-label="Pencarian dan filter klien" method="get" data-filter-form data-keep-empty="residence">
            <div class="lks-client-filter-row">
                <label class="lks-search"><x-dashboard.lks-icon name="search" /><span class="sr-only">Cari nama atau nomor registrasi</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama atau nomor registrasi…" @if ($filters['q']) data-autofocus @endif></label>
                @if ($admin)
                    <x-dashboard.lks-select name="org" label="Lembaga" all="Semua LKS" :value="$filters['org']" :options="$organizations->map(fn ($org) => [$org['id'], $org['title']])->all()" />
                    <x-dashboard.lks-select name="district" label="Kecamatan" all="Semua Kecamatan" :value="$filters['district']" :options="$districts" />
                    <x-dashboard.lks-select name="village" label="Kelurahan" all="Semua Kelurahan" :value="$filters['village']" :options="$villages" />
                @endif
                <x-dashboard.lks-select name="category" label="Kelompok layanan" all="Semua Kelompok" :value="$filters['category']" :options="$options['category']" />
                <x-dashboard.lks-select name="gender" label="Jenis kelamin" all="Semua Gender" :value="$filters['gender']" :options="$options['gender']" />
                <x-dashboard.lks-select name="residence" label="Keberadaan" all="Semua Keberadaan" :value="$filters['residence']" :options="[['living', 'Tinggal di panti'], ['nonresidential', 'Layanan nonresidensial'], ['exited', 'Sudah keluar']]" />
                <a class="lks-button lks-button-plain" href="{{ request()->url() }}"><x-dashboard.lks-icon name="reset" />Hapus Filter</a>
            </div>
        </form>
        <section class="lks-card lks-client-results" aria-label="Daftar klien">
            <div>
                @if ($clients->count())
                    <div class="lks-table-scroll" tabindex="0" role="region" aria-label="Tabel klien terdaftar">
                        <table class="lks-table lks-client-table">
                            <caption class="sr-only">Rekap klien terdaftar — {{ $caption }}</caption>
                            <thead><tr><th scope="col" class="lks-col-number">No.</th><th scope="col">Nama / Registrasi</th>@if ($admin)<th scope="col">Lembaga / Wilayah</th>@endif<th scope="col">Kelompok / Usia</th><th scope="col">Keberadaan</th><th scope="col">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($clients as $index => $client)
                                    @php
                                        $query = http_build_query(array_filter(request()->only(['q', 'category', 'gender', 'org', 'district', 'village', 'residence', 'page']), fn ($value) => $value !== null));
                                        $owner = $organizationName($client['organizationId']);
                                    @endphp
                                    <tr>
                                        <td class="lks-col-number" data-label="No.">{{ $clients->firstItem() + $index }}</td>
                                        <td><div class="lks-person"><span class="lks-initials" aria-hidden="true">{{ Format::initials($client['name']) }}</span><div><strong>{{ $client['name'] ?: 'Nama belum diisi' }}</strong><small class="lks-registration-code">{{ $client['code'] ?: 'Nomor belum diisi' }}</small></div></div></td>
                                        @if ($admin)
                                            <td><strong>{{ $owner['title'] }}</strong><small class="lks-cell-meta">{{ $owner['village'] }}, {{ $owner['district'] }}</small></td>
                                        @endif
                                        <td>{{ $client['category'] ?: 'Belum dicatat' }}<small class="lks-cell-meta">{{ Format::age($client, $today) }}@if ($client['gender']) · {{ $client['gender'] }}@endif</small></td>
                                        <td><span @class(['lks-existence', 'lks-existence-exited' => $client['residenceStatus'] === 'Sudah keluar'])>{{ Format::existence($client) }}</span></td>
                                        <td>
                                            <div class="lks-row-actions">
                                                <a class="lks-row-action lks-row-action-detail" href="{{ $rolePrefix }}/resident/{{ $client['id'] }}{{ $query ? '?'.$query : '' }}" aria-label="Detail klien {{ $client['name'] }}"><x-dashboard.lks-icon name="eye" />Detail</a>
                                                @unless ($admin)
                                                    <a class="lks-row-action lks-row-action-edit" href="/lks/edit-resident/{{ $client['id'] }}" aria-label="Edit data klien {{ $client['name'] }}"><x-dashboard.lks-icon name="edit" />Edit</a>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state variant="lks" title="Belum ada data sesuai pilihan" description="Ubah filter untuk melihat klien tersimpan lainnya." />
                @endif
            </div>
            <x-dashboard.pagination :paginator="$clients" variant="lks" noun="klien terdaftar" label="Halaman daftar klien" />
        </section>
        <aside class="lks-info lks-recap-note"><x-dashboard.lks-icon name="shield" /><div><h2>Catatan {{ $pageTitle }}</h2><p>Rekap menghitung seluruh klien tersimpan dengan status bertempat tinggal di panti.</p></div></aside>
    </section>
@endsection
