@extends('layouts.dashboard')

@section('title', $item['title'])

@php
    use App\Support\Demo\DemoLetters;
    use App\Support\Format;
    $fields = [
        'Nama kegiatan' => $item['title'],
        'Latar belakang' => $item['background'],
        'Tujuan' => $item['purpose'],
        'Bentuk kegiatan' => $kinds[$item['type']] ?? $item['type'],
        'Tanggal kegiatan' => Format::date($item['date']),
        'Waktu' => $item['time'],
        'Lokasi' => $item['location'],
        'Sasaran' => $item['target'],
        'Perkiraan peserta' => $item['participants'].' orang (perkiraan)',
        'Penanggung jawab' => $item['responsible'],
        'WhatsApp' => $item['contact'],
    ];
    $orgFields = [
        'Nama kelompok' => $organization['title'],
        'Ketua' => $organization['chair'],
        'Kontak' => $organization['contact'],
        'Kelurahan' => $organization['village'],
        'Kecamatan' => $organization['district'],
    ];
    $action = '<a class="admin-correspondence-button" href="/admin/new-reply-letter?org='.e($item['organizationId']).'&activity='.e($item['id']).'">Buat surat balasan</a>';
@endphp

@push('scripts')
    @vite(['resources/js/pages/correspondence.js'])
@endpush

@section('content')
    <section class="admin-correspondence-view">
        @include('admin.correspondence.heading', ['title' => $item['title'], 'description' => 'Informasi dari versi pengajuan terakhir yang dikirim mitra.', 'action' => $action, 'back' => 'incoming-letters/karang-taruna'])
        <div class="admin-correspondence-grid">
            <div>
                <section class="admin-correspondence-card">
                    <h2>Pengajuan kegiatan</h2>
                    <dl class="admin-correspondence-detail">
                        @foreach ($fields as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                        @endforeach
                    </dl>
                </section>
                <section class="admin-correspondence-card">
                    <h2>Organisasi pengaju</h2>
                    <dl class="admin-correspondence-detail">
                        @foreach ($orgFields as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                        @endforeach
                    </dl>
                </section>
                <section class="admin-correspondence-card">
                    <h2>Lampiran asli</h2>
                    @if (count($item['attachments']))
                        <ul class="admin-correspondence-files">
                            @foreach ($item['attachments'] as $file)
                                <li><span><strong>{{ $file['name'] }}</strong><small>{{ DemoLetters::fileSize($file['size']) }}</small></span><button class="admin-correspondence-button" type="button" data-demo-download>Unduh</button></li>
                            @endforeach
                        </ul>
                    @else
                        <p>Tidak ada lampiran pada pengajuan ini.</p>
                    @endif
                </section>
                @include('admin.correspondence.history', ['history' => $item['history']])
            </div>
            <aside>
                <section class="admin-correspondence-card">
                    <h2>Pemeriksaan Admin</h2>
                    @include('admin.correspondence.status', ['status' => $item['status']])
                    <p>Diajukan {{ Format::date($item['submittedAt']) }}. Keputusan pengajuan dan penerbitan surat balasan adalah dua tindakan terpisah.</p>
                    @if ($item['reviewNote'])
                        <p class="admin-correspondence-note">{{ $item['reviewNote'] }}</p>
                    @endif
                    @if ($item['status'] === 'Perlu Perbaikan')
                        <p>Mitra dapat memperbaiki dan mengirim kembali. Perubahan yang belum dikirim tidak ditampilkan kepada Admin.</p>
                    @endif
                    @if ($reviewable)
                        {{-- TODO(Tahap 2): POST /admin/karang-taruna/activities/{id}/review. --}}
                        <form data-correspondence-review novalidate>
                            <label><span>Catatan pemeriksaan</span><textarea name="note" maxlength="3000" rows="5" placeholder="Wajib untuk perbaikan atau penolakan"></textarea></label>
                            <p role="alert" tabindex="-1" data-correspondence-error hidden></p>
                            <div class="admin-correspondence-actions">
                                @if ($item['status'] === 'Menunggu Pemeriksaan')
                                    <button class="admin-correspondence-button" name="decision" value="Ditinjau" type="submit">Mulai tinjau</button>
                                @endif
                                <button class="admin-correspondence-button" name="decision" value="Perlu Perbaikan" type="submit">Minta perbaikan</button>
                                <button class="admin-correspondence-button is-primary" name="decision" value="Disetujui" type="submit">Setujui kegiatan</button>
                                <button class="admin-correspondence-button is-danger" name="decision" value="Ditolak" type="submit">Tolak kegiatan</button>
                            </div>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </section>
@endsection
