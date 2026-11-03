@extends('layouts.public', [
    'title' => 'Pilar Sosial &mdash; SiDaya',
    'description' => 'Pilihan informasi mitra SiDaya: Lembaga Kesejahteraan Sosial (LKS).',
    'bodyClass' => 'detail-page pillars-page',
    'bodyId' => '',
    'navigation' => 'pillars',
    'showInfoDialog' => true,
    'entry' => 'pillars',
])

@section('content')
<main id="konten" tabindex="-1">
            <section class="ph pillar-hub-hero" aria-labelledby="page-title">
                <div class="container">
                    <nav class="breadcrumbs" aria-label="Jejak navigasi"><ol><li><a href="/index.html">Beranda</a></li><li aria-current="page">Pilar Sosial</li></ol></nav>
                    <div class="ph-layout">
                        <div class="ph-copy">
                            <p class="ph-kicker"><span>{!! $landing->text('pillars.page.01', 'Pilar sosial') !!}</span> Kota Banjarbaru</p>
                            <h1 id="page-title">{!! $landing->text('pillars.page.02', 'Pilar *pemberdayaan* sosial') !!}</h1>
                            <p class="ph-lead">{!! $landing->text('pillars.page.03', 'Kenali peran, layanan, dan contoh kegiatan LKS di Kota Banjarbaru.') !!}</p>
                            <div class="ph-actions"><a class="button ph-primary" href="#daftar-pilar">Lihat pilar LKS</a><a class="button ph-ghost" href="/kontak.html">Hubungi Dinsos</a></div>
                        </div>
                        <div class="pillar-match" data-pillar-match hidden>
                            <p class="pillar-match-title" id="pillar-match-title">{!! $landing->text('pillars.page.04', 'Saya ingin…') !!}</p>
                            <div class="pillar-match-options" role="group" aria-labelledby="pillar-match-title"><button type="button" class="pillar-match-option" data-match="lks" data-match-text="Lembaga Kesejahteraan Sosial menyelenggarakan pelayanan bagi anak, keluarga, lanjut usia, dan penyandang disabilitas." aria-pressed="false">Mendapat layanan sosial</button><button type="button" class="pillar-match-option" data-match="lks" data-match-text="Pendaftaran Lembaga Kesejahteraan Sosial dilakukan secara daring dengan 16 berkas." aria-pressed="false">Mendaftarkan lembaga</button></div>
                            <div class="pillar-match-result" role="status" aria-live="polite">
                                <p class="pillar-match-hint">{!! $landing->text('pillars.page.05', 'Pilih salah satu kebutuhan untuk melihat informasi yang sesuai.') !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pillar-directory-section" id="daftar-pilar" aria-label="Pilihan pilar sosial">
                <div class="container">
                    <ul class="pillar-panels" id="pillar-directory-grid">
                        <li class="pillar-panel-item" id="lks" data-pillar="lks" data-pillar-category="lembaga">
                            <a class="pillar-panel" href="/pilar/lks.html" aria-labelledby="lks-card-title lks-card-name">
                                <img {!! $landing->img('pillars.daftar-pilar.foto-1', '/assets/images/pilar-sosial/lks.jpg', 'Ilustrasi petugas dan relawan mencatat serta menyiapkan bantuan di lembaga sosial.') !!} class="pillar-panel-image" width="512" height="382" decoding="async">
                                <span class="pillar-panel-badge">{!! $landing->text('pillars.daftar-pilar.05', 'Informasi Mitra') !!}</span>
                                <span class="pillar-panel-number" aria-hidden="true">01</span>
                                <span class="pillar-panel-body">
                                    <span class="pillar-panel-title" id="lks-card-title">{!! $landing->text('pillars.daftar-pilar.06', 'LKS') !!}</span>
                                    <span class="pillar-panel-name" id="lks-card-name">{!! $landing->text('pillars.daftar-pilar.07', 'Lembaga Kesejahteraan Sosial') !!}</span>
                                    <span class="pillar-panel-more">
                                        <span class="pillar-panel-desc">{!! $landing->text('pillars.daftar-pilar.08', 'Organisasi penyelenggara pelayanan kesejahteraan sosial bagi individu, keluarga, kelompok, atau masyarakat yang membutuhkan dukungan sosial.') !!}</span>
                                        <span class="pillar-panel-facts" role="list"><span role="listitem">{!! $landing->text('pillars.daftar-pilar.09', 'Layanan di dalam maupun di luar lembaga') !!}</span><span role="listitem">{!! $landing->text('pillars.daftar-pilar.10', 'Pendaftaran daring dengan 16 berkas') !!}</span><span role="listitem">{!! $landing->text('pillars.daftar-pilar.11', 'Laporan semester ke Dinas Sosial') !!}</span></span>
                                        <span class="pillar-panel-link">Lihat informasi <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16M14 6l6 6-6 6"/></svg></span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <p class="pillar-directory-note">{!! $landing->text('pillars.daftar-pilar.33', 'Gambar pada halaman ini merupakan ilustrasi pendukung informasi pilar sosial.') !!}</p>
                </div>
            </section>
        </main>
@endsection
