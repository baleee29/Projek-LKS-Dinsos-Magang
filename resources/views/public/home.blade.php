@extends('layouts.public', [
    'title' => 'SiDaya — Pemberdayaan Sosial Kota Banjarbaru',
    'description' => 'SiDaya — informasi pemberdayaan sosial Dinas Sosial Kota Banjarbaru. Kenali LKS, layanan, dan kontak Dinas Sosial.',
    'bodyClass' => '',
    'bodyId' => 'beranda',
    'navigation' => 'home',
    'showInfoDialog' => true,
    'entry' => 'home',
])

@section('content')
<main id="konten" tabindex="-1">
            <!-- ----------------------------------------------------------------------
             1. Hero Section
             ---------------------------------------------------------------------- -->
            <section class="ph home-hero" aria-labelledby="hero-title">
                <div class="container">
                    <div class="ph-layout home-hero-layout">
                        <div class="ph-copy">
                            <p class="ph-kicker"><span>{!! $landing->text('home.hero.02', 'SiDaya') !!}</span> Dinas Sosial Kota Banjarbaru</p>
                            <h1 id="hero-title">{!! $landing->text('home.hero.03', 'Sistem Informasi *Pemberdayaan* dan Monitoring Sosial') !!}</h1>
                            <p class="ph-lead"><strong>{!! $landing->text('home.hero.04', 'SiDaya') !!}</strong> mendukung pengelolaan data, administrasi, pelaporan, dan dokumentasi pemberdayaan sosial bersama LKS dan masyarakat Kota Banjarbaru.</p>
                            <div class="ph-actions"><a class="button ph-primary" href="#profil">Kenali Dayasos</a><a class="button ph-ghost" href="/pilar/lks.html">Kenali LKS <svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></div>
                            <ul class="home-hero-info" aria-label="Informasi layanan">
                                <li><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><span><strong>{!! $landing->text('home.hero.05', 'Senin–Kamis') !!}</strong> 08.00–16.30 · <strong>{!! $landing->text('home.hero.06', 'Jumat') !!}</strong> 07.30–11.00 WITA</span></li>
                                <li><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{!! $landing->text('home.hero.07', 'Jl. Ketumbar, Kel. Komet, Banjarbaru Utara') !!}</span></li>
                            </ul>
                        </div>
                        <div class="home-hero-visual">
                            <figure class="home-hero-photo">
                                <img {!! $landing->img('home.hero.foto-1', '/assets/images/landing/foto-dinsos-584.webp', 'Gedung Dinas Sosial Kota Banjarbaru', '/assets/images/landing/foto-dinsos-584.webp 584w, /assets/images/landing/foto-dinsos-941.webp 941w', '(max-width: 640px) 100vw, 584px') !!} width="584" height="402" fetchpriority="high">
                            </figure>
                        </div>
                    </div>
                    <ul class="ph-paths" aria-label="Akses cepat"><li><a href="/pilar/pendaftaran-lks.html"><span>{!! $landing->text('home.hero.08', 'Daftarkan LKS') !!}</span><small>{!! $landing->text('home.hero.09', 'Formulir pendataan lembaga') !!}</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="/pilar/lks.html"><span>{!! $landing->text('home.hero.10', 'Kenali LKS') !!}</span><small>{!! $landing->text('home.hero.11', 'Fungsi dan layanan lembaga') !!}</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="/login"><span>{!! $landing->text('home.hero.12', 'Masuk SiDaya') !!}</span><small>{!! $landing->text('home.hero.13', 'Ruang kerja LKS dan Admin') !!}</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li><li><a href="/kontak.html"><span>{!! $landing->text('home.hero.14', 'Hubungi Dinsos') !!}</span><small>{!! $landing->text('home.hero.15', 'Telepon, email, dan alamat') !!}</small><svg class="ph-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li></ul>
                </div>
            </section>



            <!-- ----------------------------------------------------------------------
             2. Profil Section
             ---------------------------------------------------------------------- -->
            <section
                class="profile section"
                id="profil"
                aria-labelledby="profile-title"
            >
                <div class="container">
                    <div class="section-heading">
                        <h2 id="profile-title">{!! $landing->text('home.profil.01', 'Mengenal pemberdayaan sosial') !!}</h2>
                        <p>
                            {!! $landing->text('home.profil.02', 'Pemberdayaan sosial menguatkan kemampuan warga, keluarga, dan lembaga masyarakat. SiDaya membantu warga Banjarbaru serta pengurus lembaga menemukan informasi yang dibutuhkan.') !!}
                        </p>
                    </div>

                    <div class="home-roles" data-home-roles>
                        <p class="home-roles-label" id="home-roles-label">{!! $landing->text('home.profil.03', 'Saya adalah…') !!}</p>
                        <div class="home-role-tabs" role="tablist" aria-labelledby="home-roles-label" hidden><button type="button" class="home-role-tab" role="tab" id="role-tab-warga" aria-controls="role-panel-warga">Warga yang membutuhkan bantuan</button><button type="button" class="home-role-tab" role="tab" id="role-tab-lembaga" aria-controls="role-panel-lembaga">Pengurus lembaga</button></div>
                        <div class="home-role-panels"><div class="home-role-panel" role="tabpanel" id="role-panel-warga" aria-labelledby="role-tab-warga"><h3 class="home-role-heading">{!! $landing->text('home.profil.04', 'Warga yang membutuhkan bantuan') !!}</h3><ol class="home-role-steps"><li><span class="home-role-num" aria-hidden="true">01</span><h4>{!! $landing->text('home.profil.05', 'Kenali kebutuhan') !!}</h4><p>{!! $landing->text('home.profil.06', 'Catat bantuan yang dibutuhkan, misalnya pendampingan keluarga, lanjut usia, atau disabilitas.') !!}</p></li><li><span class="home-role-num" aria-hidden="true">02</span><h4>{!! $landing->text('home.profil.07', 'Cari lembaga yang sesuai') !!}</h4><p>{!! $landing->text('home.profil.08', 'Lembaga Kesejahteraan Sosial menyelenggarakan layanan sesuai sasaran dan programnya.') !!}</p><a href="/pilar/lks.html#akses-lks">Panduan mencari bantuan</a></li><li><span class="home-role-num" aria-hidden="true">03</span><h4>{!! $landing->text('home.profil.09', 'Hubungi petugas') !!}</h4><p>{!! $landing->text('home.profil.10', 'Pastikan persyaratan dan jadwal layanan kepada Dinas Sosial.') !!}</p><a href="#layanan">Kontak dan jam layanan</a></li></ol></div><div class="home-role-panel" role="tabpanel" id="role-panel-lembaga" aria-labelledby="role-tab-lembaga"><h3 class="home-role-heading">{!! $landing->text('home.profil.18', 'Pengurus lembaga') !!}</h3><ol class="home-role-steps"><li><span class="home-role-num" aria-hidden="true">01</span><h4>{!! $landing->text('home.profil.19', 'Pelajari layanan Dinsos') !!}</h4><p>{!! $landing->text('home.profil.20', 'Izin operasional LKS, pendaftaran layanan panti sosial, dan rekomendasi lainnya.') !!}</p><a href="https://dinsos.banjarbarukota.go.id/pelayanan-dinas-sosial/" target="_blank" rel="noopener noreferrer">Layanan Dinas Sosial<span class="sr-only"> (tab baru)</span></a></li><li><span class="home-role-num" aria-hidden="true">02</span><h4>{!! $landing->text('home.profil.21', 'Daftarkan LKS') !!}</h4><p>{!! $landing->text('home.profil.22', 'Isi formulir daring dan unggah berkas pendaftaran lembaga.') !!}</p><a href="/pilar/pendaftaran-lks.html">Pendaftaran LKS</a></li><li><span class="home-role-num" aria-hidden="true">03</span><h4>{!! $landing->text('home.profil.23', 'Kelola data di SiDaya') !!}</h4><p>{!! $landing->text('home.profil.24', 'Akun mitra dibuat Admin; laporan dan data lembaga dikelola di ruang kerja.') !!}</p><a href="/login">Masuk SiDaya</a></li></ol></div></div>
                    </div>
                </div>
            </section>

<!-- ----------------------------------------------------------------------
             3. Layanan Section
             ---------------------------------------------------------------------- -->
            <section
                class="services section"
                id="layanan"
                aria-labelledby="services-title"
            >
                <div class="container">
                    <div class="section-heading">
                        <h2 id="services-title">{!! $landing->text('home.layanan.01', 'Hubungi Dinas Sosial') !!}</h2>
                        <p>
                            {!! $landing->text('home.layanan.02', 'Sampaikan kebutuhan informasi Anda kepada Dinas Sosial Kota Banjarbaru. Tanyakan persyaratan dan jadwal terbaru sebelum mengakses layanan.') !!}
                        </p>
                    </div>

                    <div class="services-layout">
                        <div class="services-image location-map">
                            <iframe
                                class="location-map-frame"
                                src="https://www.google.com/maps/embed?origin=mfe&amp;pb=!1m4!2m1!1s-3.4412435,114.8267711!5e1!6i18!3m1!1sid!5m1!1sid"
                                title="Peta lokasi Dinas Sosial Kota Banjarbaru"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                tabindex="-1"
                                aria-hidden="true"
                            ></iframe>
                            <a
                                class="location-map-link"
                                href="{{ $landing->value('dinsos.maps', 'https://maps.app.goo.gl/HZuWwH7dPimqbc1V7?g_st=iw', 'url') }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Buka lokasi di Google Maps (tab baru)"
                            >
                                <span class="location-map-action">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    Buka titik di Google Maps
                                    <span aria-hidden="true">↗</span>
                                </span>
                            </a>
                        </div>

                        <div class="service-list">
                            <p class="home-open-status" data-open-status hidden></p>
                            <article class="service-item">
                                <span class="service-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3H3v4c0 8 6 14 14 14h4v-3l-5-2-2 2a13 13 0 0 1-8-8l2-2-2-5Z"/></svg></span>
                                <div>
                                    <h3>{!! $landing->text('home.layanan.03', 'Telepon dan email') !!}</h3>
                                    <p>
                                        <a href="tel:{{ $landing->phoneUri('dinsos.phone', '(0511) 4781749') }}">{{ $landing->value('dinsos.phone', '(0511) 4781749', 'phone') }}</a> <button type="button" class="home-copy" data-copy="{{ $landing->value('dinsos.phone', '(0511) 4781749', 'phone') }}" hidden>Salin</button><br />
                                        <a href="mailto:{{ $landing->value('dinsos.email', 'dinsos@banjarbarukota.go.id', 'email') }}">{{ $landing->value('dinsos.email', 'dinsos@banjarbarukota.go.id', 'email') }}</a> <button type="button" class="home-copy" data-copy="{{ $landing->value('dinsos.email', 'dinsos@banjarbarukota.go.id', 'email') }}" hidden>Salin</button>
                                    </p>
                                </div>
                            </article>

                            <article class="service-item">
                                <span class="service-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 21V4h14v17M3 21h18M9 21v-6h6v6M9 7h1m4 0h1m-6 4h1m4 0h1"/></svg></span>
                                <div>
                                    <h3>{!! $landing->text('home.layanan.04', 'Alamat kantor') !!}</h3>
                                    <p>
                                        {!! $landing->text('home.layanan.05', 'Jl. Ketumbar, Kelurahan Komet,
Kota Banjarbaru, Kalimantan Selatan 70711.') !!}
                                    </p>
                                </div>
                            </article>

                            <article class="service-item">
                                <span class="service-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                                <div>
                                    <h3>{!! $landing->text('home.layanan.06', 'Jam pelayanan') !!}</h3>
                                    <p>
                                        {!! $landing->text('home.layanan.07', 'Senin–Kamis: 08.00–16.30 WITA
Jumat: 07.30–11.00 WITA
Sabtu–Minggu: libur.') !!}
                                    </p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ----------------------------------------------------------------------
             4. Galeri Section
             ---------------------------------------------------------------------- -->
            <section class="gallery" id="galeri" aria-labelledby="gallery-title">
                <div class="container">
                    <div class="section-heading">
                        <h2 id="gallery-title">{!! $landing->text('home.galeri.03', 'Galeri pemberdayaan sosial') !!}</h2>
                        <p>{!! $landing->text('home.galeri.04', 'Ruang dokumentasi kegiatan bersama warga dan LKS. Foto kegiatan yang telah diverifikasi belum tersedia.') !!}</p>
                    </div>
                    <div class="gallery-grid">
                        <figure>
                            <img {!! $landing->img('home.galeri.foto-1', '/assets/images/landing/gallery-placeholder.svg', '') !!} width="640" height="340" loading="lazy" />
                            <figcaption>{!! $landing->text('home.galeri.05', 'Pembinaan LKS.') !!}</figcaption>
                        </figure>
                        <figure>
                            <img {!! $landing->img('home.galeri.foto-2', '/assets/images/landing/gallery-placeholder.svg', '') !!} width="640" height="340" loading="lazy" />
                            <figcaption>{!! $landing->text('home.galeri.06', 'Partisipasi masyarakat.') !!}</figcaption>
                        </figure>
                    </div>
                </div>
            </section>
        </main>
@endsection
