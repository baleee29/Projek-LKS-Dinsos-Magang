@extends('layouts.dashboard')

@section('title', 'Panduan Laporan Semester LKS')

@section('content')
    <x-dashboard.section-heading title="Panduan Laporan Semester LKS" description="Susun laporan, periksa berkas, lalu kirim langsung ke Admin tanpa persetujuan." :eyebrow="$eyebrow" />
    <div class="detail-layout">
        <div class="detail-primary">
            <section class="detail-card">
                <h2>Dua semester setiap tahun</h2>
                <p>Semester 1: Januari–Juni. Semester 2: Juli–Desember. Draf dapat disusun selama periode berjalan; pengiriman tersedia setelah semester berakhir.</p>
            </section>
            <section class="detail-card">
                <h2>Menyusun dan mengirim laporan</h2>
                <ol class="q-guide-steps">
                    <li>Pilih tahun dan semester; laporan yang sudah ada dibuka dengan ID yang sama.</li>
                    <li>Isi judul atau perihal dan unggah berkas laporan. Simpan draf untuk melanjutkan nanti.</li>
                    <li>Periksa pratinjau dan konfirmasikan pengiriman. Laporan langsung masuk ke Admin.</li>
                    <li>Laporan Terkirim dapat dibaca dan diunduh Admin, tanpa peninjauan, persetujuan atau permintaan revisi.</li>
                    <li>Jika perlu pembaruan, simpan draf perubahan lalu kirim ulang. Admin tetap melihat kiriman terakhir sampai pengiriman ulang berhasil.</li>
                </ol>
            </section>
        </div>
        <aside class="detail-secondary">
            <section class="detail-card">
                <h2>Alur pengiriman</h2>
                <p>Draf → Terkirim ke Admin.</p>
                <p>Draf dan perubahan yang belum dikirim hanya dapat dilihat LKS pemilik.</p>
            </section>
            <section class="detail-card">
                <h2>Template laporan</h2>
                <a href="/assets/documents/Template_Laporan_LKS.pdf" download="Template_Laporan_LKS.pdf">Unduh Template Laporan LKS</a>
            </section>
        </aside>
    </div>
@endsection
