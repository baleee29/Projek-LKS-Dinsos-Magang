@extends('layouts.dashboard')

@section('title', 'Profil & Legalitas')

@php
    use App\Support\Format;
    $org = $organization;
    $region = 'Kel. '.($org['village'] ?: '-').', Kec. '.($org['district'] ?: '-');
@endphp

@section('content')
    <section class="lks-workspace lks-profile-view">
        <x-dashboard.lks-heading title="Profil & Legalitas" :description="$org['title'].' · Kelurahan '.($org['village'] ?: '-').', Kecamatan '.($org['district'] ?: '-')">
            @unless ($admin)
                <button class="lks-button lks-button-primary" type="button" data-open-dialog="edit-profile-dialog"><x-dashboard.lks-icon name="edit" />Edit Profil</button>
            @endunless
        </x-dashboard.lks-heading>
        @if ($admin)
            <p class="lks-admin-back"><a href="/admin/organizations/lks"><x-dashboard.lks-icon name="arrow" />Kembali ke data LKS</a></p>
        @endif
        <div class="lks-profile-grid">
            <div class="lks-profile-main">
                <section class="lks-card">
                    <header class="lks-card-heading lks-card-heading-tinted"><span class="lks-icon-tile"><x-dashboard.lks-icon name="building" /></span><div><h2>Identitas Organisasi</h2><p>Data induk lembaga pada akun SiDaya.</p></div><span class="lks-id">ID: {{ $org['id'] }}</span></header>
                    <dl class="lks-values">
                        <div><dt>Nama Lembaga</dt><dd>{{ $org['title'] ?: 'Belum dicatat' }}</dd></div>
                        <div><dt>Penanggung Jawab</dt><dd>{{ $org['chair'] ?: 'Belum dicatat' }}</dd></div>
                        <div><dt>Wilayah Administratif</dt><dd>{{ $region }}</dd></div>
                        <div><dt>Terdaftar Sejak</dt><dd>{{ Format::date($org['registeredFrom']) }}</dd></div>
                        <div class="lks-value-wide"><dt>Alamat Sekretariat</dt><dd>{{ $org['address'] ?: 'Belum dicatat' }}</dd></div>
                        <div class="lks-value-wide"><dt>Kontak Resmi</dt><dd>{{ $org['contact'] ?: 'Belum dicatat' }}</dd></div>
                        <div class="lks-value-wide">
                            <dt>Titik Koordinat Sekretariat</dt>
                            <dd style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                @if ($map['has'])
                                    <strong>{{ $map['text'] }}</strong> <a href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" style="color:var(--lks-primary,#0284c7);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">📍 Buka Google Maps ↗</a>
                                @else
                                    <span style="color:var(--lks-muted,#64748b);">Belum dicatat</span>
                                @endif
                                @unless ($admin)
                                    <button type="button" class="lks-button lks-button-secondary" data-open-dialog="edit-profile-dialog" style="padding:3px 8px;font-size:12px;margin-left:auto;">{{ $map['has'] ? 'Ubah Koordinat' : '+ Atur Koordinat' }}</button>
                                @endunless
                            </dd>
                        </div>
                    </dl>
                </section>
                <section class="lks-card lks-map-card">
                    <header class="lks-card-heading lks-card-heading-tinted">
                        <span class="lks-icon-tile">📍</span>
                        <div style="flex:1;">
                            <h2>Titik Lokasi &amp; Peta LKS</h2>
                            <p>Titik koordinat keberadaan sekretariat/kantor LKS yang dapat dipantau oleh Dinas Sosial (Admin).</p>
                        </div>
                        @if ($map['has'])
                            <a href="{{ $map['mapUrl'] }}" target="_blank" rel="noopener noreferrer" class="lks-button lks-button-secondary" style="font-size:12px;padding:6px 12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">Buka Layar Penuh ↗</a>
                        @endif
                    </header>
                    @if ($map['has'])
                        <div class="lks-map-meta">
                            <div class="lks-map-coords">
                                <strong>Koordinat GPS:</strong>
                                <code>{{ $map['text'] }}</code>
                                <span>({{ $region }})</span>
                            </div>
                            @unless ($admin)
                                <button type="button" class="lks-button lks-button-secondary" data-open-dialog="edit-profile-dialog" style="font-size:12px;padding:4px 10px;">📍 Perbarui Titik GPS</button>
                            @endunless
                        </div>
                        <div class="lks-map-container" style="margin-top:14px;">
                            <iframe src="{{ $map['embedUrl'] }}" loading="lazy" title="Peta Titik Lokasi LKS"></iframe>
                        </div>
                    @else
                        <div style="padding:28px 24px;text-align:center;">
                            <div style="width:52px;height:52px;border-radius:50%;background:rgba(2,132,199,0.1);color:#0284c7;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;margin-bottom:12px;">📍</div>
                            <h3 style="margin:0 0 6px 0;font-size:1rem;color:var(--lks-heading,#0f172a);">Titik Koordinat Belum Diatur</h3>
                            <p style="margin:0 0 16px 0;font-size:13px;color:var(--lks-muted,#64748b);max-width:480px;margin-inline:auto;">
                                {{ $admin ? 'Lembaga ini belum mengatur titik koordinat sekretariat.' : 'Atur titik koordinat lokasi lembaga Anda agar Admin Dinas Sosial dapat memantau dan memverifikasi keberadaan fisik sekretariat LKS di peta.' }}
                            </p>
                            @unless ($admin)
                                <button type="button" class="lks-button lks-button-primary" data-open-dialog="edit-profile-dialog">📍 Atur Titik Lokasi Sekarang</button>
                            @endunless
                        </div>
                    @endif
                </section>
                <section class="lks-card lks-legal-card">
                    <header class="lks-card-heading"><span class="lks-icon-tile"><x-dashboard.lks-icon name="shield" /></span><div><h2>Dokumen & Keterangan Legalitas</h2><p>Informasi akta, tanda daftar, dan dokumen lembaga yang telah dicatat.</p></div></header>
                    <pre class="lks-legal-text">{{ $org['legal'] ?: 'Keterangan legalitas belum dicatat.' }}</pre>
                    @unless ($admin)
                        <div class="lks-info"><x-dashboard.lks-icon name="info" /><p>Gunakan keterangan sesuai dokumen lembaga. Perubahan profil digunakan pada pengajuan berikutnya; identitas dalam laporan yang sudah diajukan tetap tercatat.</p></div>
                    @endunless
                </section>
            </div>
            <aside class="lks-card lks-operational-summary" aria-labelledby="lks-operational-title">
                <h2 id="lks-operational-title">Ringkasan Operasional</h2>
                <p>Jumlah personel dan klien pada data lembaga.</p>
                <div class="lks-operational-metrics">
                    <a class="lks-operational-metric" href="{{ $admin ? '#lks-staff-section' : '/lks/members' }}"><div><span>Pengurus & SDM Terdaftar</span><strong>Pengurus & SDM <x-dashboard.lks-icon name="arrow" /></strong></div><div><b>{{ $memberCount }}</b><small>ORANG</small></div></a>
                    <a class="lks-operational-metric" href="{{ $admin ? '/admin/occupancy?org='.$org['id'] : '/lks/occupancy' }}"><div><span>Penghuni di Panti</span><strong>Rekap Klien <x-dashboard.lks-icon name="arrow" /></strong></div><div><b>{{ $counts['residents'] }}</b><small>KLIEN</small></div></a>
                    <a class="lks-operational-metric" href="{{ $admin ? '/admin/occupancy?residence=&org='.$org['id'] : '/lks/occupancy?residence=' }}"><div><span>Penerima layanan tercatat</span><strong>Total Klien <x-dashboard.lks-icon name="arrow" /></strong></div><div><b class="lks-attention">{{ $counts['clients'] }}</b><small>KLIEN</small></div></a>
                </div>
            </aside>
        </div>
        @yield('profile-extra')
    </section>
    @unless ($admin)
        @include('lks.profile-dialog', ['org' => $org])
    @endunless
@endsection
