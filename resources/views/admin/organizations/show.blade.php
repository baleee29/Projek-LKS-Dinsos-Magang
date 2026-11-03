@extends('layouts.dashboard')

@section('title', 'Profil & Legalitas')

@section('content')
    @include('shared.lks-profile-body')
    @if ($organization['registrationId'])
        <section class="detail-card"><h2>Pendaftaran lembaga</h2><p class="muted">Lembaga ini berasal dari pendaftaran publik yang disetujui Admin.</p><a class="text-button" href="/admin/registration/{{ $organization['registrationId'] }}">Lihat data dan berkas pendaftaran</a></section>
    @endif
    <section class="reports-panel" id="lks-staff-section">
        <div class="panel-heading">
            <h2>Pengurus & SDM</h2>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;"><button type="button" class="export-button" data-export-url="/ekspor/pengurus?org={{ $organization['id'] }}" data-export-format="pdf"><x-dashboard.icon name="download" /> Unduh PDF</button><button type="button" class="export-button" data-export-url="/ekspor/pengurus?org={{ $organization['id'] }}" data-export-format="xlsx"><x-dashboard.icon name="download" /> Unduh Excel</button></div>
        </div>
        @include('admin.organizations.member-table', ['members' => $members])
    </section>
    <section class="reports-panel">
        <div class="panel-heading"><h2>Riwayat laporan LKS yang diajukan</h2></div>
        @include('shared.reports.table', ['reports' => $reports])
    </section>
@endsection
