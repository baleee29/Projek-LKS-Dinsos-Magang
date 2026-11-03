@extends('layouts.public', [
    'title' => 'Profil — SiDaya',
    'description' => 'Kenali Bidang Pemberdayaan Sosial Kota Banjarbaru, peran LKS, serta tujuan dan fungsi website SiDaya.',
    'bodyClass' => 'profile-page',
    'bodyId' => '',
    'navigation' => 'profile',
    'showInfoDialog' => false,
    'entry' => 'profile',
])

@section('content')
<main class="profile-main" id="konten" tabindex="-1">
            <svg class="profile-symbols" xmlns="http://www.w3.org/2000/svg" width="0" height="0" aria-hidden="true" focusable="false">
                <symbol id="profile-icon-people" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
                <symbol id="profile-icon-building" viewBox="0 0 24 24"><path d="M4 21V3h12v18M16 9h4v12M2 21h20M8 7h4M8 11h4M8 15h4M9 21v-3h2v3"/></symbol>
                <symbol id="profile-icon-heart" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></symbol>
                <symbol id="profile-icon-link" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7 .1l3-3a5 5 0 0 0-7.1-7.1L11 5M14 11a5 5 0 0 0-7-.1l-3 3a5 5 0 0 0 7.1 7.1L13 19"/></symbol>
                <symbol id="profile-icon-book" viewBox="0 0 24 24"><path d="M12 7v14M3 3h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5v16h-5a4 4 0 0 0-4 2 4 4 0 0 0-4-2H3V3Z"/></symbol>
                <symbol id="profile-icon-chart" viewBox="0 0 24 24"><path d="M3 3v18h18M8 16v-4M13 16V8M18 16v-6"/></symbol>
                <symbol id="profile-icon-star" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></symbol>
                <symbol id="profile-icon-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6ZM14 2v6h6M8 12h8M8 16h8"/></symbol>
                <symbol id="profile-icon-phone" viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 3.1 5.2 2 2 0 0 1 5.1 3h3a2 2 0 0 1 2 1.7c.1.9.4 1.9.7 2.8a2 2 0 0 1-.5 2.1L9 10.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.8.7a2 2 0 0 1 1.8 1.1Z"/></symbol>
                <symbol id="profile-icon-flag" viewBox="0 0 24 24"><path d="M4 22V3M4 3c4-3 8 3 12 0l4-1v12l-4 1c-4 3-8-3-12 0"/></symbol>
                <symbol id="profile-icon-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
                <symbol id="profile-icon-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
                <symbol id="profile-icon-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-6-6 6 6-6 6"/></symbol>
                <symbol id="profile-icon-external" viewBox="0 0 24 24"><path d="M14 3h7v7m0-7L10 14M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5"/></symbol>
            </svg>

            <section class="profile-hero ph" aria-labelledby="page-title">
                <div class="container">
                    <nav class="breadcrumbs" aria-label="Jejak navigasi"><ol><li><a href="/index.html">Beranda</a></li><li aria-current="page">Profil</li></ol></nav>
                    <div class="ph-layout">
                        <div class="ph-copy">
                            <p class="ph-kicker"><span>{!! $landing->text('profile.page.04', 'Profil') !!}</span> Dinas Sosial Kota Banjarbaru</p>
                            <h1 id="page-title">{!! $landing->text('profile.page.05', 'Mengenal *pemberdayaan sosial* dan SiDaya') !!}</h1>
                            <p class="ph-lead">{!! $landing->text('profile.page.06', 'Kenali peran Bidang Pemberdayaan Sosial, LKS, serta tujuan SiDaya dalam membantu akses informasi dan administrasi kegiatan sosial di Kota Banjarbaru.') !!}</p>
                            <div class="ph-actions"><a class="button ph-primary" href="#pilar">Kenali LKS</a><a class="button ph-ghost" href="#dayasos">Apa itu Dayasos <svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v16m-6-6 6 6 6-6"/></svg></a></div>
                            <p class="ph-caption">{!! $landing->text('profile.page.07', 'Informasi bidang mengacu pada publikasi Kementerian Sosial dan Dinas Sosial Kota Banjarbaru.') !!}</p>
                        </div>
                        <div class="ph-visual">
                            <figure class="ph-photo ph-photo-main"><img {!! $landing->img('profile.page.foto-1', '/assets/images/profil/pemberdayaan-sosial.jpg', 'Ilustrasi warga dan pendamping berdiskusi bersama dalam kegiatan pemberdayaan sosial.') !!} width="512" height="300" fetchpriority="high"></figure><figure class="ph-photo ph-photo-a"><img {!! $landing->img('profile.page.foto-2', '/assets/images/pilar-sosial/lks.jpg', '') !!} width="512" height="300" loading="lazy"></figure>
                        </div>
                    </div>
                    <nav aria-label="Di halaman ini" data-profile-toc><ol class="ph-paths"><li><a href="#dayasos"><span>Apa itu Dayasos</span><small>Peran dan bentuk dukungan bidang</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="#tugas"><span>Tugas dan fungsi</span><small>Lima pekerjaan Bidang Pemberdayaan Sosial</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="#pilar"><span>Lembaga Kesejahteraan Sosial</span><small>Peran LKS di Kota Banjarbaru</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="#tentang-sidaya"><span>Tentang SiDaya</span><small>Tujuan, fungsi, dan manfaat website</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li></ol></nav>
                </div>
            </section>

            <section class="profile-section" id="dayasos" aria-labelledby="dayasos-title">
                <div class="container">
                    <div class="profile-section-heading profile-heading-centered">
                        <p class="profile-eyebrow profile-label">{!! $landing->text('profile.dayasos.01', 'Pemberdayaan sosial Kota Banjarbaru') !!}</p>
                        <h2 id="dayasos-title">{!! $landing->text('profile.dayasos.02', 'Apa itu Dayasos dan bagaimana perannya?') !!}</h2>
                        <p>{!! $landing->text('profile.dayasos.03', 'Dayasos merupakan sebutan untuk pemberdayaan sosial. Bidang Pemberdayaan Sosial di Dinas Sosial Kota Banjarbaru mendukung penguatan kemampuan individu, lembaga, dan masyarakat melalui fasilitasi, koordinasi, pembinaan, serta evaluasi kegiatan.') !!}</p>
                    </div>
                    <div class="profile-layers" data-profile-layers>
                        <div class="profile-layers-diagram" aria-hidden="true">
                            <span class="profile-ring profile-ring-3" data-ring="3"><span class="profile-ring-label">{!! $landing->text('profile.dayasos.04', 'Masyarakat') !!}</span>
                                <span class="profile-ring profile-ring-2" data-ring="2"><span class="profile-ring-label">{!! $landing->text('profile.dayasos.05', 'Lembaga') !!}</span>
                                    <span class="profile-ring profile-ring-1" data-ring="1"><span class="profile-ring-label">{!! $landing->text('profile.dayasos.06', 'Individu') !!}</span></span>
                                </span>
                            </span>
                        </div>
                        <ol class="profile-layers-list">
                            <li><button type="button" class="profile-layer" data-layer="1" aria-pressed="false"><span class="profile-layer-step">{!! $landing->text('profile.dayasos.07', 'Lapis 1') !!}</span><h3>{!! $landing->text('profile.dayasos.08', 'Pemberdayaan individu') !!}</h3><p>{!! $landing->text('profile.dayasos.09', 'Penguatan kemampuan seseorang agar dapat berpartisipasi dalam kegiatan kesejahteraan sosial.') !!}</p></button></li>
                            <li><button type="button" class="profile-layer" data-layer="2" aria-pressed="false"><span class="profile-layer-step">{!! $landing->text('profile.dayasos.10', 'Lapis 2') !!}</span><h3>{!! $landing->text('profile.dayasos.11', 'Kelembagaan sosial') !!}</h3><p>{!! $landing->text('profile.dayasos.12', 'Pengembangan kapasitas organisasi dan pelaku sosial dalam menjalankan kegiatan bagi masyarakat.') !!}</p></button></li>
                            <li><button type="button" class="profile-layer" data-layer="3" aria-pressed="false"><span class="profile-layer-step">{!! $landing->text('profile.dayasos.13', 'Lapis 3') !!}</span><h3>{!! $landing->text('profile.dayasos.14', 'Partisipasi masyarakat') !!}</h3><p>{!! $landing->text('profile.dayasos.15', 'Penguatan peran warga, kepedulian, dan kerja sama untuk mendukung kegiatan sosial di lingkungannya.') !!}</p></button></li>
                        </ol>
                    </div>
                    <div class="profile-support" data-profile-tabs aria-labelledby="support-title">
                        <div class="profile-support-heading">
                            <div><h3 id="support-title">{!! $landing->text('profile.dayasos.16', 'Bentuk dukungan Dayasos') !!}</h3><p>{!! $landing->text('profile.dayasos.17', 'Empat peran yang berjalan sebagai satu siklus. Pilih langkah untuk melihat penjelasannya.') !!}</p></div>
                        </div>
                        <div class="profile-tabs profile-support-tabs" aria-label="Bentuk dukungan Dayasos" hidden>
                            <button id="tab-fasilitasi" type="button" data-profile-tab="panel-fasilitasi" aria-controls="panel-fasilitasi">Fasilitasi</button>
                            <button id="tab-koordinasi" type="button" data-profile-tab="panel-koordinasi" aria-controls="panel-koordinasi">Koordinasi</button>
                            <button id="tab-pembinaan" type="button" data-profile-tab="panel-pembinaan" aria-controls="panel-pembinaan">Pembinaan</button>
                            <button id="tab-evaluasi" type="button" data-profile-tab="panel-evaluasi" aria-controls="panel-evaluasi">Pemantauan dan evaluasi</button>
                        </div>
                        <div class="profile-support-panels">
                            <div class="profile-panel profile-support-panel" id="panel-fasilitasi" aria-labelledby="fasilitasi-title"><div><h4 id="fasilitasi-title">{!! $landing->text('profile.dayasos.18', 'Fasilitasi kegiatan dan akses informasi') !!}</h4><p>{!! $landing->text('profile.dayasos.19', 'Membantu pelaksanaan kegiatan pemberdayaan dan akses informasi yang dibutuhkan masyarakat maupun lembaga sosial.') !!}</p></div></div>
                            <div class="profile-panel profile-support-panel" id="panel-koordinasi" aria-labelledby="koordinasi-title"><div><h4 id="koordinasi-title">{!! $landing->text('profile.dayasos.20', 'Koordinasi dengan instansi dan pelaku sosial') !!}</h4><p>{!! $landing->text('profile.dayasos.21', 'Menghubungkan kegiatan dengan instansi terkait, lembaga, dan pelaku sosial agar pelaksanaan program dapat berjalan bersama.') !!}</p></div></div>
                            <div class="profile-panel profile-support-panel" id="panel-pembinaan" aria-labelledby="pembinaan-title"><div><h4 id="pembinaan-title">{!! $landing->text('profile.dayasos.22', 'Pembinaan kemampuan pelaku sosial') !!}</h4><p>{!! $landing->text('profile.dayasos.23', 'Mendukung pengembangan kemampuan pelaku sosial dan kelembagaan masyarakat dalam menjalankan kegiatan kesejahteraan sosial.') !!}</p></div></div>
                            <div class="profile-panel profile-support-panel" id="panel-evaluasi" aria-labelledby="evaluasi-title"><div><h4 id="evaluasi-title">{!! $landing->text('profile.dayasos.24', 'Pemantauan, evaluasi, dan pelaporan') !!}</h4><p>{!! $landing->text('profile.dayasos.25', 'Memantau pelaksanaan kegiatan, menyusun evaluasi, serta menyiapkan pelaporan sebagai bahan tindak lanjut.') !!}</p></div></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="profile-section profile-section-soft" id="tugas" aria-labelledby="duties-title">
                <div class="container">
                    <div class="profile-section-heading profile-heading-wide">
                        <p class="profile-eyebrow">{!! $landing->text('profile.tugas.01', 'Tugas dan fungsi') !!}</p>
                        <h2 id="duties-title">{!! $landing->text('profile.tugas.02', 'Apa yang dikerjakan Bidang Pemberdayaan Sosial?') !!}</h2>
                        <p>{!! $landing->text('profile.tugas.03', 'Kegiatan bidang mencakup penguatan pelaku sosial, kelembagaan masyarakat, serta sumber daya untuk mendukung pemberdayaan.') !!}</p>
                    </div>
                    <div class="profile-duties-list">
                        <details class="profile-duty" name="profile-duties" open><summary><span class="profile-duty-num" aria-hidden="true">01</span><span class="profile-duty-head"><span class="profile-duty-title">{!! $landing->text('profile.tugas.04', 'Penguatan pelaku sosial') !!}</span><span class="profile-duty-context">{!! $landing->text('profile.tugas.05', 'Pekerja sosial dan relawan') !!}</span></span><svg class="profile-icon profile-duty-chevron" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></summary><p>{!! $landing->text('profile.tugas.06', 'Mendukung kapasitas pekerja sosial, tenaga kesejahteraan sosial, relawan, dan kelembagaan masyarakat.') !!}</p></details>
                        <details class="profile-duty" name="profile-duties"><summary><span class="profile-duty-num" aria-hidden="true">02</span><span class="profile-duty-head"><span class="profile-duty-title">{!! $landing->text('profile.tugas.07', 'Fasilitasi dan koordinasi') !!}</span><span class="profile-duty-context">{!! $landing->text('profile.tugas.08', 'Kerja sama lintas instansi') !!}</span></span><svg class="profile-icon profile-duty-chevron" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></summary><p>{!! $landing->text('profile.tugas.09', 'Menghubungkan kegiatan pemberdayaan dengan instansi terkait dan organisasi sosial.') !!}</p></details>
                        <details class="profile-duty" name="profile-duties"><summary><span class="profile-duty-num" aria-hidden="true">03</span><span class="profile-duty-head"><span class="profile-duty-title">{!! $landing->text('profile.tugas.10', 'Pembinaan dan pendampingan') !!}</span><span class="profile-duty-context">{!! $landing->text('profile.tugas.11', 'Kapasitas masyarakat dan lembaga') !!}</span></span><svg class="profile-icon profile-duty-chevron" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></summary><p>{!! $landing->text('profile.tugas.12', 'Mendukung pengembangan kemampuan masyarakat dan lembaga dalam menjalankan kegiatan sosial.') !!}</p></details>
                        <details class="profile-duty" name="profile-duties"><summary><span class="profile-duty-num" aria-hidden="true">04</span><span class="profile-duty-head"><span class="profile-duty-title">{!! $landing->text('profile.tugas.13', 'Pemantauan dan evaluasi') !!}</span><span class="profile-duty-context">{!! $landing->text('profile.tugas.14', 'Pelaksanaan kegiatan') !!}</span></span><svg class="profile-icon profile-duty-chevron" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></summary><p>{!! $landing->text('profile.tugas.15', 'Memantau kegiatan serta menyusun evaluasi dan pelaporan pelaksanaannya.') !!}</p></details>
                        <details class="profile-duty" name="profile-duties"><summary><span class="profile-duty-num" aria-hidden="true">05</span><span class="profile-duty-head"><span class="profile-duty-title">{!! $landing->text('profile.tugas.16', 'Kepedulian dan sumber daya sosial') !!}</span><span class="profile-duty-context">{!! $landing->text('profile.tugas.17', 'Kesetiakawanan dan sumber daya sosial') !!}</span></span><svg class="profile-icon profile-duty-chevron" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></summary><p>{!! $landing->text('profile.tugas.18', 'Mendukung kesetiakawanan sosial, nilai kepahlawanan, serta pengelolaan sumber dana sosial sesuai tugas bidang.') !!}</p></details>
                    </div>
                </div>
            </section>

            <section class="profile-section" id="pilar" aria-labelledby="pillars-title">
                <div class="container">
                    <div class="profile-pillar-heading">
                        <div class="profile-section-heading"><p class="profile-eyebrow profile-label">{!! $landing->text('profile.pilar.01', 'Mitra kerja') !!}</p><h2 id="pillars-title">{!! $landing->text('profile.pilar.02', 'Bersama LKS Kota Banjarbaru') !!}</h2><p>{!! $landing->text('profile.pilar.03', 'SiDaya memperkenalkan LKS beserta perannya dalam kegiatan kesejahteraan masyarakat.') !!}</p></div>
                    </div>
                    <div class="profile-pillars-grid" style="grid-template-columns: minmax(0, 1fr);">
                        <article class="profile-pillar-card profile-pillar-lks">
                            <div class="profile-card-top"><span class="profile-pillar-label">{!! $landing->text('profile.pilar.04', 'Kelembagaan sosial') !!}</span><span class="profile-icon-box"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-building"/></svg></span></div>
                            <h3>{!! $landing->text('profile.pilar.05', 'LKS') !!}</h3><p class="profile-pillar-subtitle">{!! $landing->text('profile.pilar.06', 'Lembaga Kesejahteraan Sosial') !!}</p><p>{!! $landing->text('profile.pilar.07', 'Lembaga yang menyelenggarakan kegiatan kesejahteraan sosial bagi masyarakat.') !!}</p>
                            <ul class="profile-pillar-points"><li><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-check"/></svg>Pelayanan kesejahteraan sosial</li><li><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-check"/></svg>Pendampingan klien sesuai bidang layanan</li></ul>
                            <a class="profile-pillar-link" href="/pilar/lks.html">Kenali LKS <svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></a>
                        </article>
                        
                    </div>
                </div>
            </section>

            <section class="profile-section profile-section-soft" id="tentang-sidaya" aria-labelledby="sidaya-title" data-profile-tabs>
                <div class="container">
                    <div class="profile-section-heading profile-heading-centered">
                        <p class="profile-eyebrow profile-label">{!! $landing->text('profile.tentang-sidaya.01', 'Tentang website') !!}</p>
                        <h2 id="sidaya-title">{!! $landing->text('profile.tentang-sidaya.02', 'Mengenal SiDaya lebih dekat') !!}</h2>
                        <p>{!! $landing->text('profile.tentang-sidaya.03', 'Pelajari tujuan pengembangan website, fungsi yang disiapkan, dan manfaat bagi penggunanya.') !!}</p>
                    </div>
                    <div class="profile-tabs profile-sidaya-tabs" aria-label="Tentang SiDaya" hidden>
                        <button id="tab-tujuan" type="button" data-profile-tab="panel-tujuan" aria-controls="panel-tujuan"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-flag"/></svg>Tujuan website</button>
                        <button id="tab-fungsi" type="button" data-profile-tab="panel-fungsi" aria-controls="panel-fungsi"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-grid"/></svg>Fungsi yang dikembangkan</button>
                        <button id="tab-manfaat" type="button" data-profile-tab="panel-manfaat" aria-controls="panel-manfaat"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-people"/></svg>Manfaat bagi pengguna</button>
                    </div>
                    <div class="profile-panels">
                        <div class="profile-panel" id="panel-tujuan" aria-labelledby="purpose-title">
                            <div class="profile-purpose-layout">
                                <div class="profile-panel-copy"><p class="profile-eyebrow">{!! $landing->text('profile.tentang-sidaya.04', 'Tujuan pengembangan') !!}</p><h3 id="purpose-title">{!! $landing->text('profile.tentang-sidaya.05', 'Informasi dan administrasi yang lebih mudah diakses') !!}</h3><p>{!! $landing->text('profile.tentang-sidaya.06', 'SiDaya dirancang sebagai website informasi dan pengelolaan kegiatan pemberdayaan sosial di Kota Banjarbaru. Masyarakat dapat mengenal LKS, memperoleh panduan pendaftaran, serta menemukan informasi layanan dan kontak Dinas Sosial.') !!}</p><p>{!! $landing->text('profile.tentang-sidaya.07', 'Bagi lembaga, website ini dikembangkan untuk mendukung pendataan dan pelaporan kegiatan sesuai peran pengguna. Tujuannya adalah membantu administrasi pemberdayaan sosial menjadi lebih tertata.') !!}</p></div>
                                <aside class="profile-purpose-note" aria-labelledby="access-title"><h4 id="access-title">{!! $landing->text('profile.tentang-sidaya.08', 'Informasi yang dapat ditemukan') !!}</h4><ul><li>{!! $landing->text('profile.tentang-sidaya.09', 'Peran LKS.') !!}</li><li>{!! $landing->text('profile.tentang-sidaya.10', 'Panduan dan berkas pendaftaran.') !!}</li><li>{!! $landing->text('profile.tentang-sidaya.11', 'Kontak untuk bertanya tentang layanan.') !!}</li></ul></aside>
                            </div>
                        </div>
                        <div class="profile-panel" id="panel-fungsi" aria-labelledby="functions-title">
                            <h3 id="functions-title">{!! $landing->text('profile.tentang-sidaya.12', 'Fungsi yang dikembangkan') !!}</h3>
                            <div class="profile-functions-grid">
                                <article><h4>{!! $landing->text('profile.tentang-sidaya.13', 'Pusat informasi') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.14', 'Informasi pemberdayaan sosial dan pengenalan LKS dalam satu website.') !!}</p></article>
                                <article><h4>{!! $landing->text('profile.tentang-sidaya.15', 'Panduan pendaftaran') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.16', 'Formulir dan penjelasan berkas yang perlu disiapkan oleh calon pendaftar.') !!}</p></article>
                                <article><h4>{!! $landing->text('profile.tentang-sidaya.17', 'Dukungan administrasi') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.18', 'Ruang pendataan dan pelaporan kegiatan yang dikembangkan sesuai peran pengguna.') !!}</p></article>
                                <article><h4>{!! $landing->text('profile.tentang-sidaya.19', 'Akses kontak layanan') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.20', 'Informasi jalur komunikasi untuk menghubungi Dinas Sosial Kota Banjarbaru.') !!}</p></article>
                            </div>
                        </div>
                        <div class="profile-panel" id="panel-manfaat" aria-labelledby="benefits-title">
                            <h3 id="benefits-title">{!! $landing->text('profile.tentang-sidaya.21', 'Manfaat bagi pengguna') !!}</h3>
                            <div class="profile-benefits-grid">
                                <article><div><h4>{!! $landing->text('profile.tentang-sidaya.22', 'Masyarakat') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.23', 'Mengenal LKS dan menemukan informasi layanan serta kontak yang relevan.') !!}</p></div></article>
                                <article><div><h4>{!! $landing->text('profile.tentang-sidaya.24', 'Lembaga sosial') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.25', 'Memahami panduan pendaftaran dan kebutuhan administrasi lembaga.') !!}</p></div></article>
                                
                                <article><div><h4>{!! $landing->text('profile.tentang-sidaya.28', 'Pengelola kegiatan') !!}</h4><p>{!! $landing->text('profile.tentang-sidaya.29', 'Memiliki rancangan ruang kerja untuk membantu penataan data dan laporan kegiatan.') !!}</p></div></article>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="profile-references" aria-labelledby="references-title">
                <div class="container">
                    <div class="profile-reference-box">
                        <div><p class="profile-eyebrow">{!! $landing->text('profile.references.01', 'Sumber informasi') !!}</p><h2 id="references-title">{!! $landing->text('profile.references.02', 'Referensi pemberdayaan sosial') !!}</h2><p>{!! $landing->text('profile.references.03', 'Penjelasan Dayasos mengacu pada publikasi Kemensos dan informasi Dinas Sosial Kota Banjarbaru. Uraian SiDaya menjelaskan tujuan pengembangan website.') !!}</p></div>
                        <div class="profile-reference-links">
                            <a class="profile-reference-link" href="https://kemensos.go.id/berita-terkini/direktorat-jenderal-pemberdayaan-sosial/fokus-dayasos-peningkatan-daya-individu-lembaga-dan-masyarakat" target="_blank" rel="noopener noreferrer"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-external"/></svg>Kementerian Sosial<span class="profile-sr-only"> (terbuka di tab baru)</span></a>
                            <a class="profile-reference-link" href="https://dinsos.banjarbarukota.go.id/bid-pemberdayaan-sosial/" target="_blank" rel="noopener noreferrer"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-external"/></svg>Dinsos Banjarbaru<span class="profile-sr-only"> (terbuka di tab baru)</span></a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="profile-section profile-cta" aria-labelledby="profile-cta-title">
                <div class="container">
                    <div class="profile-cta-content">
                        <h2 id="profile-cta-title">{!! $landing->text('profile.profile-cta.01', 'Kenali LKS di sekitar kita') !!}</h2><p>{!! $landing->text('profile.profile-cta.02', 'Temukan fungsi, layanan, dan panduan pendaftaran melalui halaman LKS.') !!}</p>
                        <div class="profile-cta-actions"><a class="button button-primary" href="/pilar/lks.html">Kenali LKS <svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-arrow"/></svg></a><a class="button profile-button-secondary" href="/kontak.html"><svg class="profile-icon" aria-hidden="true"><use href="#profile-icon-phone"/></svg>Hubungi Dinsos</a></div>
                    </div>
                </div>
            </section>
        </main>
@endsection
