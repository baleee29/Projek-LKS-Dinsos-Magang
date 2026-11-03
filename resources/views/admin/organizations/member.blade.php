@extends('layouts.dashboard')

@section('title', $member['title'])

@php
    use App\Support\Format;
    $rows = [
        ['NIK', $member['nik']], ['Nama lengkap', $member['title']], ['Tempat lahir', $member['birthPlace']],
        ['Tanggal lahir', Format::date($member['birthDate'])], ['Jenis kelamin', $member['gender']], ['Pekerjaan', $member['occupation'] ?: '—'],
        ['Alamat KTP', $member['ktpAddress']], ['No. HP / WhatsApp', $member['contact']], ['NPWP (opsional)', $member['npwp'] ?: 'Tidak dicantumkan'],
    ];
@endphp

@section('content')
    <div class="q-back-nav"><a class="text-button q-back-link" href="/admin/organization/{{ $organization['id'] }}">Kembali ke daftar anggota</a></div>
    <x-dashboard.section-heading :title="$member['title']" :description="($member['category'] ?: 'Anggota organisasi').' · '.$organization['title']" />
    <div class="detail-layout">
        <section class="detail-card q-member-biodata">
            <h2>Identitas anggota</h2>
            <dl class="field-grid">
                @foreach ($rows as [$label, $value])
                    <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                @endforeach
            </dl>
        </section>
        <section class="detail-card">
            <h2>Keanggotaan</h2>
            <dl class="field-grid">
                <div><dt>Peran / jabatan</dt><dd>{{ $member['position'] ?: 'Belum dicatat' }}</dd></div>
                <div><dt>Kategori</dt><dd>{{ $member['category'] ?: 'Belum dicatat' }}</dd></div>
                <div><dt>Tanggal bergabung</dt><dd>{{ Format::date($member['joined']) }}</dd></div>
                <div><dt>Keterangan</dt><dd>{{ $member['notes'] ?: 'Belum dicatat' }}</dd></div>
            </dl>
            @if ($member['isChair'])
                <p class="muted">Ketua penanggung jawab akun dipertahankan dalam daftar organisasi.</p>
            @endif
        </section>
        @if (count($member['attachments']))
            <section class="detail-card">
                <h2>Berkas pendukung ({{ count($member['attachments']) }})</h2>
                <div class="q-files-stack">
                    @foreach ($member['attachments'] as $attachment)
                        <div class="q-file-row"><div class="q-file-info"><strong>{{ $attachment['name'] }}</strong><small class="muted">{{ ceil($attachment['size'] / 1024) }} KB · {{ $attachment['type'] }}</small></div><button class="export-button" type="button" data-demo-download>Unduh Berkas</button></div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
