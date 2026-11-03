@extends('layouts.dashboard')

@section('title', 'Panduan Admin')

@php
    $steps = [
        ['01', 'Mulai dari pekerjaan yang masuk', 'Ringkasan menunjukkan pendaftaran, klien, surat, dan laporan LKS. Draf lembaga yang belum dikirim tidak dihitung sebagai pekerjaan.', [['/admin/overview', 'Buka ringkasan'], ['/admin/notifications', 'Buka notifikasi']]],
        ['02', 'Periksa pendaftaran dan buat akun', 'Pendaftaran LKS menerima formulir dan berkas LKS dari halaman publik. Setelah pendaftaran diterima, buat akun lembaga secara terpisah, lalu kirim data login melalui WhatsApp.', [['/admin/registrations/lks', 'Pendaftaran LKS'], ['/admin/partner-accounts', 'Buat Akun LKS']]],
        ['03', 'Pantau data lembaga dan klien', 'Data LKS menyediakan profil, legalitas, dan SDM lembaga. Rekap penghuni menyajikan data klien binaan lembaga.', [['/admin/organizations/lks', 'Data LKS'], ['/admin/occupancy', 'Rekap penghuni']]],
        ['04', 'Baca laporan dan surat', 'Laporan semester yang dikirim LKS langsung masuk tanpa proses persetujuan. Gunakan tahun, semester, dan status untuk menemukan laporan. Surat Masuk & Keluar memuat surat dari LKS dan surat resmi dari Dinas Sosial.', [['/admin/reports/lks', 'Laporan LKS'], ['/admin/letters/lks', 'Surat LKS']]],
        ['05', 'Reset kata sandi akun', 'Admin mereset kata sandi akun LKS lewat Buat Akun LKS. Pemilik akun menerima notifikasi, dan riwayat reset tersimpan di notifikasi Anda.', [['/admin/partner-accounts', 'Buat Akun LKS']]],
    ];
@endphp

@section('content')
    <section class="admin-partner-view">
        <header class="admin-partner-heading"><div><small>SiDaya · Admin Dinas Sosial</small><h1 id="view-title" tabindex="-1">Panduan Admin</h1><p>Gunakan data dan alur pemeriksaan yang sudah tersedia untuk Lembaga Kesejahteraan Sosial.</p></div></header>
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
            <p>Autentikasi memakai sesi Laravel. Profil, SDM, klien, dan laporan terintegrasi pada sistem SiDaya. Pendaftaran dan surat tersimpan dan dapat diakses sesuai kewenangan.</p>
            <p>Rekap penghuni LKS terpisah dari penerima manfaat selama periode laporan. Penjumlahan penerima manfaat antarlembaga adalah jumlah layanan yang dilaporkan, bukan orang unik.</p>
            <p>Perubahan draf yang belum dikirim tidak mengganti versi terakhir yang diterima Admin. Gunakan detail, lampiran, dan riwayat yang tersedia untuk menelusuri laporan.</p>
        </section>
    </section>
@endsection
