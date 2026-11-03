@extends('layouts.public', [
    'title' => 'LKS &mdash; SiDaya',
    'description' => 'Kenali Lembaga Kesejahteraan Sosial: fungsi, contoh layanan, alur dan format pendaftaran, serta panduan pelaporan lembaga di Kota Banjarbaru.',
    'bodyClass' => 'detail-page mitra-page lks-page',
    'bodyId' => '',
    'navigation' => 'lks',
    'showInfoDialog' => true,
    'entry' => 'lks',
])

@section('content')
<main id="konten" tabindex="-1">
            <section class="lks-hero lks-hero-v2" aria-labelledby="page-title">
                <div class="container">
                    <nav class="breadcrumbs" aria-label="Jejak navigasi"><ol><li><a href="/index.html">Beranda</a></li><li aria-current="page">LKS</li></ol></nav>
                    <div class="lks-hero-layout">
                        <div class="lks-hero-copy">
                            <p class="lks-hero-kicker"><span>{!! $landing->text('lks.page.04', 'Lembaga sosial') !!}</span> Kota Banjarbaru</p>
                            <h1 id="page-title">{!! $landing->text('lks.page.05', 'Lembaga *Kesejahteraan* Sosial') !!}</h1>
                            <p class="lks-lead">{!! $landing->text('lks.page.06', 'Organisasi yang menyelenggarakan pelayanan kesejahteraan sosial bagi individu, keluarga, kelompok, atau masyarakat yang membutuhkan dukungan sosial.') !!}</p>
                            <div class="lks-actions">
                                <a class="button lks-hero-primary" href="/pilar/pendaftaran-lks.html"><svg class="lks-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="3"/><path d="M3 20v-2a6 6 0 0 1 10-4m2 3 2 2 4-5"/></svg>Daftar LKS</a>
                                <a class="button lks-hero-ghost" href="#fungsi-lks">Kenali Fungsi LKS <svg class="lks-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v16m-6-6 6 6 6-6"/></svg></a>
                            </div>
                            <p class="lks-demo-caption">{!! $landing->text('lks.page.07', 'Formulir ditujukan bagi pengelola atau penanggung jawab lembaga untuk menyampaikan informasi kelembagaan bagi keperluan pendataan.') !!}</p>
                            <noscript><p class="lks-demo-caption"><a class="inline-link" href="https://dinsos.banjarbarukota.go.id/kontak-4/">Hubungi Dinsos untuk informasi pendaftaran.</a></p></noscript>
                        </div>
                        <div class="lks-hero-visual">
                            <figure class="lks-hero-photo lks-hero-photo-main"><img {!! $landing->img('lks.page.foto-1', '/assets/images/lks/social-visit.jpg', 'Ilustrasi pendamping sosial berbincang dengan keluarga lanjut usia saat kunjungan rumah.') !!} width="512" height="279" fetchpriority="high"></figure>
                            <figure class="lks-hero-photo lks-hero-photo-a"><img {!! $landing->img('lks.page.foto-2', '/assets/images/lks/child-support.jpg', '') !!} width="512" height="279" loading="lazy"></figure>
                            <figure class="lks-hero-photo lks-hero-photo-b"><img {!! $landing->img('lks.page.foto-3', '/assets/images/lks/elderly-care.jpg', '') !!} width="512" height="279" loading="lazy"></figure>
                        </div>
                    </div>
                    <ul class="lks-hero-paths" aria-label="Mulai dari kebutuhan Anda">
                        <li><a href="#akses-lks"><span>{!! $landing->text('lks.page.08', 'Saya mencari layanan') !!}</span><small>{!! $landing->text('lks.page.09', 'Kenali kebutuhan dan hubungi petugas') !!}</small><svg class="lks-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li>
                        <li><a href="#pendaftaran"><span>{!! $landing->text('lks.page.10', 'Saya mengelola lembaga') !!}</span><small>{!! $landing->text('lks.page.11', 'Alur pendaftaran dan 16 berkas') !!}</small><svg class="lks-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li>
                        <li><a href="#pelaporan-lks"><span>{!! $landing->text('lks.page.12', 'Kewajiban pelaporan') !!}</span><small>{!! $landing->text('lks.page.13', 'Laporan semester ke Dinas Sosial') !!}</small><svg class="lks-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a></li>
                    </ul>
                </div>
            </section>

            <nav class="lks-section-nav" aria-label="Bagian informasi LKS" data-section-nav>
                <div class="container">
                    <div class="lks-section-links" data-section-links><a href="#tentang-lks">Tentang LKS</a><a href="#fungsi-lks">Fungsi</a><a href="#layanan-lks">Contoh layanan</a><a href="#akses-lks">Mencari bantuan</a><a href="#pendaftaran">Pendaftaran</a><a href="#format-pendaftaran">Formulir</a><a href="#panduan-pendaftaran">Panduan</a><a href="#pelaporan-lks">Pelaporan</a><a href="#tanya-lks">Tanya jawab</a></div>
                    <a class="button button-primary lks-nav-register section-nav-action" href="/pilar/pendaftaran-lks.html">Daftar LKS</a>
                </div>
            </nav>

            <section class="lks-section" id="tentang-lks" aria-labelledby="lks-about-title">
                <div class="container lks-about-layout">
                    <div class="lks-about-copy">
                        <p class="eyebrow">{!! $landing->text('lks.tentang-lks.02', 'Tentang lembaga') !!}</p>
                        <h2 id="lks-about-title">{!! $landing->text('lks.tentang-lks.03', 'Apa itu LKS?') !!}</h2>
                        <p>{!! $landing->text('lks.tentang-lks.04', 'Lembaga Kesejahteraan Sosial (LKS) merupakan organisasi yang berperan dalam penyelenggaraan pelayanan kesejahteraan sosial bagi individu, keluarga, kelompok, atau masyarakat yang membutuhkan dukungan sosial.') !!}</p>
                        <p>{!! $landing->text('lks.tentang-lks.05', 'Melalui layanan, pendampingan, dan pengembangan kapasitas kelembagaan, LKS menjadi salah satu unsur masyarakat yang mendukung penyelenggaraan kesejahteraan sosial di Kota Banjarbaru.') !!}</p>
                        <aside class="lks-about-aside" aria-labelledby="lks-scope-title">
                            <h3 id="lks-scope-title">{!! $landing->text('lks.tentang-lks.06', 'LKS tidak selalu berupa panti') !!}</h3>
                            <p>{!! $landing->text('lks.tentang-lks.07', 'Ada layanan dengan tempat tinggal, dan ada pendampingan tanpa tinggal di lembaga. Pilih informasi lembaga yang sesuai dengan kebutuhan layanan Anda.') !!}</p>
                        </aside>
                        <p class="lks-source">Pengantar mengacu pada <a href="https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf">Permensos Nomor 5 Tahun 2024, Pasal 1 dan 2</a>.</p>
                    </div>
                    <!-- Gambar dari referensi Stitch merupakan ilustrasi, bukan dokumentasi kegiatan. -->
                    <figure class="lks-about-image">
                        <img {!! $landing->img('lks.tentang-lks.foto-1', '/assets/images/lks/social-visit.jpg', 'Ilustrasi pendamping sosial berbincang dengan keluarga lanjut usia saat kunjungan rumah.') !!} width="512" height="279" loading="lazy" decoding="async">
                        <figcaption>{!! $landing->text('lks.tentang-lks.08', 'Ilustrasi pendampingan sosial') !!}</figcaption>
                    </figure>
                </div>
            </section>

            <section class="lks-section lks-section-soft" id="fungsi-lks" aria-labelledby="lks-function-title">
                <div class="container">
                    <div class="lks-section-intro"><p class="eyebrow">{!! $landing->text('lks.fungsi-lks.01', 'Peran dalam masyarakat') !!}</p><h2 id="lks-function-title">{!! $landing->text('lks.fungsi-lks.02', 'Apa fungsi LKS?') !!}</h2><p>{!! $landing->text('lks.fungsi-lks.03', 'Tiga peran utama berikut membantu menjelaskan kontribusi LKS dalam pelayanan sosial.') !!}</p></div>
                    <ol class="lks-function-list">
                        <li><span class="lks-function-number" aria-hidden="true">01</span><div class="lks-function-copy"><h3>{!! $landing->text('lks.fungsi-lks.04', 'Mencegah dan menangani masalah sosial') !!}</h3><p>{!! $landing->text('lks.fungsi-lks.05', 'Mengenali kebutuhan serta mendukung warga dalam mengatasi kesulitan sosial.') !!}</p></div></li>
                        <li><span class="lks-function-number" aria-hidden="true">02</span><div class="lks-function-copy"><h3>{!! $landing->text('lks.fungsi-lks.06', 'Memberikan layanan kepada warga') !!}</h3><p>{!! $landing->text('lks.fungsi-lks.07', 'Menyelenggarakan pelayanan sesuai sasaran dan program lembaga.') !!}</p></div></li>
                        <li><span class="lks-function-number" aria-hidden="true">03</span><div class="lks-function-copy"><h3>{!! $landing->text('lks.fungsi-lks.08', 'Mengurangi risiko masalah sosial') !!}</h3><p>{!! $landing->text('lks.fungsi-lks.09', 'Membantu mengantisipasi risiko agar permasalahan tidak semakin berat.') !!}</p></div></li>
                    </ol>
                </div>
            </section>

            <section class="lks-section" id="layanan-lks" aria-labelledby="lks-service-title">
                <div class="container">
                    <div class="lks-section-intro"><p class="eyebrow">{!! $landing->text('lks.layanan-lks.04', 'Gambaran layanan') !!}</p><h2 id="lks-service-title">{!! $landing->text('lks.layanan-lks.05', 'Layanan mengikuti kebutuhan warga') !!}</h2><p>{!! $landing->text('lks.layanan-lks.06', 'Berikut merupakan contoh bentuk layanan yang dapat dijumpai pada lembaga kesejahteraan sosial. Contoh ini bukan daftar program yang pasti tersedia pada setiap LKS di Banjarbaru.') !!}</p></div>
                    <ul class="lks-service-list" data-lks-services>
                        <li>
                            <figure class="lks-service-image"><img {!! $landing->img('lks.layanan-lks.foto-1', '/assets/images/lks/child-support.jpg', 'Ilustrasi pendamping membacakan buku dan menemani anak bermain di ruang belajar.') !!} width="512" height="279" loading="lazy" decoding="async"><figcaption>{!! $landing->text('lks.layanan-lks.07', 'Ilustrasi') !!}</figcaption></figure>
                            <div class="lks-service-copy"><p class="lks-service-label">{!! $landing->text('lks.layanan-lks.08', 'Anak dan keluarga') !!}</p><h3>{!! $landing->text('lks.layanan-lks.09', 'Dukungan pengasuhan dan keseharian') !!}</h3><p>{!! $landing->text('lks.layanan-lks.10', 'Contohnya pendampingan keluarga, dukungan kebutuhan dasar, atau kegiatan belajar sesuai program dan kemampuan lembaga.') !!}</p></div>
                        </li>
                        <li>
                            <figure class="lks-service-image"><img {!! $landing->img('lks.layanan-lks.foto-2', '/assets/images/lks/elderly-care.jpg', 'Ilustrasi pendamping membantu pemeriksaan tekanan darah seorang perempuan lanjut usia.') !!} width="512" height="279" loading="lazy" decoding="async"><figcaption>{!! $landing->text('lks.layanan-lks.11', 'Ilustrasi') !!}</figcaption></figure>
                            <div class="lks-service-copy"><p class="lks-service-label">{!! $landing->text('lks.layanan-lks.12', 'Lanjut usia') !!}</p><h3>{!! $landing->text('lks.layanan-lks.13', 'Pendampingan dalam aktivitas sehari-hari') !!}</h3><p>{!! $landing->text('lks.layanan-lks.14', 'Contohnya dukungan kegiatan sosial dan pendampingan dalam menjalani aktivitas keseharian sesuai kebutuhan penerima layanan.') !!}</p></div>
                        </li>
                        <li>
                            <figure class="lks-service-image"><img {!! $landing->img('lks.layanan-lks.foto-3', '/assets/images/lks/inclusive-workshop.jpg', 'Ilustrasi peserta, termasuk pengguna kursi roda, belajar membuat kerajinan bersama pendamping.') !!} width="512" height="279" loading="lazy" decoding="async"><figcaption>{!! $landing->text('lks.layanan-lks.15', 'Ilustrasi') !!}</figcaption></figure>
                            <div class="lks-service-copy"><p class="lks-service-label">{!! $landing->text('lks.layanan-lks.16', 'Penyandang disabilitas') !!}</p><h3>{!! $landing->text('lks.layanan-lks.17', 'Dukungan akses dan kemandirian') !!}</h3><p>{!! $landing->text('lks.layanan-lks.18', 'Contohnya pendampingan untuk mengakses informasi layanan, mengembangkan keterampilan, dan berpartisipasi dalam lingkungan sosial.') !!}</p></div>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="lks-section lks-section-soft" id="akses-lks" aria-labelledby="lks-access-title">
                <div class="container lks-access-layout">
                    <div class="lks-section-intro"><p class="eyebrow">{!! $landing->text('lks.akses-lks.01', 'Untuk masyarakat') !!}</p><h2 id="lks-access-title">{!! $landing->text('lks.akses-lks.02', 'Mulai dari Informasi yang Tepat') !!}</h2><p>{!! $landing->text('lks.akses-lks.03', 'Masyarakat yang membutuhkan pelayanan dapat memulai dengan mengenali kebutuhan, memeriksa kesesuaian layanan, dan meminta informasi kepada lembaga atau Dinas Sosial.') !!}</p></div>
                    <ol class="lks-access-steps">
                        <li><div><h3>{!! $landing->text('lks.akses-lks.04', 'Kenali kebutuhan bantuan') !!}</h3><p>{!! $landing->text('lks.akses-lks.05', 'Catat kebutuhan yang ingin dibicarakan, misalnya pendampingan keluarga atau dukungan aktivitas sehari-hari.') !!}</p></div></li>
                        <li><div><h3>{!! $landing->text('lks.akses-lks.06', 'Periksa kesesuaian layanan') !!}</h3><p>{!! $landing->text('lks.akses-lks.07', 'Tanyakan sasaran program, wilayah layanan, serta informasi penerimaan kepada lembaga yang dituju.') !!}</p></div></li>
                        <li><div><h3>{!! $landing->text('lks.akses-lks.08', 'Hubungi petugas untuk arahan') !!}</h3><p>{!! $landing->text('lks.akses-lks.09', 'Jika belum mengetahui lembaga yang sesuai, mintalah informasi kepada Dinas Sosial Kota Banjarbaru.') !!}</p></div></li>
                    </ol>
                    <div class="lks-access-actions">
                        <a class="button lks-button-outline" href="https://dinsos.banjarbarukota.go.id/kontak-4/">Lihat kontak resmi Dinas Sosial</a>
                        <a class="button button-primary" href="/pilar/pendaftaran-lks.html">Daftarkan lembaga</a>
                    </div>
                </div>
            </section>

            <section class="lks-section lks-registration" id="pendaftaran" aria-labelledby="lks-registration-title">
                <div class="container">
                    <div class="lks-section-intro lks-registration-intro">
                        <p class="eyebrow lks-registration-label"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4H5v17h14V4h-4M9 3h6v4H9zM8 13l3 3 5-6"/></svg>1.1 Alur pendaftaran LKS</p>
                        <h2 id="lks-registration-title">{!! $landing->text('lks.pendaftaran.01', 'Dari pengisian formulir sampai keputusan verifikasi') !!}</h2>
                        <p>{!! $landing->text('lks.pendaftaran.02', 'LKS yang wilayah kerjanya di Kota Banjarbaru mendaftar kepada Wali Kota melalui Dinas Sosial. Di SiDaya, pendaftaran diajukan secara daring melalui halaman Pendaftaran LKS, lalu diverifikasi oleh Admin Dinas Sosial. Pendaftaran tidak dipungut biaya.') !!}</p>
                        <aside class="lks-registration-note">
                            <svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/></svg>
                            <p><strong>{!! $landing->text('lks.pendaftaran.03', 'Catatan:') !!}</strong> Status <em>Disetujui</em> di SiDaya memperbarui data lembaga pada sistem. Status ini tidak otomatis menjadi tanda pendaftaran resmi, nomor induk LKS, atau akun dashboard. Ketiganya diproses terpisah oleh Dinas Sosial.</p>
                        </aside>
                    </div>
                    <div class="lks-registration-layout">
                        <ol class="lks-registration-steps" data-lks-steps>
                            <li><span class="lks-registration-number" aria-hidden="true">01</span><div><h3>{!! $landing->text('lks.pendaftaran.04', 'Pelajari persyaratan dan unduh format') !!}</h3><p>{!! $landing->text('lks.pendaftaran.05', 'Buka halaman Pendaftaran LKS. Baca ketentuan persyaratan, lalu unduh Format Berkas Pendaftaran dan format surat pernyataan yang tersedia di panel samping.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">02</span><div><h3>{!! $landing->text('lks.pendaftaran.06', 'Lengkapi berkas di luar sistem') !!}</h3><p>{!! $landing->text('lks.pendaftaran.07', 'Isi formulir pendaftaran, tanda tangani surat pernyataan bermeterai, dan siapkan salinan dokumen legalitas, struktur organisasi, KTP pengurus, serta proposal.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">03</span><div><h3>{!! $landing->text('lks.pendaftaran.08', 'Isi formulir daring dan unggah 16 berkas') !!}</h3><p>{!! $landing->text('lks.pendaftaran.09', 'Isi identitas pengisi data, rekening, fokus pelayanan, dan lima kelompok pilihan pelayanan. Unggah 16 berkas wajib satu per satu sesuai nomornya.') !!}</p><a class="button button-primary" href="/pilar/pendaftaran-lks.html">Buka Pendaftaran LKS</a></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">04</span><div><h3>{!! $landing->text('lks.pendaftaran.10', 'Periksa ringkasan dan ajukan') !!}</h3><p>Periksa ringkasan isian dan daftar dokumen sebelum mengirim. Setelah server menyimpan data, pendaftaran berstatus <strong>{!! $landing->text('lks.pendaftaran.11', 'Menunggu Verifikasi') !!}</strong>. Simpan nomor bukti dan kode akses yang muncul.</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">05</span><div><h3>{!! $landing->text('lks.pendaftaran.12', 'Verifikasi oleh Admin Dinas Sosial') !!}</h3><p>{!! $landing->text('lks.pendaftaran.13', 'Admin memeriksa data dan berkas, lalu memberi status beserta catatan. Pantau hasilnya melalui fitur bukti pendaftaran memakai nomor bukti dan kode akses.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">06</span><div><h3>{!! $landing->text('lks.pendaftaran.14', 'Tindak lanjut hasil verifikasi') !!}</h3><p>{!! $landing->text('lks.pendaftaran.15', 'Jika diminta perbaikan, kirim perbaikan dengan nomor yang sama. Jika disetujui, Dinas Sosial menindaklanjuti penerbitan tanda pendaftaran dan koordinasi nomor induk LKS dengan Kementerian Sosial.') !!}</p><a class="button lks-button-outline" href="/kontak.html">Hubungi Dinsos</a></div></li>
                        </ol>
                        <aside class="lks-registration-documents" aria-label="Status dan ketentuan pendaftaran">
                            <details class="lks-document-details" open>
                                <summary>{!! $landing->text('lks.pendaftaran.16', 'Arti status pendaftaran') !!}</summary>
                                <div class="lks-document-details-content">
                                    <ol>
                                        <li><strong>{!! $landing->text('lks.pendaftaran.17', 'Menunggu Verifikasi:') !!}</strong> Pendaftaran sudah tersimpan dan masuk antrean Admin.</li>
                                        <li><strong>{!! $landing->text('lks.pendaftaran.18', 'Ditinjau:') !!}</strong> Admin sedang memeriksa data dan berkas.</li>
                                        <li><strong>{!! $landing->text('lks.pendaftaran.19', 'Perlu Perbaikan:') !!}</strong> Ada data atau berkas yang harus dilengkapi. Baca catatan Admin, lalu kirim perbaikan.</li>
                                        <li><strong>{!! $landing->text('lks.pendaftaran.20', 'Ditolak:') !!}</strong> Permohonan tidak dapat diterima. Alasannya tercantum pada catatan Admin.</li>
                                        <li><strong>{!! $landing->text('lks.pendaftaran.21', 'Disetujui:') !!}</strong> Data dan berkas dinyatakan sesuai. Data lembaga diperbarui pada sistem.</li>
                                    </ol>
                                    <p class="lks-document-confirmation">{!! $landing->text('lks.pendaftaran.22', 'Permohonan dapat ditolak jika persyaratan belum lengkap atau lembaga tidak menyelenggarakan kegiatan di bidang kesejahteraan sosial.') !!}</p>
                                </div>
                            </details>
                            <details class="lks-document-details">
                                <summary>{!! $landing->text('lks.pendaftaran.23', 'Ketentuan tanda pendaftaran') !!}</summary>
                                <div class="lks-document-details-content">
                                    <ol>
                                        <li>{!! $landing->text('lks.pendaftaran.24', 'Tanda pendaftaran diterbitkan paling lama 14 hari kerja sejak persyaratan dinyatakan lengkap.') !!}</li>
                                        <li>{!! $landing->text('lks.pendaftaran.25', 'Selain tanda pendaftaran, LKS memperoleh nomor induk yang dikeluarkan oleh Menteri Sosial.') !!}</li>
                                        <li>{!! $landing->text('lks.pendaftaran.26', 'Tanda pendaftaran berlaku 3 tahun. LKS Berbadan Hukum dapat mengajukan perpanjangan paling lambat 3 bulan sebelum masa berlaku berakhir.') !!}</li>
                                        <li>{!! $landing->text('lks.pendaftaran.27', 'LKS Tidak Berbadan Hukum diberi waktu 3 tahun untuk menjadi LKS Berbadan Hukum. Jika belum, tanda pendaftarannya tidak dapat diperpanjang.') !!}</li>
                                    </ol>
                                </div>
                            </details>
                        </aside>
                    </div>
                    <p class="lks-source">Mengacu pada <a href="https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf">Permensos Nomor 5 Tahun 2024</a>, Pasal 5&ndash;17 dan Lampiran huruf A.</p>
                </div>
            </section>

            <section class="lks-section lks-section-soft" id="format-pendaftaran" aria-labelledby="lks-form-title">
                <div class="container">
                    <div class="lks-section-intro lks-registration-intro">
                        <p class="eyebrow lks-registration-label"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M8 12h8m-8 4h8"/></svg>1.2 Format formulir pendaftaran LKS</p>
                        <h2 id="lks-form-title">{!! $landing->text('lks.format-pendaftaran.01', 'Tujuh bagian formulir pendaftaran') !!}</h2>
                        <p>{!! $landing->text('lks.format-pendaftaran.02', 'Formulir Pendaftaran LKS mengikuti format Kementerian Sosial. Isi formulir ini di luar sistem, lalu unggah sebagai berkas nomor 16 *Formulir Pendaftaran LKS*. Sebagian isiannya juga diminta kembali pada formulir daring SiDaya.') !!}</p>
                    </div>
                    <div class="lks-registration-layout">
                        <ol class="lks-registration-steps">
                            <li><span class="lks-registration-number" aria-hidden="true">0</span><div><h3>{!! $landing->text('lks.format-pendaftaran.03', 'Identitas pengisi data') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.04', 'Nama dan domisili LKS, nama serta jabatan pengisi, nomor telepon, dan alamat e-mail. Di SiDaya, isian ini sesuai dengan kartu Identitas Pengisi Data.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">I</span><div><h3>{!! $landing->text('lks.format-pendaftaran.05', 'Data umum LKS') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.06', 'Nama dan singkatan LKS, alamat sekretariat, kontak dan media sosial, tempat dan tanggal pendirian, nomor akta, serta nama, alamat, dan telepon ketua, sekretaris, dan bendahara.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">II</span><div><h3>{!! $landing->text('lks.format-pendaftaran.07', 'Identitas/jati diri LKS') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.08', 'Visi, misi, tujuan, status badan hukum, sifat pelayanan (operasional atau bukan), posisi (pusat, cabang, atau lokal), dan lingkup kerja.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">III</span><div><h3>{!! $landing->text('lks.format-pendaftaran.09', 'Legalitas LKS') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.10', 'Anggaran dasar dan rumah tangga, akta pendirian, pengesahan atau surat keterangan terdaftar, keterangan domisili, tanda pendaftaran yang dimiliki, NPWP, dan rekening bank atas nama LKS.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">IV</span><div><h3>{!! $landing->text('lks.format-pendaftaran.11', 'Program dan kegiatan') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.12', 'Sasaran pelayanan, permasalahan sosial yang ditangani, jenis pelayanan, kegiatan di luar bidang kesejahteraan sosial, sistem pelayanan, dan lokasi pelayanan. Bagian ini sejalan dengan pilihan pelayanan pada formulir daring.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">V</span><div><h3>{!! $landing->text('lks.format-pendaftaran.13', 'Sumber daya LKS') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.14', 'Prasarana dan sarana, jumlah penerima layanan menurut jenis pelayanan, organ organisasi, tenaga pelaksana termasuk Pekerja Sosial, serta sumber dana.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">VI</span><div><h3>{!! $landing->text('lks.format-pendaftaran.15', 'Jejaring kerja') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.16', 'Kerja sama dengan lembaga sosial, perguruan tinggi, dunia usaha, serta pemerintah dan pemerintah daerah, baik dari dalam maupun luar negeri.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">VII</span><div><h3>{!! $landing->text('lks.format-pendaftaran.17', 'Kelengkapan dokumen pendukung') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.18', 'Daftar periksa dokumen yang dimiliki. Dokumen yang ditandai *Ada* diunggah sebagai berkas tersendiri pada formulir daring.') !!}</p></div></li>
                        </ol>
                        <aside class="lks-registration-documents" aria-label="Dokumen acuan formulir pendaftaran">
                            <div class="lks-document-card">
                                <div class="lks-document-heading">
                                    <span class="lks-document-icon" aria-hidden="true"><svg class="lks-icon" viewBox="0 0 24 24"><path d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M8 12h8m-8 4h8"/></svg></span>
                                    <div><h3>{!! $landing->text('lks.format-pendaftaran.19', 'Format Berkas Pendaftaran') !!}</h3><p>{!! $landing->text('lks.format-pendaftaran.20', 'PDF · 6 halaman') !!}</p></div>
                                    <span class="lks-document-badge">{!! $landing->text('lks.format-pendaftaran.21', 'Berkas no. 16') !!}</span>
                                </div>
                                <p class="lks-document-filename">{!! $landing->text('lks.format-pendaftaran.22', 'Format-Pendaftaran-LKS.pdf') !!}</p>
                                <p>{!! $landing->text('lks.format-pendaftaran.23', 'Cetak atau salin formulir ini, isi seluruh bagian, lalu unggah hasilnya pada formulir daring.') !!}</p>
                                <div class="lks-document-actions">
                                    <a class="button button-primary" href="/assets/documents/Format-Pendaftaran-LKS.pdf" download="Format-Pendaftaran-LKS.pdf"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg>Unduh PDF</a>
                                    <a class="button lks-button-outline" href="/assets/documents/Format-Pendaftaran-LKS.pdf" target="_blank" rel="noopener" aria-label="Lihat Format Berkas Pendaftaran, membuka tab baru">Lihat Format</a>
                                </div>
                            </div>
                            <details class="lks-document-details">
                                <summary>{!! $landing->text('lks.format-pendaftaran.24', '16 berkas wajib pada formulir daring') !!}</summary>
                                <div class="lks-document-details-content">
                                    <ol>
                                        <li>{!! $landing->text('lks.format-pendaftaran.25', 'Surat permohonan pendaftaran LKS') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.26', 'Anggaran dasar dan anggaran rumah tangga') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.27', 'Akta pendirian') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.28', 'Surat keterangan domisili dari lurah/kepala desa') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.29', 'NPWP badan hukum LKS') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.30', 'Pengesahan pendirian badan hukum oleh kementerian di bidang hukum') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.31', 'Struktur organisasi LKS') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.32', 'Data umum: alamat, telepon, website, dan media sosial LKS') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.33', 'KTP dan nomor telepon pengurus') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.34', 'Surat keterangan bebas narkoba bagi pengurus') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.35', 'Surat pernyataan tidak dalam sengketa kepengurusan atau perkara di pengadilan') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.36', 'Surat pernyataan kesanggupan melaporkan kegiatan') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.37', 'Surat pernyataan sumber pendanaan tidak melawan hukum') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.38', 'Surat pernyataan persetujuan tetangga') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.39', 'Proposal: program dan kegiatan, modal kerja, rekening, SDM, serta sarana dan prasarana') !!}</li>
                                        <li>{!! $landing->text('lks.format-pendaftaran.40', 'Formulir pendaftaran LKS') !!}</li>
                                    </ol>
                                    <p class="lks-document-confirmation">{!! $landing->text('lks.format-pendaftaran.41', 'Format surat pernyataan, struktur organisasi, dan data umum dapat diunduh pada panel samping halaman Pendaftaran LKS.') !!}</p>
                                </div>
                            </details>
                        </aside>
                    </div>
                    <p class="lks-source">Mengacu pada <a href="https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf">Permensos Nomor 5 Tahun 2024</a>, Pasal 7 dan Lampiran huruf B, D&ndash;G.</p>
                </div>
            </section>

            <section class="lks-section" id="panduan-pendaftaran" aria-labelledby="lks-guide-title">
                <div class="container">
                    <div class="lks-section-intro lks-registration-intro">
                        <p class="eyebrow lks-registration-label"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14m0 3h.01"/></svg>1.3 Panduan pendaftaran LKS</p>
                        <h2 id="lks-guide-title">{!! $landing->text('lks.panduan-pendaftaran.01', 'Petunjuk pengisian formulir') !!}</h2>
                        <p>{!! $landing->text('lks.panduan-pendaftaran.02', 'Ikuti petunjuk berikut agar pendaftaran dapat diverifikasi tanpa perbaikan berulang.') !!}</p>
                    </div>
                    <div class="lks-registration-layout">
                        <ol class="lks-registration-steps">
                            <li><span class="lks-registration-number" aria-hidden="true">01</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.03', 'Gunakan data sesuai dokumen resmi') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.04', 'Tulis nama lengkap LKS sesuai akta pendirian. Nama pengurus, tanggal pendirian, dan nomor akta juga harus sesuai akta.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">02</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.05', 'Lengkapi semua isian wajib') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.06', 'Isian bertanda bintang wajib diisi. Nomor WhatsApp diawali 08, 62, atau +62. Nomor izin operasional lama boleh dikosongkan jika belum ada.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">03</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.07', 'Pilih semua pilihan yang sesuai') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.08', 'Pada prioritas, jenis, sasaran, kegiatan, dan sifat pelayanan, pilih sedikitnya satu. Jawaban boleh lebih dari satu sesuai kondisi LKS.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">04</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.09', 'Unggah setiap berkas di tempatnya') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.10', 'Gunakan PDF, JPG, atau PNG. Batasnya 2 MB per berkas, kecuali surat permohonan, proposal, dan formulir pendaftaran yang boleh sampai 5 MB. Pastikan tulisan dan stempel terbaca.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">05</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.11', 'Periksa ulang sebelum mengirim') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.12', 'Gunakan ringkasan pendaftaran untuk memastikan semua isian benar dan 16 berkas sudah terpilih. Pengiriman berhasil hanya jika layar konfirmasi muncul.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">06</span><div><h3>{!! $landing->text('lks.panduan-pendaftaran.13', 'Simpan bukti pendaftaran') !!}</h3><p>{!! $landing->text('lks.panduan-pendaftaran.14', 'Catat nomor bukti dan kode akses. Keduanya dipakai untuk membuka status dari perangkat lain dan untuk mengirim perbaikan.') !!}</p></div></li>
                        </ol>
                        <aside class="lks-registration-documents" aria-label="Ketentuan tambahan pendaftaran">
                            <details class="lks-document-details" open>
                                <summary>{!! $landing->text('lks.panduan-pendaftaran.15', 'Jika diminta perbaikan') !!}</summary>
                                <div class="lks-document-details-content">
                                    <ol>
                                        <li>Perbaikan hanya dapat dikirim saat status <strong>{!! $landing->text('lks.panduan-pendaftaran.16', 'Perlu Perbaikan') !!}</strong>.</li>
                                        <li>{!! $landing->text('lks.panduan-pendaftaran.17', 'Baca catatan Admin, perbaiki isian atau ganti berkas yang diminta.') !!}</li>
                                        <li>{!! $landing->text('lks.panduan-pendaftaran.18', 'Kirim ulang dengan nomor pendaftaran yang sama. Status kembali menjadi Menunggu Verifikasi.') !!}</li>
                                    </ol>
                                </div>
                            </details>
                            <details class="lks-document-details">
                                <summary>{!! $landing->text('lks.panduan-pendaftaran.19', 'LKS Tidak Berbadan Hukum') !!}</summary>
                                <div class="lks-document-details-content">
                                    <p>{!! $landing->text('lks.panduan-pendaftaran.20', 'Formulir daring SiDaya disusun untuk persyaratan LKS Berbadan Hukum. Persyaratan LKS Tidak Berbadan Hukum sedikit berbeda:') !!}</p>
                                    <ol>
                                        <li>{!! $landing->text('lks.panduan-pendaftaran.21', 'Akta pendirian dikeluarkan oleh notaris.') !!}</li>
                                        <li>{!! $landing->text('lks.panduan-pendaftaran.22', 'Pengesahan badan hukum diganti surat keterangan terdaftar dari kementerian di bidang dalam negeri.') !!}</li>
                                        <li>{!! $landing->text('lks.panduan-pendaftaran.23', 'Pendaftaran hanya untuk 1 wilayah kabupaten/kota.') !!}</li>
                                    </ol>
                                    <p class="lks-document-confirmation">{!! $landing->text('lks.panduan-pendaftaran.24', 'Konsultasikan dengan Dinas Sosial sebelum mengisi formulir agar berkas yang diunggah sesuai.') !!}</p>
                                </div>
                            </details>
                        </aside>
                    </div>
                    <p class="lks-source">Mengacu pada <a href="https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf">Permensos Nomor 5 Tahun 2024</a>, Pasal 8 dan Lampiran huruf C.</p>
                </div>
            </section>

            <section class="lks-section lks-section-soft" id="pelaporan-lks" aria-labelledby="lks-report-title">
                <div class="container">
                    <div class="lks-section-intro lks-registration-intro">
                        <p class="eyebrow lks-registration-label"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V10m6 10V4m6 16v-7m4 7H2"/></svg>1.4 Panduan pelaporan LKS</p>
                        <h2 id="lks-report-title">{!! $landing->text('lks.pelaporan-lks.01', 'Melaporkan kegiatan lembaga') !!}</h2>
                        <p>{!! $landing->text('lks.pelaporan-lks.02', 'Setiap LKS wajib melaporkan pelaksanaan kegiatannya paling sedikit 1 kali dalam setahun. Laporan membuktikan bahwa LKS masih aktif. Di SiDaya, LKS yang sudah memiliki akun mengirim laporan semester melalui dashboard LKS.') !!}</p>
                        <aside class="lks-registration-note">
                            <svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/></svg>
                            <p><strong>{!! $landing->text('lks.pelaporan-lks.03', 'Catatan:') !!}</strong> LKS yang tidak melapor secara berkala dapat dikenakan sanksi administratif, mulai dari peringatan tertulis sampai pencabutan tanda pendaftaran.</p>
                        </aside>
                    </div>
                    <div class="lks-registration-layout">
                        <ol class="lks-registration-steps">
                            <li><span class="lks-registration-number" aria-hidden="true">01</span><div><h3>{!! $landing->text('lks.pelaporan-lks.04', 'Masuk ke dashboard LKS') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.05', 'Gunakan akun LKS dari Dinas Sosial, lalu buka menu Laporan Semester. Akun tidak dibuat otomatis saat pendaftaran disetujui.') !!}</p><a class="button lks-button-outline" href="/login">Masuk SiDaya</a></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">02</span><div><h3>{!! $landing->text('lks.pelaporan-lks.06', 'Pilih tahun dan semester') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.07', 'Semester 1 mencakup Januari–Juni, semester 2 mencakup Juli–Desember. Draf dapat disusun selama semester berjalan. Pengiriman tersedia setelah semester berakhir.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">03</span><div><h3>{!! $landing->text('lks.pelaporan-lks.08', 'Susun laporan sesuai format') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.09', 'Gunakan Template Laporan LKS. Isi bagian yang tetap dan yang berubah dibandingkan data saat pendaftaran, lalu jelaskan setiap perubahan.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">04</span><div><h3>{!! $landing->text('lks.pelaporan-lks.10', 'Isi perihal dan unggah berkas') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.11', 'Tulis judul atau perihal laporan dan unggah berkasnya. Simpan sebagai draf jika belum selesai. Draf hanya terlihat oleh LKS pemilik.') !!}</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">05</span><div><h3>{!! $landing->text('lks.pelaporan-lks.12', 'Kirim ke Admin Dinas Sosial') !!}</h3><p>Periksa pratinjau dan konfirmasikan pengiriman. Laporan berstatus <strong>{!! $landing->text('lks.pelaporan-lks.13', 'Terkirim') !!}</strong> dan langsung dapat dibaca serta diunduh Admin, tanpa tahap persetujuan.</p></div></li>
                            <li><span class="lks-registration-number" aria-hidden="true">06</span><div><h3>{!! $landing->text('lks.pelaporan-lks.14', 'Perbarui bila perlu') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.15', 'Simpan draf perubahan lalu kirim ulang dengan laporan yang sama. Admin tetap melihat kiriman terakhir sampai pengiriman ulang berhasil.') !!}</p></div></li>
                        </ol>
                        <aside class="lks-registration-documents" aria-label="Dokumen acuan pelaporan">
                            <div class="lks-document-card">
                                <div class="lks-document-heading">
                                    <span class="lks-document-icon" aria-hidden="true"><svg class="lks-icon" viewBox="0 0 24 24"><path d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M8 12h8m-8 4h8"/></svg></span>
                                    <div><h3>{!! $landing->text('lks.pelaporan-lks.16', 'Format Laporan LKS') !!}</h3><p>{!! $landing->text('lks.pelaporan-lks.17', 'PDF · 6 halaman') !!}</p></div>
                                    <span class="lks-document-badge">{!! $landing->text('lks.pelaporan-lks.18', 'Dokumen acuan') !!}</span>
                                </div>
                                <p class="lks-document-filename">{!! $landing->text('lks.pelaporan-lks.19', 'Format_Laporan_LKS.pdf') !!}</p>
                                <p>{!! $landing->text('lks.pelaporan-lks.20', 'Format laporan Kementerian Sosial dengan pilihan *Tetap* atau *Berubah* pada setiap isian.') !!}</p>
                                <div class="lks-document-actions">
                                    <a class="button button-primary" href="/assets/documents/Format_Laporan_LKS.pdf" download="Format_Laporan_LKS.pdf"><svg class="lks-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg>Unduh PDF</a>
                                    <a class="button lks-button-outline" href="/assets/documents/Format_Laporan_LKS.pdf" target="_blank" rel="noopener" aria-label="Lihat Format Laporan LKS, membuka tab baru">Lihat Format</a>
                                </div>
                            </div>
                            <details class="lks-document-details">
                                <summary>{!! $landing->text('lks.pelaporan-lks.21', 'Isi laporan yang wajib ada') !!}</summary>
                                <div class="lks-document-details-content">
                                    <ol>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.22', 'Penyelenggaraan kegiatan:') !!}</strong> Program, sasaran, dan jenis pelayanan yang dijalankan.</li>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.23', 'Keuangan:') !!}</strong> Total penerimaan dan pengeluaran selama setahun beserta peruntukannya.</li>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.24', 'Donor:') !!}</strong> Daftar donor dan jumlah donasi dari dalam dan luar negeri.</li>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.25', 'Sumber daya manusia:') !!}</strong> Organ organisasi serta tenaga pelaksana yang masuk, keluar, dan jumlah akhirnya.</li>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.26', 'Aset:') !!}</strong> Kekayaan lembaga yang dimiliki.</li>
                                        <li><strong>{!! $landing->text('lks.pelaporan-lks.27', 'Sarana dan prasarana:') !!}</strong> Perubahan bangunan, sarana pelayanan, dan jumlah penerima layanan.</li>
                                    </ol>
                                    <p class="lks-document-confirmation">{!! $landing->text('lks.pelaporan-lks.28', 'Lampirkan daftar organ LKS, dokumen perubahan legalitas, rekap penerima layanan, rekap staf, dan neraca keuangan tahun buku terakhir. Rekap klien dan staf dapat diunduh dari dashboard LKS.') !!}</p>
                                </div>
                            </details>
                        </aside>
                    </div>
                    <p class="lks-source">Mengacu pada <a href="https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf">Permensos Nomor 5 Tahun 2024</a>, Pasal 24, 27, 59 dan Lampiran huruf I&ndash;J.</p>
                </div>
            </section>

            <section class="lks-section" id="tanya-lks" aria-labelledby="lks-faq-title">
                <div class="container lks-faq-layout">
                    <div class="lks-section-intro"><p class="eyebrow">{!! $landing->text('lks.tanya-lks.01', 'Tanya jawab') !!}</p><h2 id="lks-faq-title">{!! $landing->text('lks.tanya-lks.02', 'Hal yang sering ditanyakan') !!}</h2></div>
                    <div class="lks-faq-list">
                        <details><summary>{!! $landing->text('lks.tanya-lks.03', 'Apakah semua LKS merupakan panti?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.04', 'Tidak. Pelayanan LKS dapat dilakukan dengan tempat tinggal maupun tanpa tinggal di lembaga, sesuai bentuk layanan yang diselenggarakan.') !!}</p></details>
                        <details><summary>{!! $landing->text('lks.tanya-lks.05', 'Apakah setiap LKS memiliki layanan yang sama?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.06', 'Tidak. Jenis layanan, sasaran penerima manfaat, dan wilayah layanan dapat berbeda pada setiap lembaga. Hubungi lembaga terkait untuk memastikan informasi pelayanannya.') !!}</p></details>
                        <details><summary>{!! $landing->text('lks.tanya-lks.07', 'Siapa yang dapat memakai tombol Daftar LKS?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.08', 'Formulir ditujukan bagi pengelola atau penanggung jawab lembaga yang ingin menyampaikan informasi kelembagaan untuk keperluan pendataan sesuai mekanisme yang berlaku.') !!}</p></details>
                        <details><summary>{!! $landing->text('lks.tanya-lks.09', 'Bagaimana proses verifikasi pendaftaran LKS?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.10', 'Pendaftaran yang terkirim berstatus Menunggu Verifikasi. Admin Dinas Sosial lalu memberi status Ditinjau, Perlu Perbaikan, Ditolak, atau Disetujui beserta catatan. Simpan nomor bukti dan kode akses untuk memeriksa status melalui fitur bukti pendaftaran pada formulir.') !!}</p></details>
                        <details><summary>{!! $landing->text('lks.tanya-lks.11', 'Apakah status Disetujui sama dengan tanda pendaftaran?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.12', 'Tidak. Status Disetujui menandakan data dan berkas sudah sesuai di SiDaya. Tanda pendaftaran resmi, nomor induk LKS, dan akun dashboard diproses terpisah oleh Dinas Sosial.') !!}</p></details>
                        <details><summary>{!! $landing->text('lks.tanya-lks.13', 'Seberapa sering LKS wajib melapor?') !!}</summary><p>{!! $landing->text('lks.tanya-lks.14', 'Paling sedikit 1 kali dalam setahun. Di SiDaya, LKS mengirim laporan setiap semester melalui menu Laporan Semester pada dashboard LKS.') !!}</p></details>
                    </div>
                </div>
            </section>

            <section class="lks-section" aria-labelledby="lks-register-cta-title">
                <div class="container">
                    <div class="lks-register-band">
                        <div class="lks-register-band-layout">
                            <div><p class="eyebrow">{!! $landing->text('lks.lks-register-cta.01', 'Untuk pengelola lembaga') !!}</p><h2 id="lks-register-cta-title">{!! $landing->text('lks.lks-register-cta.02', 'Kenalkan lembaga Anda melalui SiDaya') !!}</h2><p>{!! $landing->text('lks.lks-register-cta.03', 'Sampaikan informasi kelembagaan untuk pendataan sesuai mekanisme yang berlaku. Hubungi Dinas Sosial untuk penjelasan prosedur resmi.') !!}</p></div>
                            <div class="lks-actions"><a class="button button-primary" href="/pilar/pendaftaran-lks.html">Daftar LKS</a><a class="button lks-button-outline" href="/kontak.html">Hubungi Dinsos</a></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
@endsection
