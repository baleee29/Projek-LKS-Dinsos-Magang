@extends('layouts.dashboard')

@section('title', 'Data LKS')

@php
    use App\Support\Format;
    $rosterLabel = 'SDM tercatat';
@endphp

@section('content')
    <section class="admin-partner-view">
        <header class="admin-partner-heading">
            <div><small>SiDaya · Admin Dinas Sosial</small><h1 id="view-title" tabindex="-1">Data LKS</h1><p>Direktori lembaga kesejahteraan sosial beserta SDM yang tercatat.</p></div>
            <div class="admin-partner-heading-actions"><button type="button" class="export-button" data-export-url="/ekspor/lembaga" data-export-format="xlsx"><x-dashboard.icon name="download" /> Unduh Excel</button><button type="button" class="export-button" data-export-url="/ekspor/lembaga" data-export-format="pdf"><x-dashboard.icon name="download" /> Unduh PDF</button></div>
        </header>
        <p class="admin-partner-source-note">Daftar memuat data lembaga dan pendaftaran publik yang telah disetujui.</p>
        @include('admin.organizations.controls', ['searchLabel' => 'Cari nama lembaga, ketua, atau kontak', 'placeholder' => 'Cari nama lembaga, ketua, atau kontak'])
        <section class="admin-partner-card" aria-label="Data LKS">
            <div class="admin-partner-list-heading"><strong role="status" aria-live="polite">{{ $organizations->total() }} lembaga</strong><span>Profil tersimpan</span></div>
            <div>
                @if ($organizations->count())
                    <div class="admin-partner-table-scroll" tabindex="0" role="region" aria-label="Data LKS">
                        <table class="admin-partner-table q-table-numbered">
                            <caption class="sr-only">Data LKS</caption>
                            <thead><tr><th scope="col" class="q-col-number">No.</th><th scope="col">Lembaga</th><th scope="col">Wilayah</th><th scope="col">Ketua / kontak</th><th scope="col">{{ $rosterLabel }}</th><th scope="col">Detail</th></tr></thead>
                            <tbody>
                                @foreach ($organizations as $index => $item)
                                    @php
                                        $roster = $rosterOf($item['id']);
                                        $map = Format::coordinates($item['latitude'], $item['longitude']);
                                    @endphp
                                    <tr>
                                        <td class="q-col-number" data-label="No.">{{ $organizations->firstItem() + $index }}</td>
                                        <td data-label="Lembaga"><div style="display:flex;align-items:center;gap:10px;"><span style="width:32px;height:32px;border-radius:6px;background:var(--color-bg-subtle,#f1f5f9);display:inline-flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;border:1px solid var(--color-border,#e2e8f0);">🏢</span><div><a class="admin-partner-record" href="/admin/organization/{{ $item['id'] }}">{{ $item['title'] }}</a><small>{{ $item['status'] }}</small></div></div></td>
                                        <td data-label="Wilayah">{{ $item['village'] }}<small>{{ $item['district'] }}</small>@if ($map['has'])<div style="margin-top:4px;"><a href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" style="font-size:0.75rem;display:inline-flex;align-items:center;gap:3px;color:var(--color-primary,#1d4ed8);text-decoration:none;font-weight:600;background:var(--color-bg-subtle,#f1f5f9);padding:2px 6px;border-radius:4px;border:1px solid var(--color-border,#cbd5e1);" title="Buka titik koordinat di Google Maps">📍 {{ $item['latitude'] }}, {{ $item['longitude'] }} ↗</a></div>@endif</td>
                                        <td data-label="Ketua / kontak">{{ $item['chair'] }}<small>{{ $item['contact'] }}</small></td>
                                        <td data-label="{{ $rosterLabel }}">{{ $roster->count() }} orang</td>
                                        <td data-label="Detail"><a class="admin-partner-button" href="/admin/organization/{{ $item['id'] }}">Lihat lembaga</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="admin-partner-empty"><x-dashboard.icon name="building" /><h2>Belum ada data sesuai pilihan</h2><p>Ubah pencarian atau filter untuk melihat lembaga lainnya.</p></div>
                @endif
            </div>
            @include('admin.organizations.footer', ['paginator' => $organizations, 'noun' => 'lembaga'])
        </section>
    </section>
@endsection
