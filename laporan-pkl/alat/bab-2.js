const fs = require('fs');
const path = require('path');
const { Packer } = require('docx');
const {
  butirNomor, dokumen, gambar, jarakSetelahTabel, judulBab, judulHalaman, keteranganGambar, keteranganTabel,
  paragraf, pustaka, subBab, subSubBab, tabel,
} = require('./format');

const diagram = (nama) => path.join(__dirname, '..', 'diagram', nama);

const isi = [
  judulBab('BAB II', 'HASIL PELAKSANAAN PKL'),

  subBab('2.1 Lokasi/Tempat PKL'),
  paragraf('Praktek Kerja Lapangan berlangsung di Dinas Sosial Kota Banjarbaru, Provinsi Kalimantan Selatan, mulai 14 September 2026 sampai dengan 14 November 2026. Dinas menempatkan mahasiswa di bagian perencanaan dengan pembimbing lapangan Mas Bagus Ratna Indra D.SR., S.E., {{[nanti diisi: jabatan pembimbing lapangan]}}. Observasi selama PKL mengarah ke Bidang Pemberdayaan Sosial, yang mengelola data LKS dalam jumlah besar dan masih memerlukan digitalisasi. Karena itu, mahasiswa membangun SiDaya untuk pendataan dan pelaporan LKS di bidang tersebut.'),

  subSubBab('2.1.1 Profil dan Alamat Instansi'),
  paragraf('Dinas Sosial Kota Banjarbaru berkantor di Jl. Ketumbar, Kelurahan Komet, Kecamatan Banjarbaru Utara, Kota Banjarbaru, Kalimantan Selatan 70711 (Gambar 2.1). Kantor tersebut menempati bangunan eks RSUD lama. Warga dapat menghubungi dinas melalui telepon (0511) 4781749 dan surel dinsos@banjarbarukota.go.id (Dinas Sosial Kota Banjarbaru t.t.-d).'),
  gambar(path.join(__dirname, '..', 'aset', 'foto-kantor-dinsos.png'), 6, 9),
  keteranganGambar('Gambar 2.1 Kantor Dinas Sosial Kota Banjarbaru'),

  paragraf('Penempatan dinas di bangunan eks RSUD berawal dari rencana pemerintah kota pada 2017. Pada Maret tahun itu, Sekretaris Daerah Kota Banjarbaru menyampaikan bahwa sebagian besar ruangan eks bangunan RSUD akan direhabilitasi agar dapat dipakai Dinas Sosial dan Dinas Kesehatan. Pemerintah kota juga merencanakan taman di area tersebut untuk menghilangkan kesan angker bangunan (ANTARA News Kalimantan Selatan 2017).'),
  paragraf('Urusan sosial di Kota Banjarbaru pernah berada dalam dinas gabungan sebelum memiliki dinas sendiri. Peraturan Daerah Nomor 11 Tahun 2008 menggantikan Peraturan Daerah Nomor 20 Tahun 2003 yang sebelumnya mengatur organisasi dinas daerah. Peraturan itu menyatukan urusan sosial dan ketenagakerjaan dalam Dinas Sosial dan Tenaga Kerja (Pemerintah Kota Banjarbaru 2008).'),
  paragraf('Dinas Sosial berdiri sendiri sejak 2016. Peraturan Daerah Nomor 10 Tahun 2016 mencabut Peraturan Daerah Nomor 16 Tahun 2014, yaitu perubahan ketiga atas Peraturan Daerah Nomor 11 Tahun 2008. Peraturan baru itu menetapkan Dinas Sosial Tipe B dan menempatkan urusan tenaga kerja pada Dinas Koperasi Usaha Kecil Menengah dan Tenaga Kerja (Pemerintah Kota Banjarbaru 2016).'),
  paragraf('Kedudukan, susunan organisasi, tugas pokok, fungsi, dan tata kerja dinas kini diatur dalam Peraturan Wali Kota Banjarbaru Nomor 73 Tahun 2021. Peraturan itu mencabut Peraturan Walikota Nomor 42 Tahun 2016 dan disusun untuk menindaklanjuti penyederhanaan birokrasi (Pemerintah Kota Banjarbaru 2021). Susunan organisasinya kemudian diubah pada 2023, sebagaimana diuraikan pada subbab 2.1.3.'),
  subSubBab('2.1.2 Visi dan Misi'),
  paragraf('Dinas Sosial Kota Banjarbaru menetapkan visi "Mewujudkan Pelayanan Bidang Sosial yang Berkualitas dan Berkarakter". Dinas mengartikan pelayanan berkualitas sebagai pelayanan yang akuntabilitasnya dapat dipertanggungjawabkan. Dinas menjabarkan visi tersebut ke dalam empat misi berikut (Dinas Sosial Kota Banjarbaru t.t.-c).'),
  butirNomor('1. Mewujudkan kesejahteraan sosial melalui peningkatan kualitas dan kapasitas sumber daya manusia bidang sosial.'),
  butirNomor('2. Mewujudkan kesejahteraan sosial melalui peningkatan pelayanan kepada Penyandang Masalah Kesejahteraan Sosial (PMKS).'),
  butirNomor('3. Mewujudkan kesejahteraan sosial melalui optimalisasi peran serta masyarakat dan potensi sumber daya sosial dalam penanganan masalah sosial.'),
  butirNomor('4. Mewujudkan kesejahteraan sosial melalui pembinaan kesejahteraan sosial, penguatan karakter, moral, iman dan akhlak.'),
  paragraf('SiDaya berkaitan dengan misi ketiga, karena LKS menjadi bagian dari peran serta masyarakat dalam penanganan masalah sosial. Melalui SiDaya, dinas dapat mendata lembaga tersebut dan menerima laporan semesternya. Data itu juga mendukung pembinaan kesejahteraan sosial pada misi keempat.'),

  subSubBab('2.1.3 Struktur Organisasi'),
  paragraf('Gambar 2.2 menggambarkan ulang struktur organisasi Dinas Sosial Kota Banjarbaru menurut Pasal 3 dan lampiran bagan Peraturan Wali Kota Banjarbaru Nomor 20 Tahun 2023, yang mengubah Peraturan Wali Kota Nomor 73 Tahun 2021 (Pemerintah Kota Banjarbaru 2023). Struktur ini terdiri atas Kepala Dinas, Sekretariat dengan dua subbagian, tiga bidang yang masing-masing membawahkan dua seksi, unit pelaksana teknis daerah, dan kelompok jabatan fungsional. Bidang Pemberdayaan Sosial membawahkan Seksi Pemberdayaan Sosial dan Kelembagaan Masyarakat serta Seksi Pemberdayaan Kepahlawanan. Kotak abu-abu pada gambar menandai bidang dan seksi yang menangani kelembagaan masyarakat. {{[nanti diisi: cocokkan susunan ini dengan bagan terbaru di kantor]}}'),
  gambar(diagram('gambar-2-2-struktur-organisasi.png'), 13, 16),
  keteranganGambar('Gambar 2.2 Struktur Organisasi Dinas Sosial Kota Banjarbaru'),

  subSubBab('2.1.4 Tugas Bidang Pemberdayaan Sosial'),
  paragraf('Kepala Bidang Pemberdayaan Sosial bertugas menyiapkan bahan, data, dan petunjuk teknis untuk perencanaan, pelaksanaan, koordinasi, pengawasan, dan evaluasi kegiatan pemberdayaan sosial dan kelembagaan masyarakat, pemberdayaan kepahlawanan, perintisan kemerdekaan, serta pengelolaan sumber dana sosial. Untuk tugas itu, bidang ini merumuskan kebijakan teknis, mengoordinasikan dan membina pelaksanaan program, serta mengevaluasi program pada urusan yang sama (Pemerintah Kota Banjarbaru 2021). LKS termasuk kelembagaan masyarakat yang dibina bidang ini.'),
  paragraf('LKS adalah organisasi sosial atau perkumpulan sosial bentukan masyarakat yang menyelenggarakan kesejahteraan sosial, baik yang berbadan hukum maupun yang tidak. Untuk LKS yang wilayah kerjanya satu kota, wali kota berwenang menerbitkan tanda pendaftaran dan melaksanakan pendataan LKS (Kementerian Sosial Republik Indonesia 2024). Di Dinas Sosial Kota Banjarbaru, Bidang Pemberdayaan Sosial menangani kedua pekerjaan itu.'),

  subSubBab('2.1.5 Kegiatan Selama PKL'),
  paragraf('Tabel 2.1 merangkum kegiatan PKL, sedangkan rincian harian dengan paraf pembimbing lapangan tercatat pada lampiran Daftar Hadir dan Agenda Kegiatan. {{Kegiatan tersebut mencakup pengenalan instansi, pengumpulan data, perancangan, pembuatan aplikasi, dan pengujian.}} Daftar hadir menjadi bukti pelaksanaan PKL minimal 30 hari sesuai pedoman. {{[nanti diisi: cocokkan isi Tabel 2.1 dengan catatan harian PKL]}}'),
  keteranganTabel('Tabel 2.1 Jadwal Kegiatan Praktek Kerja Lapangan'),
  tabel([1000, 1900, 5037], [
    ['Minggu', 'Tanggal', 'Kegiatan'],
    ['1', '14–18 September 2026', 'Pengenalan instansi, struktur organisasi, dan tugas tiap bidang'],
    ['2', '21–25 September 2026', 'Observasi prosedur pendataan dan pelaporan LKS di Bidang Pemberdayaan Sosial'],
    ['3', '28 September–2 Oktober 2026', 'Wawancara petugas, pengumpulan berkas pendaftaran LKS, dan studi Peraturan Menteri Sosial Nomor 5 Tahun 2024'],
    ['4', '5–9 Oktober 2026', 'Analisis kebutuhan, perancangan UML, dan perancangan basis data'],
    ['5', '12–16 Oktober 2026', 'Pembuatan halaman publik dan formulir pendaftaran LKS'],
    ['6', '19–23 Oktober 2026', 'Pembuatan dasbor Admin, peninjauan pendaftaran, akun LKS, dan pembatasan hak akses'],
    ['7', '26–30 Oktober 2026', 'Pembuatan dasbor LKS untuk profil lembaga, pengurus, klien, laporan semester, dan surat'],
    ['8', '2–6 November 2026', 'Pembuatan notifikasi serta lima laporan PDF dan Excel'],
    ['9', '9–14 November 2026', 'Pengujian PHPUnit dan kotak hitam, perbaikan, demonstrasi kepada pembimbing lapangan, dan penyusunan laporan'],
  ]),
  jarakSetelahTabel(),

  subBab('2.2 Pengumpulan Data'),
  paragraf('Pengumpulan data memakai empat teknik menurut pedoman fakultas, yaitu observasi, wawancara, studi dokumentasi, dan studi kepustakaan. Observasi dan wawancara menghasilkan data prosedur yang berjalan di instansi. Studi dokumentasi dan studi kepustakaan menghasilkan data kebutuhan isian dan rujukan teori.'),

  subSubBab('2.2.1 Observasi'),
  paragraf('Observasi berlangsung di ruang Bidang Pemberdayaan Sosial selama {{[nanti diisi: lama observasi]}}. Objek pengamatan mencakup cara petugas menerima berkas pendaftaran, merekap data pengurus dan klien, serta menyimpan laporan dan surat dari LKS. Pendaftaran, data, laporan, dan surat dari LKS masuk melalui berkas fisik, WhatsApp, atau lembar kerja tiap lembaga, lalu petugas menyalinnya ke lembar kerja gabungan. Lembar kerja gabungan itu memuat data klien, termasuk Nomor Induk Kependudukan, dan dapat dibuka tanpa pembatasan peran. Catatan pengamatan menjadi dasar analisis prosedur pada subbab 2.3.'),

  subSubBab('2.2.2 Wawancara'),
  paragraf('Wawancara dilakukan dengan Zaprany, S.AP., Kepala Bidang Pemberdayaan Sosial, untuk menggali prosedur, kendala, dan kebutuhan sistem baru (Tabel 2.2). Narasumber menjelaskan cara petugas menerima dan merekap data LKS, kendala yang muncul, serta laporan yang dibutuhkan dinas. Jawaban narasumber menjadi bahan perancangan fitur SiDaya.'),
  keteranganTabel('Tabel 2.2 Ringkasan Hasil Wawancara'),
  tabel([600, 3669, 3668], [
    ['No', 'Pertanyaan', 'Ringkasan Jawaban'],
    ['1', 'Bagaimana prosedur pendaftaran LKS saat ini?', 'Pengurus menyerahkan formulir dan berkas persyaratan ke kantor atau mengirimnya lewat WhatsApp, lalu petugas memeriksa kelengkapannya sebelum tanda pendaftaran diproses'],
    ['2', 'Bagaimana dinas mengumpulkan dan merekap data klien LKS?', 'Petugas meminta data pengurus dan klien kepada tiap lembaga, lalu menyalinnya ke lembar kerja gabungan'],
    ['3', 'Bagaimana LKS menyampaikan laporan kegiatan dan surat?', 'Pengurus mengantar laporan dan surat ke kantor atau mengirimnya lewat WhatsApp, lalu petugas mencatat dan mengarsipkannya'],
    ['4', 'Seberapa sering LKS menyampaikan laporan kegiatan?', 'Dinas meminta laporan kegiatan setiap semester'],
    ['5', 'Siapa yang boleh melihat dan mengubah data tiap lembaga?', 'Lembar kerja gabungan belum memiliki pembagian hak akses, sehingga siapa pun yang membuka berkas dapat melihat data klien'],
    ['6', 'Kendala apa yang paling sering terjadi?', 'Rekap memakan waktu lama, format data tiap lembaga berbeda, dan data dapat tercatat ganda'],
    ['7', 'Laporan apa saja yang dibutuhkan dinas dari data LKS?', 'Daftar LKS, rekap penghuni, data pengurus, rekap laporan semester, dan daftar surat'],
    ['8', 'Fitur apa yang petugas harapkan dari sistem baru?', 'Pendaftaran LKS secara daring, akun untuk tiap lembaga, pendataan pengurus dan klien, laporan semester, surat antara dinas dan LKS, notifikasi, ekspor PDF dan Excel, serta pembatasan akses sesuai peran'],
  ]),
  jarakSetelahTabel(),

  subSubBab('2.2.3 Studi Dokumentasi'),
  paragraf('Dokumen pendaftaran LKS terdiri atas 16 berkas. Empat belas berkas mengikuti persyaratan pendaftaran LKS berbadan hukum dalam Pasal 7 Peraturan Menteri Sosial Nomor 5 Tahun 2024, yaitu anggaran dasar dan anggaran rumah tangga, akta pendirian, surat keterangan domisili, NPWP, pengesahan badan hukum, struktur organisasi, data umum lembaga, KTP dan kontak pengurus, surat keterangan bebas narkoba, empat surat pernyataan, dan proposal. Dua berkas lainnya adalah surat permohonan tertulis dan formulir pendaftaran LKS, yang diatur dalam Pasal 11 peraturan tersebut (Kementerian Sosial Republik Indonesia 2024). Isian formulir dipetakan ke tabel basis data, sehingga nama lembaga, nomor izin, rekening, dan fokus pelayanan menjadi kolom pada tabel pendaftaran LKS.'),
  paragraf('Peraturan itu juga mewajibkan LKS membuat laporan tertulis paling sedikit satu kali dalam satu tahun, dengan isi penyelenggaraan kegiatan, keuangan, daftar donor, sumber daya manusia, aset, serta sarana dan prasarana (Kementerian Sosial Republik Indonesia 2024). SiDaya memakai periode semester, sehingga LKS melapor dua kali setahun dan memenuhi batas minimal tersebut. Kewajiban ini menjadi dasar fitur laporan semester pada SiDaya.'),
  paragraf('Studi dokumentasi juga memakai Data LKSA per Maret 2023 dari situs resmi dinas. Data itu memuat 12 lembaga kesejahteraan sosial anak beserta nama pengurus, jumlah penghuni, dan izin operasional tiap lembaga (Dinas Sosial Kota Banjarbaru t.t.-b). Data tersebut hanya mencakup lembaga anak, sehingga SiDaya juga menyediakan kategori klien lansia, penyandang disabilitas, dan dewasa. {{[nanti diisi: tambahkan dokumen lain yang diperoleh dari bidang, misalnya format rekap klien dan format laporan]}}'),

  subSubBab('2.2.4 Studi Kepustakaan'),
  paragraf('Studi kepustakaan memakai jurnal nasional terbitan enam tahun terakhir dan mengutamakan jurnal terakreditasi SINTA bila artikel yang relevan tersedia. Rujukan tersebut membahas pemerintahan elektronik, perlindungan data pribadi, tata kelola LKS, sistem informasi kesejahteraan sosial, pembatasan hak akses, serta pemodelan dan pengembangan sistem. Selain jurnal, studi kepustakaan memakai peraturan perundang-undangan dan dokumen resmi Dinas Sosial Kota Banjarbaru yang tercantum pada daftar pustaka.'),

  subBab('2.3 Analisis Sistem / Prosedur yang Sedang Berjalan'),
  paragraf('Sebelum SiDaya dibangun, tiga prosedur berjalan di Bidang Pemberdayaan Sosial, yaitu pendaftaran LKS, pendataan pengurus dan klien, serta pelaporan dan surat. Pada diagram alur tiap prosedur, kotak abu-abu berisi langkah pengurus LKS, dan kotak putih berisi langkah petugas. Analisis prosedur tersebut menjadi dasar temuan permasalahan pada subbab 2.4. {{[nanti diisi: cocokkan urutan langkah pada subbab 2.3 dengan catatan observasi dan wawancara]}}'),

  subSubBab('2.3.1 Prosedur Pendaftaran LKS'),
  paragraf('Pengurus LKS menyiapkan formulir dan dokumen, lalu menyerahkan berkas ke kantor dinas atau mengirimnya lewat WhatsApp. Petugas memeriksa kelengkapan berkas dan menghubungi pengurus bila ada kekurangan. Setelah berkas lengkap, petugas mencatat data lembaga di lembar kerja, mengarsipkan berkas fisik, dan memproses tanda pendaftaran (Gambar 2.3). Menurut Peraturan Menteri Sosial Nomor 5 Tahun 2024, tanda pendaftaran terbit paling lama 14 hari kerja sejak persyaratan dinyatakan lengkap dan berlaku selama tiga tahun. Peraturan itu juga mengatur permohonan melalui sistem dalam jaringan, dan cara manual hanya dipakai bila sistem tersebut belum tersedia (Kementerian Sosial Republik Indonesia 2024).'),
  gambar(diagram('gambar-2-3-flowmap-pendaftaran.png'), 9, 18),
  keteranganGambar('Gambar 2.3 Alur Prosedur Pendaftaran LKS yang Sedang Berjalan'),

  subSubBab('2.3.2 Prosedur Pendataan Pengurus dan Klien'),
  paragraf('Tiap LKS menyimpan data pengurus dan klien di buku induk atau lembar kerjanya sendiri. Saat dinas memerlukan rekap, petugas meminta data kepada tiap lembaga. Lembaga mengirim data dalam format masing-masing, lalu petugas menyalinnya ke satu lembar kerja gabungan dan menyimpannya di komputer (Gambar 2.4).'),
  gambar(diagram('gambar-2-4-flowmap-data-klien.png'), 8, 18),
  keteranganGambar('Gambar 2.4 Alur Prosedur Pendataan Pengurus dan Klien yang Sedang Berjalan'),

  subSubBab('2.3.3 Prosedur Pelaporan dan Surat'),
  paragraf('Pengurus LKS menyusun laporan kegiatan atau surat, lalu mengantarnya ke kantor atau mengirimnya lewat WhatsApp. Petugas membaca berkas dan menyampaikan catatan bila perlu perbaikan. Pengurus mengirim perbaikan sebagai berkas baru. Petugas mencatat penerimaan di buku atau lembar kerja, lalu mengarsipkan berkas (Gambar 2.5). Kewajiban melapor ini berasal dari surat pernyataan kesanggupan melaporkan kegiatan yang LKS serahkan saat mendaftar (Kementerian Sosial Republik Indonesia 2024).'),
  gambar(diagram('gambar-2-5-flowmap-laporan-surat.png'), 9, 18),
  keteranganGambar('Gambar 2.5 Alur Prosedur Pelaporan dan Surat yang Sedang Berjalan'),

  subSubBab('2.3.4 Analisis Hak Akses'),
  paragraf('Tabel 2.3 membandingkan hak akses pada prosedur berjalan dengan kewenangan yang diusulkan untuk tiap jenis data. Perbandingan itu memisahkan kondisi sebelum SiDaya dengan pembagian hak pada aplikasi. Hasilnya menjadi dasar pembatasan akses pada perancangan sistem.'),
  keteranganTabel('Tabel 2.3 Hak Akses pada Prosedur Berjalan dan Kewenangan yang Diusulkan'),
  tabel([1900, 2900, 3137], [
    ['Jenis Data', 'Kondisi Berjalan', 'Kewenangan Usulan'],
    ['Pendaftaran LKS', 'Petugas penerima dan pembuka lembar kerja dapat melihat berkas', 'Pengunjung mengirim, dan Admin membaca serta memutuskan'],
    ['Pengurus dan klien', 'Data semua lembaga berada dalam satu lembar kerja', 'LKS pemilik mengelola, dan Admin hanya membaca'],
    ['Laporan semester', 'Berkas beredar melalui WhatsApp dan arsip kantor', 'LKS menyusun, dan Admin membaca setelah LKS mengirim'],
    ['Surat', 'Berkas beredar melalui WhatsApp dan arsip kantor', 'Dinas dan LKS terkait membaca dan mengunduh'],
  ]),
  jarakSetelahTabel(),

  subBab('2.4 Temuan Permasalahan'),
  paragraf('Tabel 2.4 merangkum temuan dan dampaknya. Temuan tersebut berasal dari observasi, wawancara, dan studi dokumentasi. Tiap temuan menjadi dasar fitur yang diusulkan pada subbab 2.5. {{[nanti diisi: cocokkan isi tabel dengan hasil wawancara]}}'),
  keteranganTabel('Tabel 2.4 Temuan Permasalahan dan Dampaknya'),
  tabel([600, 3669, 3668], [
    ['No', 'Temuan', 'Dampak'],
    ['1', 'Data LKS tersebar di berkas fisik, WhatsApp, dan lembar kerja', 'Petugas memerlukan waktu lama untuk menyusun rekap'],
    ['2', 'Tiap lembaga memakai format data sendiri', 'Petugas menyalin ulang data dan berisiko membuat data ganda'],
    ['3', 'Pengurus tidak dapat melihat status pendaftaran dan laporan', 'Pengurus harus bertanya berulang kepada petugas'],
    ['4', 'Dinas belum membatasi akses pada lembar kerja gabungan', 'Pihak yang tidak berwenang dapat membaca data klien'],
    ['5', 'Dinas belum mencatat riwayat perubahan data dan laporan', 'Petugas sulit menelusuri versi terakhir data'],
  ]),
  jarakSetelahTabel(),
  paragraf('Penelitian lain mencatat masalah serupa. Di Kabupaten Banyuwangi, pendataan penyandang masalah kesejahteraan sosial dengan Microsoft Excel menimbulkan duplikasi data, kesulitan menggabungkan data per desa, dan waktu pencarian yang lama (Ridla dan Baha\'uddin 2024). Masalah itu sejalan dengan temuan nomor 1 dan 2 pada Tabel 2.4.'),

  subBab('2.5 Solusi Pemecahan Masalah'),
  paragraf('SiDaya diusulkan untuk mengatasi temuan tersebut, dengan pemetaan temuan dan fitur pada Tabel 2.5. Pembatasan hak akses mengikuti prinsip RBAC, yang pada sistem berbasis Laravel menampilkan menu sesuai peran dan membatasi akses data (Kusumadewa dkk. 2026). Pendaftaran lewat SiDaya juga memenuhi ketentuan permohonan tanda pendaftaran LKS melalui sistem dalam jaringan yang diuraikan pada subbab 2.3.1.'),
  keteranganTabel('Tabel 2.5 Pemetaan Temuan dan Solusi pada SiDaya'),
  tabel([600, 2700, 4637], [
    ['No', 'Temuan', 'Solusi pada SiDaya'],
    ['1', 'Data tersebar', 'Dinas dan LKS menyimpan data dalam satu basis data yang berisi 13 tabel'],
    ['2', 'Format data berbeda', 'Pengguna mengisi formulir yang sama, dan aplikasi memvalidasi tiap isian'],
    ['3', 'Status sulit dilihat', 'Aplikasi menampilkan status pendaftaran dan laporan, serta mengirim notifikasi kepada penerima'],
    ['4', 'Akses tidak terbatas', '*Middleware* EnsurePeran memeriksa peran, dan aplikasi membatasi data LKS berdasarkan lembaga pada sesi pengguna'],
    ['5', 'Riwayat tidak tercatat', 'Aplikasi menyimpan riwayat klien dan laporan, serta menolak perubahan yang bentrok melalui kolom versi'],
  ]),
  jarakSetelahTabel(),

  judulHalaman('DAFTAR PUSTAKA BAB II'),
  pustaka('Anggi, N. D., & Ilhadi, V. (2026). Sistem informasi pengajuan bantuan penyandang disabilitas berbasis web pada Dinas Sosial Kota Lhokseumawe. *Jurnal Manajemen Informatika Jayakarta*, *6*(1), 46–55. https://doi.org/10.52362/jmijayakarta.v6i1.2267'),
  pustaka('ANTARA News Kalimantan Selatan. (2017, 7 Maret). *Banjarbaru hilangkan kesan angker eks bangunan RSUD*. https://kalsel.antaranews.com/berita/44045/banjarbaru-hilangkan-kesan-angker-eks-bangunan-rsud'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-a). *Bidang Pemberdayaan Sosial*. https://dinsos.banjarbarukota.go.id/bid-pemberdayaan-sosial/'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-b). *Data LKSA/Panti Anak*. https://dinsos.banjarbarukota.go.id/data-lksa-panti-anak/'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-c). *Visi dan Misi*. https://dinsos.banjarbarukota.go.id/visi-dan-misi/'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-d). *Profil Dinas Sosial Kota Banjarbaru*.'),
  pustaka('Kusumadewa, S., Azinar, A. W., Sumarno, & Fitrani, A. S. (2026). Implementasi role-based access control (RBAC) pada sistem manajemen data pekerja berbasis framework Laravel. *JATI (Jurnal Mahasiswa Teknik Informatika)*, *10*(5), 8085–8092. https://doi.org/10.36040/jati.v10i5.19234'),
  pustaka('Kementerian Sosial Republik Indonesia. (2024). *Peraturan Menteri Sosial Republik Indonesia Nomor 5 Tahun 2024 tentang Lembaga Kesejahteraan Sosial*.'),
  pustaka('Pemerintah Kota Banjarbaru. (2008). *Peraturan Daerah Kota Banjarbaru Nomor 11 Tahun 2008 tentang Pembentukan, Organisasi dan Tata Kerja Dinas Daerah di Lingkungan Pemerintah Kota Banjarbaru*.'),
  pustaka('Pemerintah Kota Banjarbaru. (2016). *Peraturan Daerah Kota Banjarbaru Nomor 10 Tahun 2016 tentang Pembentukan dan Susunan Perangkat Daerah Kota Banjarbaru*.'),
  pustaka('Pemerintah Kota Banjarbaru. (2021). *Peraturan Wali Kota Banjarbaru Nomor 73 Tahun 2021 tentang Kedudukan, Susunan Organisasi, Tugas Pokok dan Fungsi serta Tata Kerja Dinas Sosial Kota Banjarbaru*.'),
  pustaka('Pemerintah Kota Banjarbaru. (2023). *Peraturan Wali Kota Banjarbaru Nomor 20 Tahun 2023 tentang Perubahan Peraturan Wali Kota Banjarbaru Nomor 73 Tahun 2021*.'),
  pustaka('Ridla, M. A., & Baha\'uddin, M. (2024). Perancangan sistem informasi pendataan penyandang masalah kesejahteraan sosial (PMKS) berbasis web di Kab. Banyuwangi. *JUSTIFY: Jurnal Sistem Informasi Ibrahimy*, *3*(1), 66–73. https://doi.org/10.35316/justify.v3i1.5303'),
  pustaka('Sinlae, F., Irwanda, E., Maulana, Z., & Syahputra, V. E. (2024). Penggunaan framework Laravel dalam membangun aplikasi website berbasis PHP. *Jurnal Siber Multi Disiplin*, *2*(2), 119–132. https://doi.org/10.38035/jsmd.v2i2.186'),
];

module.exports = { isi };

if (require.main === module) {
  const tujuan = path.join(__dirname, '..', 'Bab-II-Hasil-Pelaksanaan-PKL.docx');
  if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
    console.error('Bab-II-Hasil-Pelaksanaan-PKL.docx sudah ada. Isian kuning yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
    process.exit(1);
  }
  Packer.toBuffer(dokumen({ judul: 'Bab II Hasil Pelaksanaan PKL', halamanAwal: 8, isi })).then((isiBerkas) => {
    fs.writeFileSync(tujuan, isiBerkas);
    console.log(tujuan);
  });
}
