@extends('layouts.public', [
    'title' => 'Pendaftaran LKS &mdash; SiDaya',
    'description' => 'Simulasi pendaftaran Lembaga Kesejahteraan Sosial di SiDaya: profil lembaga, fokus pelayanan, berkas pendaftaran, dan surat permohonan.',
    'bodyClass' => 'detail-page lks-registration-page',
    'bodyId' => '',
    'navigation' => 'lks',
    'showInfoDialog' => false,
    'entry' => 'registration',
])

@section('content')
<main id="konten" tabindex="-1">
            <section class="lksr-hero" aria-labelledby="page-title">
                <div class="container lksr-shell">
                    <div class="lksr-hero-top">
                        <nav class="breadcrumbs" aria-label="Jejak navigasi">
                            <ol>
                                <li>
                                    <a href="/index.html">Beranda</a>
                                </li>
                                <li>
                                    <a href="/pilar/lks.html">LKS</a>
                                </li>
                                <li aria-current="page">Pendaftaran</li>
                            </ol>
                        </nav>
                        <div class="lksr-hero-actions">
                            <a
                                class="button lksr-button-outline"
                                href="/pilar/lks.html#pendaftaran"
                                >&larr; Kembali ke Panduan LKS</a
                            >
                            @if (app()->environment('local'))
                            <button
                                class="button button-primary"
                                id="btn-fill-demo"
                                type="button"
                                hidden
                            >
                                Isi Data Contoh
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="lksr-page-heading">
                        <p class="eyebrow">Sistem Registrasi Awal LKS</p>
                        <h1 id="page-title">
                            Pendaftaran Lembaga Kesejahteraan Sosial (LKS)
                        </h1>
                        <p>
                            Lengkapi identitas pengisi data, unduh format dokumen,
                            lalu unggah 16 berkas persyaratan pendaftaran LKS
                            untuk verifikasi awal.
                        </p>
                    </div>
                    <aside class="lksr-demo-note">
                        <svg
                            class="lksr-icon"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 11v6m0-10h.01" />
                        </svg>
                        <div>
                            <strong
                                >Pendaftaran Lembaga Kesejahteraan Sosial</strong
                            >
                            <p>
                                Data dan berkas dikirim ke Dinas Sosial setelah Anda menekan tombol kirim, lalu diverifikasi oleh Admin.
                            </p>
                        </div>
                    </aside>
                    <noscript>
                        <p class="lksr-demo-note">
                            Aktifkan JavaScript untuk menggunakan formulir ini.
                            Panduan dan tautan unduh PDF tetap dapat digunakan.
                        </p>
                    </noscript>
                </div>
            </section>
            <section
                class="lksr-section"
                aria-label="Formulir dan panduan pendaftaran"
            >
                <div class="container lksr-shell">
                    <div class="lksr-layout" id="view-form-grid">
                        <form class="lksr-main-column" id="form-lks" novalidate>
                            <div style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;" aria-hidden="true"><label for="lks-website">Situs web</label><input type="text" id="lks-website" name="website" tabindex="-1" autocomplete="off" value=""></div>
                            <div
                                class="lksr-error-summary"
                                id="form-error-banner"
                                role="alert"
                                tabindex="-1"
                                hidden
                            ></div>

                            <section
                                class="lksr-card lksr-requirements-card"
                                id="ketentuan-persyaratan"
                                aria-labelledby="persyaratan-title"
                            >
                                <div class="lksr-req-header">
                                    <div class="lksr-req-badge-row">
                                        <span class="lksr-pill lksr-pill-regulasi">
                                            <svg class="lksr-icon" viewBox="0 0 24 24" aria-hidden="true" style="width:14px;height:14px;">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                            Ketentuan Regulasi Resmi
                                        </span>
                                        <span class="lksr-pill">5 Ayat Persyaratan</span>
                                    </div>
                                    <h2 class="lksr-req-title" id="persyaratan-title">
                                        Ketentuan &amp; Persyaratan Pendaftaran LKS Berbadan Hukum
                                    </h2>
                                    <p class="lksr-req-intro">
                                        Sebelum mengisi formulir dan mengunggah berkas, pelajari rincian persyaratan pendaftaran Lembaga Kesejahteraan Sosial (LKS) Berbadan Hukum berdasarkan ketentuan regulasi perundang-undangan berikut:
                                    </p>
                                </div>

                                <div class="lksr-req-overview" aria-label="Ringkasan kelompok persyaratan">
                                    <div class="lksr-req-stat-box">
                                        <span class="lksr-req-stat-num">Ayat (1)</span>
                                        <span class="lksr-req-stat-label">Legalitas Dasar</span>
                                        <span class="lksr-req-stat-sub">4 Dokumen Wajib</span>
                                    </div>
                                    <div class="lksr-req-stat-box">
                                        <span class="lksr-req-stat-num">Ayat (2)</span>
                                        <span class="lksr-req-stat-label">Dokumen Lanjutan</span>
                                        <span class="lksr-req-stat-sub">10 Dokumen &amp; Pernyataan</span>
                                    </div>
                                    <div class="lksr-req-stat-box">
                                        <span class="lksr-req-stat-num">Ayat (3)</span>
                                        <span class="lksr-req-stat-label">Muatan Proposal</span>
                                        <span class="lksr-req-stat-sub">5 Unsur Wajib</span>
                                    </div>
                                    <div class="lksr-req-stat-box">
                                        <span class="lksr-req-stat-num">Ayat (4-5)</span>
                                        <span class="lksr-req-stat-label">Lingkup Wilayah</span>
                                        <span class="lksr-req-stat-sub">Tanda Daftar Daerah</span>
                                    </div>
                                </div>

                                <div class="lksr-req-groups">
                                    <!-- Panel Ayat (1) -->
                                    <article class="lksr-req-panel" aria-labelledby="req-ayat-1-title">
                                        <div class="lksr-req-panel-head">
                                            <div class="lksr-req-panel-title-wrap">
                                                <span class="lksr-req-ayat-badge">(1)</span>
                                                <h3 class="lksr-req-panel-title" id="req-ayat-1-title">
                                                    Persyaratan Pendaftaran bagi LKS Berbadan Hukum
                                                </h3>
                                            </div>
                                            <span class="lksr-req-panel-tag">Formulir Berkas No. 1 &ndash; 4</span>
                                        </div>
                                        <div class="lksr-req-panel-body">
                                            <p class="lksr-req-statement">
                                                (1) Persyaratan pendaftaran bagi LKS Berbadan Hukum meliputi:
                                            </p>
                                            <ul class="lksr-req-list lksr-req-list-2col">
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">a</span>
                                                    <span class="lksr-req-text">anggaran dasar dan anggaran rumah tangga;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">b</span>
                                                    <span class="lksr-req-text">akta pendirian;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">c</span>
                                                    <span class="lksr-req-text">surat keterangan domisili dari lurah/kepala desa setempat atau nama lain; dan</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">d</span>
                                                    <span class="lksr-req-text">nomor pokok wajib pajak badan hukum LKS.</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </article>

                                    <!-- Panel Ayat (2) -->
                                    <article class="lksr-req-panel" aria-labelledby="req-ayat-2-title">
                                        <div class="lksr-req-panel-head">
                                            <div class="lksr-req-panel-title-wrap">
                                                <span class="lksr-req-ayat-badge">(2)</span>
                                                <h3 class="lksr-req-panel-title" id="req-ayat-2-title">
                                                    Dokumen Tambahan LKS Berbadan Hukum
                                                </h3>
                                            </div>
                                            <span class="lksr-req-panel-tag">Formulir Berkas No. 5 &ndash; 14</span>
                                        </div>
                                        <div class="lksr-req-panel-body">
                                            <p class="lksr-req-statement">
                                                (2) Selain persyaratan sebagaimana dimaksud pada ayat (1), LKS Berbadan Hukum harus menyampaikan dokumen:
                                            </p>
                                            <ul class="lksr-req-list lksr-req-list-2col">
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">a</span>
                                                    <span class="lksr-req-text">pengesahan pendirian badan hukum oleh kementerian yang menyelenggarakan urusan pemerintahan di bidang hukum;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">b</span>
                                                    <span class="lksr-req-text">struktur organisasi LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">c</span>
                                                    <span class="lksr-req-text">alamat, nomor telepon, website, dan media social LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">d</span>
                                                    <span class="lksr-req-text">kartu tanda penduduk dan nomor telepon pengurus LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">e</span>
                                                    <span class="lksr-req-text">surat keterangan bebas dari narkoba bagi pengurus LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">f</span>
                                                    <span class="lksr-req-text">surat pernyataan tidak dalam sengketa kepengurusan atau tidak dalam perkara di pengadilan;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">g</span>
                                                    <span class="lksr-req-text">surat pernyataan kesanggupan melaporkan kegiatan;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">h</span>
                                                    <span class="lksr-req-text">surat pernyataan bahwa sumber pendanaan tidak berasal dari melawan dan tidak digunakan untuk kegiatan yang melawan hukum;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">i</span>
                                                    <span class="lksr-req-text">surat pernyataan persetujuan tetangga; dan</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">j</span>
                                                    <span class="lksr-req-text">proposal.</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </article>

                                    <!-- Panel Ayat (3) -->
                                    <article class="lksr-req-panel" aria-labelledby="req-ayat-3-title">
                                        <div class="lksr-req-panel-head">
                                            <div class="lksr-req-panel-title-wrap">
                                                <span class="lksr-req-ayat-badge">(3)</span>
                                                <h3 class="lksr-req-panel-title" id="req-ayat-3-title">
                                                    Muatan Wajib Dokumen Proposal
                                                </h3>
                                            </div>
                                            <span class="lksr-req-panel-tag">Termuat di Proposal (Berkas No. 14)</span>
                                        </div>
                                        <div class="lksr-req-panel-body">
                                            <p class="lksr-req-statement">
                                                (3) Proposal sebagaimana dimaksud pada ayat (2) huruf j berisi:
                                            </p>
                                            <ul class="lksr-req-list lksr-req-list-2col">
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">a</span>
                                                    <span class="lksr-req-text">program dan kegiatan LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">b</span>
                                                    <span class="lksr-req-text">modal kerja untuk pelaksanaan kegiatan;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">c</span>
                                                    <span class="lksr-req-text">nomor rekening LKS;</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">d</span>
                                                    <span class="lksr-req-text">sumber daya manusia; dan</span>
                                                </li>
                                                <li class="lksr-req-item">
                                                    <span class="lksr-req-letter">e</span>
                                                    <span class="lksr-req-text">kelengkapan sarana dan prasarana.</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </article>

                                    <!-- Panel Ayat (4) & (5) -->
                                    <article class="lksr-req-panel" aria-labelledby="req-ayat-4-5-title">
                                        <div class="lksr-req-panel-head">
                                            <div class="lksr-req-panel-title-wrap">
                                                <span class="lksr-req-ayat-badge">(4 &ndash; 5)</span>
                                                <h3 class="lksr-req-panel-title" id="req-ayat-4-5-title">
                                                    Ketentuan Wilayah Kewenangan Khusus (Lintas Daerah)
                                                </h3>
                                            </div>
                                            <span class="lksr-req-panel-tag">Formulir Berkas No. 15 (Jika Berlaku)</span>
                                        </div>
                                        <div class="lksr-req-panel-body">
                                            <div class="lksr-req-subcards">
                                                <div class="lksr-req-subcard">
                                                    <div class="lksr-req-subcard-head">
                                                        <span class="lksr-req-ayat-badge">(4)</span>
                                                        <strong>Lingkup Kementerian Sosial</strong>
                                                    </div>
                                                    <p>
                                                        (4) Dalam hal pendaftaran bagi LKS Berbadan Hukum diajukan untuk lingkup wilayah kewenangan Kementerian Sosial harus melampirkan tanda pendaftaran yang diterbitkan oleh paling sedikit 2 (dua) instansi yang menyelenggarakan urusan pemerintahan di bidang sosial atau perangkat daerah yang menyelenggarakan pelayanan perizinan dari daerah provinsi yang berbeda.
                                                    </p>
                                                </div>
                                                <div class="lksr-req-subcard">
                                                    <div class="lksr-req-subcard-head">
                                                        <span class="lksr-req-ayat-badge">(5)</span>
                                                        <strong>Lingkup Kewenangan Provinsi</strong>
                                                    </div>
                                                    <p>
                                                        (5) Dalam hal pendaftaran bagi LKS Berbadan Hukum diajukan untuk lingkup wilayah kewenangan provinsi harus melampirkan tanda pendaftaran yang diterbitkan oleh paling sedikit 2 (dua) instansi yang menyelenggarakan urusan pemerintahan di bidang sosial atau perangkat daerah yang menyelenggarakan pelayanan perizinan dari daerah kabupaten/kota yang berbeda.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <div class="lksr-req-footer">
                                    <div class="lksr-req-footer-text">
                                        <svg class="lksr-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="M12 8v4m0 4h.01"></path>
                                        </svg>
                                        <span>
                                            Lengkapi <strong>16 berkas unggahan</strong> pada formulir di bawah, termasuk surat permohonan pendaftaran. Format template surat pernyataan dapat diunduh pada panel samping.
                                        </span>
                                    </div>
                                    <a href="#section-berkas" class="button button-primary" style="font-size: 13px; padding: 8px 16px;">
                                        Menuju Unggah Berkas &darr;
                                    </a>
                                </div>
                            </section>

                            <fieldset class="lksr-card lksr-fieldset" id="section-identitas">
                                <legend>
                                    <span
                                        class="lksr-section-letter"
                                        aria-hidden="true"
                                        >1</span
                                    >
                                    Identitas Pengisi Data
                                </legend>
                                <p class="lksr-section-description">
                                    Lengkapi identitas pengisi data dan informasi lembaga. Isian bertanda
                                    <span
                                        class="lksr-required"
                                        aria-hidden="true"
                                        >*</span
                                    >
                                    wajib diisi.
                                </p>
                                <div class="lksr-field-grid">
                                    <div class="lksr-field lksr-field-wide">
                                        <label for="lks-nama">
                                            Nama Yayasan / LKS
                                            <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="lks-nama"
                                            name="lks-nama"
                                            type="text"
                                            maxlength="150"
                                            autocomplete="organization"
                                            placeholder="Nama yayasan atau lembaga"
                                            required
                                            aria-describedby="err-lks-nama"
                                        />
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-nama"
                                            hidden
                                        ></p>
                                    </div>
                                    <div class="lksr-field">
                                        <label for="lks-ketua">
                                            Nama Pengisi Data
                                            <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="lks-ketua"
                                            name="lks-ketua"
                                            type="text"
                                            maxlength="150"
                                            autocomplete="name"
                                            placeholder="Nama pengisi data atau penanggung jawab"
                                            required
                                            aria-describedby="err-lks-ketua"
                                        />
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-ketua"
                                            hidden
                                        ></p>
                                    </div>
                                    <div class="lksr-field">
                                        <label for="lks-whatsapp">
                                            Nomor WhatsApp yang Bisa Dihubungi
                                            <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="lks-whatsapp"
                                            name="lks-whatsapp"
                                            type="tel"
                                            inputmode="tel"
                                            maxlength="30"
                                            autocomplete="tel"
                                            data-numeric="phone"
                                            placeholder="Contoh: 081234567890"
                                            required
                                            aria-describedby="help-lks-whatsapp err-lks-whatsapp"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-whatsapp"
                                        >
                                            Gunakan nomor pengisi data atau penanggung jawab
                                            yang bisa dihubungi. Awali dengan 08, 62,
                                            atau +62.
                                        </p>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-whatsapp"
                                            hidden
                                        ></p>
                                    </div>
                                    <div class="lksr-field">
                                        <label for="lks-izin">
                                            Nomor Izin Operasional Lama
                                            <span class="lksr-optional"
                                                >(jika ada)</span
                                            >
                                        </label>
                                        <input
                                            id="lks-izin"
                                            name="lks-izin"
                                            type="text"
                                            maxlength="150"
                                            placeholder="Nomor izin operasional sebelumnya"
                                            aria-describedby="help-lks-izin err-lks-izin"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-izin"
                                        >
                                            Kosongkan jika lembaga belum
                                            memiliki izin operasional
                                            sebelumnya.
                                        </p>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-izin"
                                            hidden
                                        ></p>
                                    </div>
                                    <div class="lksr-field">
                                        <label for="lks-rekening">
                                            Rekening Bank
                                            <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="lks-rekening"
                                            name="lks-rekening"
                                            type="text"
                                            maxlength="150"
                                            placeholder="Contoh: Bank Kalsel - 1234567890 a.n LKS"
                                            required
                                            aria-describedby="help-lks-rekening err-lks-rekening"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-rekening"
                                        >
                                            Cantumkan nama bank, nomor rekening, dan nama pemilik rekening resmi LKS.
                                        </p>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-rekening"
                                            hidden
                                        ></p>
                                    </div>
                                    <div class="lksr-field lksr-field-wide">
                                        <label for="lks-fokus">
                                            Fokus Pelayanan / Bidang Garap
                                            <span aria-hidden="true">*</span>
                                        </label>
                                        <textarea
                                            id="lks-fokus"
                                            name="lks-fokus"
                                            rows="4"
                                            maxlength="2000"
                                            placeholder="Jelaskan sasaran penerima manfaat dan bentuk layanan utama lembaga..."
                                            required
                                            aria-describedby="help-lks-fokus err-lks-fokus"
                                        ></textarea>
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-fokus"
                                        >
                                            Jelaskan sasaran penerima manfaat
                                            dan layanan utama lembaga. Maksimal
                                            2.000 karakter.
                                        </p>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-fokus"
                                            hidden
                                        ></p>
                                    </div>
                                    @php
                                        $serviceChoiceGroups = [
                                            'lks-prioritas-pelayanan' => [
                                                'label' => 'Prioritas Pelayanan',
                                                'options' => ['Kemiskinan', 'Keterlantaran', 'Disabilitas', 'Keterpencilan', 'Ketunaan sosial dan penyimpangan perilaku', 'Korban bencana', 'Korban tindak kekerasan, eksploitasi, dan diskriminasi'],
                                            ],
                                            'lks-jenis-pelayanan' => [
                                                'label' => 'Jenis Pelayanan',
                                                'options' => ['Jaminan Sosial', 'Rehabilitasi Sosial', 'Pemberdayaan Sosial', 'Perlindungan Sosial'],
                                            ],
                                            'lks-sasaran-pelayanan' => [
                                                'label' => 'Sasaran Pelayanan',
                                                'options' => ['Perseorangan', 'Keluarga', 'Kelompok', 'Masyarakat'],
                                            ],
                                            'lks-jenis-kegiatan' => [
                                                'label' => 'Jenis Kegiatan',
                                                'options' => ['Unit pelayanan sosial untuk anak', 'Unit pelayanan sosial untuk lansia', 'Unit pelayanan sosial untuk penyandang disabilitas', 'Dukungan psikososial untuk anak dalam situasi bencana', 'Pemberdayaan ekonomi kelompok miskin dan rentan'],
                                            ],
                                            'lks-sifat-pelayanan' => [
                                                'label' => 'Sifat Pelayanan',
                                                'options' => ['Dalam LKS', 'Luar LKS'],
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($serviceChoiceGroups as $groupKey => $choiceGroup)
                                        <fieldset
                                            class="lksr-choice-group lksr-field-wide"
                                            id="{{ $groupKey }}"
                                            name="{{ $groupKey }}"
                                            data-choice-group="{{ $groupKey }}"
                                            tabindex="-1"
                                            aria-required="true"
                                            aria-describedby="help-{{ $groupKey }} err-{{ $groupKey }}"
                                        >
                                            <legend>
                                                {{ $choiceGroup['label'] }}
                                                <span class="lksr-required" aria-hidden="true">*</span>
                                            </legend>
                                            <p class="lksr-field-help" id="help-{{ $groupKey }}">
                                                Pilih satu atau lebih sesuai pelayanan lembaga.
                                            </p>
                                            <div class="lksr-choice-grid">
                                                @foreach ($choiceGroup['options'] as $option)
                                                    <label class="lksr-choice-option" for="{{ $groupKey }}-{{ $loop->iteration }}">
                                                        <input
                                                            id="{{ $groupKey }}-{{ $loop->iteration }}"
                                                            type="checkbox"
                                                            name="{{ $groupKey }}[]"
                                                            value="{{ $option }}"
                                                        />
                                                        <span>{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <p class="lksr-field-error" id="err-{{ $groupKey }}" hidden></p>
                                        </fieldset>
                                    @endforeach
                                </div>
                            </fieldset>
                            <fieldset class="lksr-card lksr-fieldset" id="section-berkas">
                                <legend>
                                    <span
                                        class="lksr-section-letter"
                                        aria-hidden="true"
                                        >2</span
                                    >
                                    Berkas Pendaftaran LKS
                                    <span
                                        class="lksr-required"
                                        aria-hidden="true"
                                        >*</span
                                    >
                                </legend>
                                <p class="lksr-section-description">
                                    Unggah masing-masing dokumen persyaratan secara terpisah sesuai ketentuan resmi Ayat (1) s/d Ayat (5).
                                    Format berkas: PDF, JPG, atau PNG sesuai batas ukuran per berkas.
                                </p>
                                <p class="lksr-document-count">
                                    16 dokumen wajib
                                </p>
                                <div class="lksr-doc-grid">
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="surat_permohonan"
                                        data-label="Surat Permohonan Pendaftaran LKS"
                                        data-max-mb="5"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >1</span
                                            >
                                            <div>
                                                <label for="lks-surat_permohonan-input">
                                                    Surat Permohonan Pendaftaran LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    5 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-surat_permohonan-input"
                                            name="lks-surat_permohonan-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-surat_permohonan-input err-lks-surat_permohonan-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-surat_permohonan-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-surat_permohonan-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="ad_art"
                                        data-label="Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART)"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >2</span
                                            >
                                            <div>
                                                <label for="lks-ad_art-input">
                                                    Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART)
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-ad_art-input"
                                            name="lks-ad_art-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-ad_art-input err-lks-ad_art-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-ad_art-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-ad_art-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="akta_pendirian"
                                        data-label="Akta Pendirian"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >3</span
                                            >
                                            <div>
                                                <label for="lks-akta_pendirian-input">
                                                    Akta Pendirian
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-akta_pendirian-input"
                                            name="lks-akta_pendirian-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-akta_pendirian-input err-lks-akta_pendirian-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-akta_pendirian-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-akta_pendirian-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="domisili"
                                        data-label="Surat Keterangan Domisili dari Lurah/Kepala Desa Setempat atau Nama Lain"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >4</span
                                            >
                                            <div>
                                                <label for="lks-domisili-input">
                                                    Surat Keterangan Domisili dari Lurah/Kepala Desa Setempat atau Nama Lain
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-domisili-input"
                                            name="lks-domisili-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-domisili-input err-lks-domisili-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-domisili-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-domisili-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="npwp"
                                        data-label="Nomor Pokok Wajib Pajak (NPWP) Badan Hukum LKS"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >5</span
                                            >
                                            <div>
                                                <label for="lks-npwp-input">
                                                    Nomor Pokok Wajib Pajak (NPWP) Badan Hukum LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-npwp-input"
                                            name="lks-npwp-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-npwp-input err-lks-npwp-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-npwp-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-npwp-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="kemenkumham"
                                        data-label="Pengesahan Pendirian Badan Hukum oleh Kementerian yang Menyelenggarakan Urusan Pemerintahan di Bidang Hukum"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >6</span
                                            >
                                            <div>
                                                <label for="lks-kemenkumham-input">
                                                    Pengesahan Pendirian Badan Hukum oleh Kementerian yang Menyelenggarakan Urusan Pemerintahan di Bidang Hukum
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-kemenkumham-input"
                                            name="lks-kemenkumham-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-kemenkumham-input err-lks-kemenkumham-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-kemenkumham-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-kemenkumham-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="struktur_organisasi"
                                        data-label="Struktur Organisasi LKS"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >7</span
                                            >
                                            <div>
                                                <label for="lks-struktur_organisasi-input">
                                                    Struktur Organisasi LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-struktur_organisasi-input"
                                            name="lks-struktur_organisasi-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-struktur_organisasi-input err-lks-struktur_organisasi-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-struktur_organisasi-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-struktur_organisasi-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="data_umum"
                                        data-label="DATA UMUM LKS : Alamat, Nomor Telepon, Website, dan Media Sosial LKS"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >8</span
                                            >
                                            <div>
                                                <label for="lks-data_umum-input">
                                                    DATA UMUM LKS : Alamat, Nomor Telepon, Website, dan Media Sosial LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-data_umum-input"
                                            name="lks-data_umum-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-data_umum-input err-lks-data_umum-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-data_umum-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-data_umum-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="ktp_kontak_pengurus"
                                        data-label="Kartu Tanda Penduduk Pengurus dan Nomor Telepon Pengurus LKS"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >9</span
                                            >
                                            <div>
                                                <label for="lks-ktp_kontak_pengurus-input">
                                                    Kartu Tanda Penduduk Pengurus dan Nomor Telepon Pengurus LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-ktp_kontak_pengurus-input"
                                            name="lks-ktp_kontak_pengurus-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-ktp_kontak_pengurus-input err-lks-ktp_kontak_pengurus-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-ktp_kontak_pengurus-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-ktp_kontak_pengurus-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="bebas_narkoba"
                                        data-label="Surat Keterangan Bebas dari Narkoba bagi Pengurus LKS"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >10</span
                                            >
                                            <div>
                                                <label for="lks-bebas_narkoba-input">
                                                    Surat Keterangan Bebas dari Narkoba bagi Pengurus LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-bebas_narkoba-input"
                                            name="lks-bebas_narkoba-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-bebas_narkoba-input err-lks-bebas_narkoba-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-bebas_narkoba-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-bebas_narkoba-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="pernyataan_sengketa"
                                        data-label="Surat Pernyataan Tidak dalam Sengketa Kepengurusan atau Tidak dalam Perkara di Pengadilan"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >11</span
                                            >
                                            <div>
                                                <label for="lks-pernyataan_sengketa-input">
                                                    Surat Pernyataan Tidak dalam Sengketa Kepengurusan atau Tidak dalam Perkara di Pengadilan
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-pernyataan_sengketa-input"
                                            name="lks-pernyataan_sengketa-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-pernyataan_sengketa-input err-lks-pernyataan_sengketa-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-pernyataan_sengketa-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-pernyataan_sengketa-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="kesanggupan_laporan"
                                        data-label="Surat Pernyataan Kesanggupan Melaporkan Kegiatan"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >12</span
                                            >
                                            <div>
                                                <label for="lks-kesanggupan_laporan-input">
                                                    Surat Pernyataan Kesanggupan Melaporkan Kegiatan
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-kesanggupan_laporan-input"
                                            name="lks-kesanggupan_laporan-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-kesanggupan_laporan-input err-lks-kesanggupan_laporan-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-kesanggupan_laporan-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-kesanggupan_laporan-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="keabsahan_dana"
                                        data-label="Surat Pernyataan bahwa Sumber Pendanaan Tidak Berasal dari Kegiatan yang Melawan Hukum dan Tidak Digunakan untuk Kegiatan yang Melawan Hukum"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >13</span
                                            >
                                            <div>
                                                <label for="lks-keabsahan_dana-input">
                                                    Surat Pernyataan bahwa Sumber Pendanaan Tidak Berasal dari Kegiatan yang Melawan Hukum dan Tidak Digunakan untuk Kegiatan yang Melawan Hukum
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-keabsahan_dana-input"
                                            name="lks-keabsahan_dana-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-keabsahan_dana-input err-lks-keabsahan_dana-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-keabsahan_dana-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-keabsahan_dana-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="persetujuan_tetangga"
                                        data-label="Surat Pernyataan Persetujuan Tetangga"
                                        data-max-mb="2"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >14</span
                                            >
                                            <div>
                                                <label for="lks-persetujuan_tetangga-input">
                                                    Surat Pernyataan Persetujuan Tetangga
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    2 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-persetujuan_tetangga-input"
                                            name="lks-persetujuan_tetangga-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-persetujuan_tetangga-input err-lks-persetujuan_tetangga-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-persetujuan_tetangga-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-persetujuan_tetangga-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="proposal"
                                        data-label="Proposal yang Memuat (Program dan Kegiatan LKS, Modal Kerja untuk Pelaksanaan Kegiatan, Nomor Rekening LKS, Sumber Daya Manusia dan Kelengkapan Sarana dan Prasarana)"
                                        data-max-mb="5"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >15</span
                                            >
                                            <div>
                                                <label for="lks-proposal-input">
                                                    Proposal yang Memuat (Program dan Kegiatan LKS, Modal Kerja untuk Pelaksanaan Kegiatan, Nomor Rekening LKS, Sumber Daya Manusia dan Kelengkapan Sarana dan Prasarana)
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    5 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-proposal-input"
                                            name="lks-proposal-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-proposal-input err-lks-proposal-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-proposal-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-proposal-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                    <div
                                        class="lksr-document"
                                        data-lks-attachment="formulir_pendaftaran"
                                        data-label="FORMULIR PENDAFTARAN LKS"
                                        data-max-mb="5"
                                    >
                                        <div class="lksr-document-heading">
                                            <span
                                                class="lksr-document-number"
                                                aria-hidden="true"
                                                >16</span
                                            >
                                            <div>
                                                <label for="lks-formulir_pendaftaran-input">
                                                    FORMULIR PENDAFTARAN LKS
                                                    <span
                                                        class="lksr-required"
                                                        aria-hidden="true"
                                                        >*</span
                                                    >
                                                </label>
                                                <p class="lksr-field-help">
                                                    PDF, JPG, atau PNG (maksimal
                                                    5 MB)
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="lksr-document-status"
                                            data-file-status
                                            role="status"
                                            >Belum ada berkas</span
                                        >
                                        <p class="lksr-field-help">
                                            Pilih atau seret satu berkas ke area ini.
                                        </p>
                                        <div
                                            class="lksr-upload-box"
                                            data-file-empty
                                        >
                                            <svg
                                                class="lksr-icon"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M14 2H5v20h14V7l-5-5Zm0 0v5h5M12 18v-7m-3 3 3-3 3 3"
                                                />
                                            </svg>
                                            <p>
                                                Tarik berkas ke area ini atau gunakan
                                                tombol di bawah.
                                            </p>
                                        </div>
                                        <input
                                            class="lksr-file-input"
                                            id="lks-formulir_pendaftaran-input"
                                            name="lks-formulir_pendaftaran-input"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            aria-required="true"
                                            aria-describedby="help-lks-formulir_pendaftaran-input err-lks-formulir_pendaftaran-input"
                                        />
                                        <p
                                            class="lksr-field-help"
                                            id="help-lks-formulir_pendaftaran-input"
                                        >
                                            Berkas baru terkirim setelah formulir dikirim.
                                        </p>
                                        <div
                                            class="lksr-selected-file"
                                            data-file-selected
                                            hidden
                                        >
                                            <div>
                                                <strong data-file-name></strong>
                                                <p
                                                    class="lksr-field-help"
                                                    data-file-size
                                                ></p>
                                            </div>
                                            <div class="lksr-file-actions">
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-view
                                                >
                                                    Lihat
                                                </button>
                                                <button
                                                    class="button lksr-button-outline"
                                                    type="button"
                                                    data-file-replace
                                                >
                                                    Ganti
                                                </button>
                                                <button
                                                    class="button lksr-button-plain"
                                                    type="button"
                                                    data-file-remove
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <p
                                            class="lksr-field-error"
                                            id="err-lks-formulir_pendaftaran-input"
                                            data-file-error
                                            hidden
                                        ></p>
                                        <button
                                            class="button lksr-button-plain"
                                            type="button"
                                            data-file-clear
                                            hidden
                                        >
                                            Bersihkan pilihan berkas
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="lksr-card lksr-form-actions">
                                <button
                                    class="button lksr-button-plain"
                                    id="btn-reset-form"
                                    type="button"
                                    hidden
                                >
                                    Batal &amp; Bersihkan
                                </button>
                                <button
                                    class="button button-primary"
                                    id="btn-review-submit"
                                    type="submit"
                                    disabled
                                    hidden
                                >
                                    Periksa Data &rarr;
                                </button>
                            </div>
                        </form>
                        <aside
                            class="lksr-sidebar"
                            aria-label="Format dokumen dan panduan"
                        >
                            <section
                                class="lksr-card lksr-format-card"
                                aria-labelledby="format-title"
                            >
                                <h2 id="format-title">Format Dokumen</h2>
                                <p>
                                    Unduh format, lengkapi sesuai data lembaga,
                                    lalu unggah kembali pada bagian yang sesuai.
                                </p>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M3 7h7l2 2h9v11H3V7Zm0 0V4h7l2 3"
                                            />
                                        </svg>
                                        <h3>
                                            Format Berkas Pendaftaran
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Pendaftaran-LKS.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format"
                                            data-lks-format
                                            data-format-title="Format Berkas Pendaftaran"
                                            href="/assets/documents/Format-Pendaftaran-LKS.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Berkas Pendaftaran"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Pendaftaran-LKS.pdf"
                                            download="Format-Pendaftaran-LKS.pdf"
                                            aria-label="Unduh Format Berkas Pendaftaran"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                            />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <h3>
                                            Format Surat Pernyataan Bebas Sengketa
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Surat-Pernyataan-Bebas-Sengketa.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-sengketa"
                                            data-lks-format
                                            data-format-title="Format Surat Pernyataan Bebas Sengketa Kepengurusan"
                                            href="/assets/documents/Format-Surat-Pernyataan-Bebas-Sengketa.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Surat Pernyataan Bebas Sengketa Kepengurusan"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Surat-Pernyataan-Bebas-Sengketa.pdf"
                                            download="Format-Surat-Pernyataan-Bebas-Sengketa.pdf"
                                            aria-label="Unduh Format Surat Pernyataan Bebas Sengketa Kepengurusan"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                            />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <h3>
                                            Format Surat Pernyataan Kesanggupan Lapor
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Surat-Pernyataan-Kesanggupan-Melaporkan-Kegiatan.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-lapor"
                                            data-lks-format
                                            data-format-title="Format Surat Pernyataan Kesanggupan Melaporkan Kegiatan"
                                            href="/assets/documents/Format-Surat-Pernyataan-Kesanggupan-Melaporkan-Kegiatan.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Surat Pernyataan Kesanggupan Melaporkan Kegiatan"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Surat-Pernyataan-Kesanggupan-Melaporkan-Kegiatan.pdf"
                                            download="Format-Surat-Pernyataan-Kesanggupan-Melaporkan-Kegiatan.pdf"
                                            aria-label="Unduh Format Surat Pernyataan Kesanggupan Melaporkan Kegiatan"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                            />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <h3>
                                            Format Surat Pernyataan Sumber Pendanaan
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Surat-Pernyataan-Sumber-Pendanaan.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-pendanaan"
                                            data-lks-format
                                            data-format-title="Format Surat Pernyataan Sumber Pendanaan Tidak Melawan Hukum"
                                            href="/assets/documents/Format-Surat-Pernyataan-Sumber-Pendanaan.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Surat Pernyataan Sumber Pendanaan"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Surat-Pernyataan-Sumber-Pendanaan.pdf"
                                            download="Format-Surat-Pernyataan-Sumber-Pendanaan.pdf"
                                            aria-label="Unduh Format Surat Pernyataan Sumber Pendanaan"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                            />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <h3>
                                            Format Surat Pernyataan Persetujuan Tetangga
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Surat-Pernyataan-Persetujuan-Tetangga.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-tetangga"
                                            data-lks-format
                                            data-format-title="Format Surat Pernyataan Persetujuan Tetangga"
                                            href="/assets/documents/Format-Surat-Pernyataan-Persetujuan-Tetangga.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Surat Pernyataan Persetujuan Tetangga"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Surat-Pernyataan-Persetujuan-Tetangga.pdf"
                                            download="Format-Surat-Pernyataan-Persetujuan-Tetangga.pdf"
                                            aria-label="Unduh Format Surat Pernyataan Persetujuan Tetangga"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                            />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                        <h3>
                                            Format Struktur Organisasi
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Struktur-Organisasi-LKS.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-struktur"
                                            data-lks-format
                                            data-format-title="Format Struktur Organisasi LKS"
                                            href="/assets/documents/Format-Struktur-Organisasi-LKS.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Struktur Organisasi LKS"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Struktur-Organisasi-LKS.pdf"
                                            download="Format-Struktur-Organisasi-LKS.pdf"
                                            aria-label="Unduh Format Struktur Organisasi LKS"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                                <div class="lksr-format-item">
                                    <div class="lksr-format-heading">
                                        <svg
                                            class="lksr-icon"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                            <line x1="8" y1="21" x2="16" y2="21" />
                                            <line x1="12" y1="17" x2="12" y2="21" />
                                        </svg>
                                        <h3>
                                            Format Data Umum LKS
                                        </h3>
                                    </div>
                                    <p class="lksr-field-help">
                                        Format-Data-Umum-LKS.pdf
                                    </p>
                                    <div class="lksr-format-actions">
                                        <a
                                            class="button lksr-button-outline"
                                            id="btn-view-format-data-umum"
                                            data-lks-format
                                            data-format-title="Format Data Umum LKS"
                                            href="/assets/documents/Format-Data-Umum-LKS.pdf"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Lihat Format Data Umum LKS"
                                            >Lihat Format</a
                                        >
                                        <a
                                            class="button button-primary"
                                            href="/assets/documents/Format-Data-Umum-LKS.pdf"
                                            download="Format-Data-Umum-LKS.pdf"
                                            aria-label="Unduh Format Data Umum LKS"
                                            >Unduh PDF</a
                                        >
                                    </div>
                                </div>
                            </section>
                            <section
                                class="lksr-card lksr-guide"
                                aria-labelledby="guide-title"
                            >
                                <h2 id="guide-title">
                                    Panduan Tahapan Pendaftaran
                                </h2>
                                <ol>
                                    <li>
                                        <div>
                                            <strong
                                                >Unduh Format Dokumen</strong
                                            >
                                            <p>
                                                Unduh format berkas
                                                pendaftaran/laporan LKS pada
                                                panel di atas.
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong
                                                >Siapkan Dokumen
                                                Pendukung</strong
                                            >
                                            <p>
                                                Siapkan dokumen legalitas dasar, berkas operasional, surat pernyataan, proposal, dan tanda pendaftaran sesuai ketentuan resmi.
                                            </p>
                                            <a
                                                class="inline-link"
                                                href="#ketentuan-persyaratan"
                                                style="font-size: 11px; font-weight: 600; color: var(--lksr-primary); display: inline-block; margin-top: 4px;"
                                                >Lihat rincian persyaratan &uarr;</a
                                            >
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong
                                                >Isi Data &amp; Unggah
                                                Berkas</strong
                                            >
                                            <p>
                                                Isi identitas pengisi data, lalu
                                                unggah 16 berkas persyaratan pendaftaran LKS.
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div>
                                            <strong
                                                >Periksa Kembali Data</strong
                                            >
                                            <p>
                                                Periksa ringkasan dan nama
                                                berkas sebelum melanjutkan
                                                simulasi. Berkas terpilih belum
                                                berarti terverifikasi.
                                            </p>
                                        </div>
                                    </li>
                                </ol>
                                <div class="lksr-guide-contact">
                                    <h3>Butuh panduan pendaftaran?</h3>
                                    <p>
                                        Hubungi Dinas Sosial Kota Banjarbaru
                                        untuk informasi pendaftaran resmi.
                                    </p>
                                    <a href="/kontak.html"
                                        >Hubungi Dinsos Banjarbaru &rarr;</a
                                    >
                                </div>
                            </section>
                        </aside>
                    </div>
                    <section
                        class="lksr-card lksr-summary"
                        id="view-summary"
                        aria-labelledby="summary-title"
                        hidden
                    >
                        <p class="eyebrow">Periksa kembali</p>
                        <h2 id="summary-title" tabindex="-1">
                            Ringkasan Data Pendaftaran LKS
                        </h2>
                        <p class="lksr-review-intro">
                            Periksa data dan 16 berkas yang dipilih. Isi
                            dokumen belum diverifikasi.
                        </p>
                        <div class="lksr-review-panel">
                            <dl>
                                <div>
                                    <dt>Nama Yayasan / LKS</dt>
                                    <dd id="ringkasan-nama"></dd>
                                </div>
                                <div>
                                    <dt>Nama Pengisi Data</dt>
                                    <dd id="ringkasan-ketua"></dd>
                                </div>
                                <div>
                                    <dt>Nomor WhatsApp yang Bisa Dihubungi</dt>
                                    <dd id="ringkasan-whatsapp"></dd>
                                </div>
                                <div>
                                    <dt>Nomor Izin Operasional Lama</dt>
                                    <dd id="ringkasan-izin"></dd>
                                </div>
                                <div>
                                    <dt>Rekening Bank</dt>
                                    <dd id="ringkasan-rekening"></dd>
                                </div>
                                <div>
                                    <dt>Fokus Pelayanan / Bidang Garap</dt>
                                    <dd id="ringkasan-fokus"></dd>
                                </div>
                                <div>
                                    <dt>Prioritas Pelayanan</dt>
                                    <dd id="ringkasan-prioritas-pelayanan"></dd>
                                </div>
                                <div>
                                    <dt>Jenis Pelayanan</dt>
                                    <dd id="ringkasan-jenis-pelayanan"></dd>
                                </div>
                                <div>
                                    <dt>Sasaran Pelayanan</dt>
                                    <dd id="ringkasan-sasaran-pelayanan"></dd>
                                </div>
                                <div>
                                    <dt>Jenis Kegiatan</dt>
                                    <dd id="ringkasan-jenis-kegiatan"></dd>
                                </div>
                                <div>
                                    <dt>Sifat Pelayanan</dt>
                                    <dd id="ringkasan-sifat-pelayanan"></dd>
                                </div>
                            </dl>
                        </div>
                        <div class="lksr-review-panel">
                            <h3>Dokumen Terpilih</h3>
                            <dl id="ringkasan-dokumen"></dl>
                        </div>
                        <div class="lksr-dialog-actions">
                            <button
                                class="button lksr-button-outline"
                                id="btn-back-to-edit"
                                type="button"
                            >
                                Kembali Mengedit
                            </button>
                            <button
                                class="button button-primary"
                                id="btn-confirm"
                                type="button"
                            >
                                Ajukan Pendaftaran
                            </button>
                        </div>
                    </section>
                    <section
                        class="lksr-card lksr-success"
                        id="view-success"
                        aria-labelledby="success-title"
                        hidden
                    >
                        <span class="lksr-success-icon" aria-hidden="true">
                            <svg
                                class="lksr-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path d="m7 12 3 3 7-7" />
                            </svg>
                        </span>
                        <p class="eyebrow">Pemeriksaan Selesai</p>
                        <h2 id="success-title" tabindex="-1">
                            Pendaftaran Belum Dikirim (Mode Demo)
                        </h2>
                        <p>
                            Pendaftaran sudah masuk dan menunggu verifikasi Admin Dinas Sosial. Hasilnya disampaikan melalui WhatsApp.
                        </p>
                        <dl class="lksr-success-recap">
                            <div><dt>Status</dt><dd id="success-reference"></dd></div>
                            <div>
                                <dt>Nama Lembaga</dt>
                                <dd id="success-nama"></dd>
                            </div>
                            <div>
                                <dt>Berkas Pendaftaran</dt>
                                <dd id="success-berkas"></dd>
                            </div>
                            <div>
                                <dt>Surat Permohonan Pendaftaran LKS</dt>
                                <dd id="success-permohonan"></dd>
                            </div>
                            <div>
                                <dt>Waktu Pemeriksaan</dt>
                                <dd id="success-time"></dd>
                            </div>
                        </dl>
                        <div class="lksr-dialog-actions">
                            <button
                                class="button lksr-button-soft"
                                id="btn-reset"
                                type="button"
                            >
                                Mulai Pengisian Baru
                            </button>
                            <a class="button button-primary" href="/pilar/lks.html"
                                >Kembali ke Halaman LKS</a
                            >
                        </div>
                    </section>
                </div>
            </section>
        </main>
@endsection

@section('dialogs')
<dialog
            class="lksr-dialog"
            id="modal-konfirmasi"
            aria-labelledby="confirmation-title"
            aria-describedby="confirmation-description"
        >
            <p class="eyebrow">Konfirmasi Pendaftaran</p>
            <h2 id="confirmation-title">Selesaikan pemeriksaan pendaftaran?</h2>
            <p id="confirmation-description">
                Pastikan data sudah benar. Keputusan Admin bersifat final; bila ditolak, Anda dapat mengirim pendaftaran baru.
            </p>
            <div class="lksr-dialog-actions">
                <button
                    class="button lksr-button-outline"
                    type="button"
                    data-close-dialog
                >
                    Kembali
                </button>
                <button
                    class="button button-primary"
                    id="btn-proceed"
                    type="button"
                >
                    Ya, Selesaikan Pemeriksaan
                </button>
            </div>
        </dialog>
        <dialog
            class="lksr-dialog lksr-preview-dialog"
            id="modal-file-preview"
            aria-labelledby="file-preview-title"
        >
            <div class="lksr-preview-heading">
                <h2 id="file-preview-title">Pratinjau Berkas</h2>
                <button
                    class="button lksr-button-outline"
                    type="button"
                    data-close-dialog
                >
                    Tutup
                </button>
            </div>
            <p id="file-preview-name"></p>
            <p class="lksr-field-help" id="file-preview-size"></p>
            <div id="file-preview-content"></div>
        </dialog>
        <dialog
            class="lksr-dialog lksr-preview-dialog"
            id="modal-format-preview"
            aria-labelledby="format-preview-title"
        >
            <div class="lksr-preview-heading">
                <h2 id="format-preview-title">Format Berkas Pendaftaran</h2>
                <button
                    class="button lksr-button-outline"
                    type="button"
                    data-close-dialog
                >
                    Tutup
                </button>
            </div>
            <div id="format-preview-content"></div>
            <div class="lksr-dialog-actions">
                <a
                    class="button button-primary"
                    id="format-download-link"
                    href="/assets/documents/Format-Pendaftaran-LKS.pdf"
                    download="Format-Pendaftaran-LKS.pdf"
                    >Unduh PDF</a
                >
            </div>
        </dialog>
@endsection
