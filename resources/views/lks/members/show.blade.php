@extends('layouts.dashboard')

@section('title', $member['title'])

@php
    use App\Support\Format;
    $identity_rows = [
        ['NIK', $member['nik']],
        ['Nama lengkap', $member['title']],
        ['Tempat lahir', $member['birthPlace']],
        ['Tanggal lahir', Format::date($member['birthDate'])],
        ['Jenis kelamin', $member['gender']],
        ['Pekerjaan', $member['occupation'] ?: '—'],
        ['Alamat KTP', $member['ktpAddress']],
        ['No. HP / WhatsApp', $member['contact']],
        ['NPWP (opsional)', $member['npwp'] ?: 'Tidak dicantumkan'],
    ];
    $membership_rows = [
        ['Peran / jabatan', $member['position']],
        ['Kategori', $member['category']],
        ['Tanggal bergabung', Format::date($member['joined'])],
        ['Keterangan', $member['notes']],
    ];
@endphp

@section('content')
    <div class="q-back-nav"><a class="text-button q-back-link" href="/lks/members">Kembali ke daftar anggota</a></div>
    <x-dashboard.section-heading :title="$member['title']" :description="($member['category'] ?: 'Anggota organisasi').' · '.$organization['title']" :eyebrow="$organization['title']">
        <a class="export-button" href="/lks/edit-member/{{ $member['id'] }}">Edit keterangan</a>
        @unless ($member['isChair'])
            <button class="psm-delete-button" type="button" data-open-dialog="delete-member-dialog">Hapus anggota</button>
        @endunless
    </x-dashboard.section-heading>
    <div class="detail-layout">
        <section class="detail-card q-member-biodata">
            <h2>Identitas anggota</h2>
            <dl class="field-grid">
                @foreach ($identity_rows as [$label, $value])
                    <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                @endforeach
            </dl>
        </section>
        <section class="detail-card">
            <h2>Keanggotaan</h2>
            <dl class="field-grid">
                @foreach ($membership_rows as [$label, $value])
                    <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Belum dicatat' }}</dd></div>
                @endforeach
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
    <x-dashboard.dialog id="delete-member-dialog" title="Hapus anggota?">
        <p>Hapus {{ $member['title'] }} dari daftar organisasi?</p>
        <p class="muted">Nama yang sudah tercatat dalam laporan tetap tersedia dalam laporan tersebut.</p>
        <x-slot:actions>
            <button class="text-button" type="button" data-close-dialog>Batal</button><button class="psm-delete-confirm" type="button" data-demo-action>Ya, hapus anggota</button>
        </x-slot:actions>
    </x-dashboard.dialog>
@endsection
