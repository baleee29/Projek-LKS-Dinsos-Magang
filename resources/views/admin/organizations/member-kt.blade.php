@extends('layouts.dashboard')

@section('title', $member['title'])

@php
    use App\Support\Format;
    $field = function (string $label, ?string $value, string $key, bool $wide = false, string $fallback = 'Belum dicatat') {
        $available = $value !== null && $value !== '';

        return '<div'.($wide ? ' class="is-wide"' : '').' data-kt-member-detail-field="'.e($key).'"><dt>'.e($label).'</dt><dd'.($available ? '' : ' class="is-empty"').'><span>'.e($available ? $value : $fallback).'</span></dd></div>';
    };
    $heading = fn (string $title, string $action = '') => '<div class="kt-member-detail-card-heading"><h2><span>'.e($title).'</span></h2>'.$action.'</div>';
@endphp

@section('content')
    <section class="kt-member-detail-view">
        <a class="kt-member-detail-back" href="/admin/organization/{{ $organization['id'] }}"><x-dashboard.lks-icon name="arrow" /><span>Kembali ke profil organisasi</span></a>
        <header class="kt-member-detail-heading">
            <div><small>{{ $organization['title'] }}</small><h1 id="view-title" tabindex="-1">{{ $member['title'] }}</h1><p>{{ $member['category'] }} · {{ $organization['title'] }}</p></div>
        </header>
        <div class="kt-member-detail-layout">
            <div class="kt-member-detail-primary">
                <section class="kt-member-detail-card">
                    {!! $heading('Identitas anggota', '<span class="kt-member-detail-badge">Terdaftar</span>') !!}
                    <dl class="kt-member-detail-fields">
                        {!! $field('NIK', $member['nik'], 'nik') !!}
                        {!! $field('Nama lengkap', $member['title'], 'title') !!}
                        {!! $field('Tempat lahir', $member['birthPlace'], 'birthPlace') !!}
                        {!! $field('Tanggal lahir', Format::date($member['birthDate']), 'birthDate') !!}
                        {!! $field('Jenis kelamin', $member['gender'], 'gender') !!}
                        {!! $field('Pekerjaan', $member['occupation'], 'occupation') !!}
                        {!! $field('No. HP / WhatsApp', $member['contact'], 'contact') !!}
                        {!! $field('Alamat KTP', $member['ktpAddress'], 'ktpAddress', true) !!}
                        {!! $field('NPWP (opsional)', $member['npwp'], 'npwp', false, 'Tidak dicantumkan') !!}
                    </dl>
                </section>
            </div>
            <aside class="kt-member-detail-secondary">
                <section class="kt-member-detail-card">
                    {!! $heading('Keanggotaan') !!}
                    <dl class="kt-member-detail-fields">
                        {!! $field('Jabatan / peran', $member['position'], 'position') !!}
                        {!! $field('Kategori', $member['category'], 'category') !!}
                        {!! $field('Tanggal bergabung', Format::date($member['joined']), 'joined') !!}
                        {!! $field('Keterangan', $member['notes'], 'notes', true) !!}
                    </dl>
                    @if ($member['isChair'])
                        <p class="kt-member-detail-note">Ketua penanggung jawab akun dipertahankan dalam daftar organisasi.</p>
                    @endif
                </section>
            </aside>
        </div>
    </section>
@endsection
