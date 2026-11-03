@extends('layouts.dashboard')

@section('title', 'Panduan Admin')

@php
    $steps = [
        ['01', 'Mulai dari pekerjaan yang masuk', 'Ringkasan menunjukkan pendaftaran, klien, kegiatan, dan laporan dari sumber aktif. Notifikasi saat ini memuat laporan LKS. Draf mitra yang belum diajukan tidak dihitung sebagai antrean pemeriksaan.', [['/admin/overview', 'Buka ringkasan'], ['/admin/notifications', 'Buka notifikasi']]],
        ['02', 'Periksa output LKS', 'Pendaftaran Mitra menerima formulir dan berkas dari halaman publik. Daftar LKS menyediakan profil dan SDM lembaga. Rekap penghuni menyajikan data klien binaan lembaga. Laporan triwulan hanya tersedia untuk LKS; gunakan tahun, triwulan, dan status untuk menemukan laporan.', [['/admin/organizations/lks', 'Daftar LKS'], ['/admin/organizations/lks', 'Data SDM LKS'], ['/admin/occupancy', 'Rekap penghuni'], ['/admin/reports/lks', 'Laporan LKS']]],
        ['03', 'Periksa keanggotaan PSM', 'Data Anggota menampilkan seluruh anggota terdaftar dari pendaftaran Ketua PSM yang langsung tercatat tanpa perlu persetujuan Admin.', [['/admin/members/psm', 'Data anggota PSM']]],
        ['04', 'Pantau kelompok Karang Taruna', 'Daftar kelompok dan data anggota menggunakan data keanggotaan mitra yang langsung tercatat. Pengajuan kegiatan terkirim tersedia pada Surat Masuk. Admin memeriksa pengajuan, lalu dapat membuat draf dan menerbitkan surat balasan secara terpisah.', [['/admin/organizations/karang-taruna', 'Data kelompok'], ['/admin/organizations/karang-taruna', 'Data anggota KT'], ['/admin/incoming-letters/karang-taruna', 'Surat masuk'], ['/admin/reply-letters/karang-taruna', 'Surat balasan']]],
        ['05', 'Reset kata sandi akun', 'Admin mereset kata sandi akun LKS, Karang Taruna, dan Ketua PSM lewat Buat Akun Mitra. Kata sandi Anggota PSM hanya dapat direset oleh Ketua PSM organisasinya. Pemilik akun menerima notifikasi, dan riwayat reset tersimpan di notifikasi Anda.', [['/admin/partner-accounts', 'Buat Akun Mitra']]],
    ];
@endphp

@section('content')
    <section class="admin-partner-view">
        <header class="admin-partner-heading"><div><small>SiDaya · Admin Dinas Sosial</small><h1 id="view-title" tabindex="-1">Panduan Admin</h1><p>Gunakan data dan alur pemeriksaan yang sudah tersedia untuk masing-masing mitra.</p></div></header>
        <section class="admin-partner-guide">
            @foreach ($steps as [$number, $title, $description, $links])
                <article class="admin-partner-guide-step">
                    <span aria-hidden="true">{{ $number }}</span>
                    <div>
                        <h2>{{ $title }}</h2>
                        <p>{{ $description }}</p>
                        <div class="admin-partner-info-actions">
                            @foreach ($links as [$href, $label])
                                <a class="admin-partner-button" href="{{ $href }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
        <section class="admin-partner-info-card">
            <h2>Memahami sumber dan rekap</h2>
            <p>Autentikasi memakai sesi Laravel. Profil, anggota, klien, dan laporan terintegrasi pada sistem SiDaya. Pendaftaran mitra, kegiatan, dan surat balasan tersimpan dan dapat diakses sesuai kewenangan.</p>
            <p>Rekap penghuni LKS saat ini terpisah dari penerima manfaat selama periode laporan. Penjumlahan penerima manfaat antarlembaga adalah jumlah layanan yang dilaporkan, bukan orang unik. Perkiraan peserta kegiatan bukan realisasi peserta.</p>
            <p>Perubahan draf revisi belum mengganti versi terakhir yang diajukan kepada Admin. Gunakan detail, lampiran, catatan pemeriksaan, serta riwayat yang tersedia untuk menelusuri keputusan.</p>
        </section>
    </section>
@endsection
