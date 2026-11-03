@extends('layouts.dashboard')

@section('title', 'Pendaftaran LKS')

@php
    use App\Support\Format;
@endphp

@section('content')
    <section class="admin-registration-view">
        <header class="admin-registration-heading"><div><small>SiDaya · Admin Dinas Sosial</small><h1 id="view-title" tabindex="-1">Pendaftaran LKS</h1><p>Periksa data ringkas dan dokumen pendaftaran, lalu berikan keputusan beserta catatan.</p></div><span class="admin-registration-badge">Verifikasi Berkas</span></header>
        <p class="admin-registration-note">Pengajuan dan berkas berasal dari pendaftaran mandiri LKS. Persetujuan menambahkan lembaga ke direktori organisasi.</p>
        <form class="admin-registration-controls" method="get" data-filter-form>
            <label>Cari pendaftaran<input type="search" id="registration-search" name="q" value="{{ $filters['q'] }}" placeholder="Nama lembaga, ketua, kontak…" @if ($filters['q']) data-autofocus @endif></label>
            <label>Status
                <select name="status">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $value)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ \App\Support\Data\DataPendaftaran::statusLabel($value) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="button" class="admin-registration-button" data-href="{{ request()->fullUrl() }}">Perbarui</button>
        </form>
        @if ($rows->count())
            <div class="admin-registration-table">
                <table class="q-table-numbered">
                    <thead><tr><th scope="col" class="q-col-number">No.</th><th>Lembaga</th><th>Ketua / kontak</th><th>Dikirim</th><th>Status</th><th>Detail</th></tr></thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                <td class="q-col-number" data-label="No.">{{ $rows->firstItem() + $index }}</td>
                                <td><strong>{{ $row['organization_name'] }}</strong></td>
                                <td>{{ $row['leader_name'] }}<small>{{ $row['whatsapp'] ?: 'Kontak belum dicatat' }}</small></td>
                                <td>{{ Format::dateTime($row['created_at']) }}</td>
                                <td>@include('admin.registrations.status', ['status' => $row['status']])</td>
                                <td><a class="admin-registration-button" href="/admin/registration/{{ $row['id'] }}">Periksa</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="admin-registration-empty"><h2>Belum ada pendaftaran sesuai pilihan</h2><p>Ubah pencarian atau filter untuk melihat pendaftaran lainnya.</p></div>
        @endif
        <nav class="admin-registration-pagination" aria-label="Halaman pendaftaran">
            <button type="button" class="admin-registration-button" @if ($rows->currentPage() <= 1) disabled @else data-href="{{ $rows->previousPageUrl() }}" @endif>Sebelumnya</button>
            <span>Halaman {{ $rows->currentPage() }} dari {{ $rows->lastPage() }}</span>
            <button type="button" class="admin-registration-button" @if ($rows->currentPage() >= $rows->lastPage()) disabled @else data-href="{{ $rows->nextPageUrl() }}" @endif>Berikutnya</button>
        </nav>
    </section>
@endsection
