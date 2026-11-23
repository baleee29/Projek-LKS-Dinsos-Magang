const fs = require('fs');
const path = require('path');
const { Packer } = require('docx');
const { butirBab, butirNomor, dokumen, judulBab, judulHalaman, paragraf, pustaka, subBab, subSubBab, uraian, uraianButir } = require('./format');

const JUDUL = 'Rancang Bangun Sistem Informasi Pendataan dan Pelaporan Lembaga Kesejahteraan Sosial pada Dinas Sosial Kota Banjarbaru';

const isi = [
  judulBab('BAB I', 'PENDAHULUAN'),

  subBab('1.1 Latar Belakang'),
  paragraf('Pemerintahan digital menjadi salah satu strategi utama reformasi birokrasi, seiring belanja teknologi informasi pemerintah global yang melampaui 548 miliar dolar AS pada 2022. Indonesia mengembangkannya melalui Sistem Pemerintahan Berbasis Elektronik (SPBE) berdasarkan Peraturan Presiden Nomor 95 Tahun 2018, dan peringkatnya dalam *United Nations E-Government Survey* naik dari 116 pada 2016 menjadi 64 pada 2024. Namun, pengembangan aplikasi yang terpisah antarinstansi menimbulkan tumpang tindih fungsi, fitur, dan data (Hendiyani dan Fansuri 2024).'),
  paragraf('Di tingkat daerah, kematangan SPBE belum merata. Pada evaluasi 2021, hanya 24% dari 391 pemerintah kabupaten/kota mencapai predikat minimal “Baik”, di bawah target 30% (Keban dkk. 2024). Kapasitas organisasi, yang meliputi inovasi, keuangan, dan sumber daya manusia, berpengaruh positif terhadap kematangan tersebut (Keban dkk. 2024). Oleh karena itu, digitalisasi daerah juga bergantung pada kesiapan organisasi pemerintah daerah.'),
  paragraf('Kesiapan organisasi juga diperlukan pada Lembaga Kesejahteraan Sosial (LKS), pelaksana penyelenggaraan kesejahteraan sosial dari unsur masyarakat yang dibina dinas sosial. Studi pada lima LKS di Kabupaten Garut menunjukkan bahwa peraturan dan prosedur kerja belum diterapkan secara ketat, dan sejumlah LKS tidak menyampaikan laporan penyaluran serta penggunaan dana (Huripah dkk. 2021). Pencatatan manual dengan buku atau Microsoft Excel pada sebuah Lembaga Kesejahteraan Sosial Anak juga menimbulkan kesalahan pencatatan, kehilangan data, dan kesulitan menyusun laporan (Tahu dkk. 2024).'),
  paragraf('Kondisi serupa terjadi di Dinas Sosial Kota Banjarbaru. Berdasarkan hasil observasi, pendaftaran, data pengurus dan klien, laporan kegiatan, serta surat dari LKS dikirim melalui berkas fisik, WhatsApp, atau lembar kerja masing-masing lembaga. Petugas lalu menyalinnya ke lembar kerja gabungan, sehingga rekapitulasi memerlukan waktu lama dan data berpotensi tercatat ganda. Akses terhadap data klien, termasuk Nomor Induk Kependudukan, juga belum dibatasi. Di sisi lain, Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi mewajibkan pengendali data, termasuk instansi pemerintah, untuk mencegah akses tidak sah melalui langkah seperti enkripsi dan kontrol akses (Nurmala dan Wiraguna 2026).'),
  paragraf('Sistem informasi berbasis *web* menjadi salah satu solusi atas permasalahan tersebut. Pada Dinas Sosial Kota Lhokseumawe, sistem berbasis Laravel menggantikan pengajuan manual dengan pengelolaan data terpusat. Pengguna sistem itu hanya pemohon dan admin, dan penelitinya menyarankan pengelolaan hak akses yang lebih ketat (Anggi dan Ilhadi 2026). Pembatasan akses dapat diterapkan melalui *Role-Based Access Control* (RBAC), yang pada sistem berbasis Laravel 11 mampu menolak akses di luar kewenangan, termasuk melalui URL langsung (Purba dan Hidayasari 2026). Namun, rujukan tersebut belum membahas sistem pendataan dan pelaporan LKS yang melibatkan pengurus lembaga sebagai pengguna dan memisahkan data antarlembaga.'),
  paragraf('Berdasarkan uraian tersebut, laporan Praktek Kerja Lapangan ini mengangkat judul “Rancang Bangun Sistem Informasi Pendataan dan Pelaporan Lembaga Kesejahteraan Sosial pada Dinas Sosial Kota Banjarbaru”. Sistem bernama SiDaya ini dibangun berbasis Laravel untuk pendaftaran dan pendataan LKS serta pengelolaan laporan dan surat, dengan RBAC bagi Pengunjung, LKS, dan Admin, serta diuji menggunakan PHPUnit dan *black box testing*. Sistem ini diharapkan dapat mempercepat rekapitulasi, mengurangi data ganda, dan membatasi akses terhadap data pribadi klien sesuai peran pengguna.'),

  subBab('1.2 Alasan Pemilihan Judul'),
  paragraf('Judul ini dipilih berdasarkan pertimbangan sebagai berikut.'),
  butirNomor('1. Dinas Sosial Kota Banjarbaru wajib mendata LKS di wilayahnya (Republik Indonesia 2009; Kementerian Sosial Republik Indonesia 2024), dan tugas pembinaan kelembagaan masyarakat berada pada Bidang Pemberdayaan Sosial (Dinas Sosial Kota Banjarbaru t.t.-a). Karena itu, data lembaga, pengurus, dan klien LKS menjadi kebutuhan pokok dinas.'),
  butirNomor('2. Pendataan dan pelaporan LKS masih berjalan melalui berkas fisik, WhatsApp, dan lembar kerja yang disalin petugas ke lembar kerja gabungan, sehingga rekapitulasi memakan waktu lama dan data dapat tercatat ganda. Masalah ini memerlukan sistem informasi yang menghimpun pendataan dan pelaporan dalam satu basis data.'),
  butirNomor('3. Rancang bangun sistem ini sesuai dengan kelompok materi *Database Multiuser* dalam pedoman PKL, karena Admin dinas dan pengurus banyak LKS memakai satu aplikasi *web* dengan satu basis data secara bersamaan (Fakultas Teknologi Informasi UNISKA 2020).'),

  subBab('1.3 Ruang Lingkup'),
  paragraf('Laporan ini membahas rancang bangun SiDaya di Bidang Pemberdayaan Sosial Dinas Sosial Kota Banjarbaru selama masa PKL, yaitu 14 September sampai 14 November 2026. Aplikasi berbasis *web* ini dibangun dengan Laravel dan dipakai tiga aktor, yaitu Pengunjung, pengurus LKS, dan Admin dinas. Fiturnya mencakup pendaftaran LKS, pendataan lembaga, pengurus, dan klien, laporan semester, surat, serta pembatasan akses melalui RBAC.'),
  paragraf('Pengujian aplikasi memakai PHPUnit dan pengujian kotak hitam (*black box testing*). Data yang dikelola terbatas pada data LKS di Kota Banjarbaru. Pemasangan aplikasi pada *server* produksi dan integrasi dengan sistem lain berada di luar pembahasan.'),

  subBab('1.4 Tujuan dan Manfaat'),
  subSubBab('1.4.1 Tujuan'),
  paragraf('Berdasarkan latar belakang yang telah diuraikan, tujuan PKL ini dirumuskan sebagai berikut.'),
  butirNomor('1. Menganalisis prosedur pendataan dan pelaporan LKS yang berjalan di Dinas Sosial Kota Banjarbaru.'),
  butirNomor('2. Merancang sistem informasi pendataan dan pelaporan LKS beserta pembagian hak akses Pengunjung, LKS, dan Admin melalui RBAC.'),
  butirNomor('3. Membangun SiDaya berbasis Laravel untuk pendaftaran LKS, pendataan lembaga, pengurus, dan klien, serta pengelolaan laporan dan surat.'),
  butirNomor('4. Menguji fungsi aplikasi dan batas akses tiap peran menggunakan PHPUnit dan pengujian kotak hitam.'),
  subSubBab('1.4.2 Manfaat'),
  paragraf('Berdasarkan latar belakang yang telah diuraikan, manfaat yang diharapkan dari PKL ini sebagai berikut.'),
  butirNomor('a. Bagi Dinas Sosial Kota Banjarbaru'),
  uraianButir('SiDaya diharapkan membantu petugas mempercepat rekapitulasi data LKS, mengurangi data ganda, dan membatasi akses data pribadi klien sesuai peran pengguna. Laporan ini juga dapat menjadi dokumentasi teknis saat dinas memelihara atau mengembangkan aplikasi.'),
  butirNomor('b. Bagi Lembaga Kesejahteraan Sosial'),
  uraianButir('Pengurus LKS dapat mendaftarkan lembaga, memperbarui data pengurus dan klien, serta mengirim laporan dan surat melalui satu aplikasi tanpa mengantar berkas fisik. Pengurus juga dapat memantau status laporan semester lembaganya.'),
  butirNomor('c. Bagi Mahasiswa'),
  uraianButir('PKL ini memberi pengalaman menerapkan rekayasa perangkat lunak, perancangan basis data, dan keamanan aplikasi *web* pada kebutuhan instansi. Pengalaman tersebut menjadi bekal untuk proyek perangkat lunak berikutnya.'),
  butirNomor('d. Bagi Fakultas Teknologi Informasi UNISKA'),
  uraianButir('Laporan ini dapat menjadi bahan rujukan bagi mahasiswa berikutnya yang membangun sistem informasi pada pemerintah daerah. Fakultas juga dapat memakainya untuk mengevaluasi kesesuaian materi perkuliahan dengan kebutuhan instansi.'),

  subBab('1.5 Sistematika Penulisan'),
  paragraf('Laporan ini terdiri atas empat bab. Bab pertama dan terakhir membuka serta menutup pembahasan, sedangkan dua bab di tengah memuat hasil PKL dan pemecahan masalah. Susunan tiap bab sebagai berikut.'),
  butirBab('BAB I : PENDAHULUAN'),
  uraian('Bab ini memuat latar belakang, alasan pemilihan judul, ruang lingkup, tujuan dan manfaat, serta sistematika penulisan. Latar belakang menjelaskan kebutuhan sistem informasi LKS di Dinas Sosial Kota Banjarbaru. Subbab lainnya menetapkan arah dan batas pembahasan.'),
  butirBab('BAB II : HASIL PELAKSANAAN PKL'),
  uraian('Bab ini memuat lokasi PKL, pengumpulan data, analisis prosedur yang berjalan, temuan permasalahan, dan solusi pemecahan masalah. Uraian prosedur menggambarkan cara dinas dan LKS bertukar data sebelum memakai SiDaya. Temuan pada bab ini menjadi dasar solusi yang diusulkan.'),
  butirBab('BAB III : ANALISIS DAN PEMECAHAN MASALAH'),
  uraian('Bab ini memuat usulan sistem baru, perancangan model sistem, perancangan basis data, perancangan antarmuka, dan hasil tampilan aplikasi. Perancangan memakai diagram UML dan rancangan basis data. Bagian akhir bab memuat tampilan aplikasi dan hasil pengujian.'),
  butirBab('BAB IV : PENUTUP'),
  uraian('Bab ini memuat kesimpulan dan saran untuk pengembangan SiDaya. Kesimpulan menjawab tujuan penulisan pada Bab I. Saran memuat kekurangan aplikasi dan ide pengembangan berikutnya.'),

  judulHalaman('DAFTAR PUSTAKA BAB I'),
  pustaka('Anggi, N. D., & Ilhadi, V. (2026). Sistem informasi pengajuan bantuan penyandang disabilitas berbasis web pada Dinas Sosial Kota Lhokseumawe. *Jurnal Manajemen Informatika Jayakarta*, *6*(1), 46–55. https://doi.org/10.52362/jmijayakarta.v6i1.2267'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-a). *Bidang Pemberdayaan Sosial*. https://dinsos.banjarbarukota.go.id/bid-pemberdayaan-sosial/'),
  pustaka('Dinas Sosial Kota Banjarbaru. (t.t.-b). *Data LKSA/Panti Anak*. https://dinsos.banjarbarukota.go.id/data-lksa-panti-anak/'),
  pustaka('Fakultas Teknologi Informasi UNISKA. (2020). *Pedoman Praktek Kerja Lapangan Program Sarjana* (Revisi 2.0). Universitas Islam Kalimantan Muhammad Arsyad Al Banjari.'),
  pustaka('Kusumadewa, S., Azinar, A. W., Sumarno, & Fitrani, A. S. (2026). Implementasi role-based access control (RBAC) pada sistem manajemen data pekerja berbasis framework Laravel. *JATI (Jurnal Mahasiswa Teknik Informatika)*, *10*(5), 8085–8092. https://doi.org/10.36040/jati.v10i5.19234'),
  pustaka('Pemerintah Kota Banjarbaru. (2021). *Peraturan Wali Kota Banjarbaru Nomor 73 Tahun 2021 tentang Kedudukan, Susunan Organisasi, Tugas Pokok dan Fungsi serta Tata Kerja Dinas Sosial Kota Banjarbaru*.'),
  pustaka('Republik Indonesia. (2009). *Undang-Undang Republik Indonesia Nomor 11 Tahun 2009 tentang Kesejahteraan Sosial*.'),
  pustaka('Ridla, M. A., & Baha\'uddin, M. (2024). Perancangan sistem informasi pendataan penyandang masalah kesejahteraan sosial (PMKS) berbasis web di Kab. Banyuwangi. *JUSTIFY: Jurnal Sistem Informasi Ibrahimy*, *3*(1), 66–73. https://doi.org/10.35316/justify.v3i1.5303'),
  pustaka('Hendiyani, M. F., & Fansuri, H. (2024). Evaluasi kebijakan clearance sistem pemerintahan berbasis elektronik, studi pada: Kementerian dan lembaga. *Jurnal Media Birokrasi*, *6*(2), 214–237. https://doi.org/10.33701/jmb.v6i2.4940'),
  pustaka('Huripah, E., Ramli, & Winarni, E. D. (2021). Strategi kapabilitas relasional bagi lembaga kesejahteraan sosial di Kabupaten Garut: Penguatan aspek kolaborasi dan kemitraan lembaga. *Jurnal Ilmiah Perlindungan dan Pemberdayaan Sosial (Lindayasos)*, *3*(1), 1–19. https://doi.org/10.31595/lindayasos.v3i1.390'),
  pustaka('Keban, Y. T., Cahyadi, D., & Djunaedi, A. (2024). Modelling e-government maturity determinants at the local level in Indonesia using technology-organization-environment framework. *Jurnal Ilmu Sosial dan Ilmu Politik*, *28*(1), 17–34. https://doi.org/10.22146/jsp.81803'),
  pustaka('Nurmala, S. A., & Wiraguna, S. A. (2026). Analisis yuridis tanggung jawab Direktorat Jenderal Pajak (DJP) terhadap kebocoran data Nomor Pokok Wajib Pajak (NPWP) berdasarkan Undang-Undang Nomor 27 Tahun 2022. *Jurnal Kajian Ilmiah*, *26*(1), 85–94. https://ejurnal.ubharajaya.ac.id/index.php/JKI/article/view/4817'),
  pustaka('Purba, D. A., & Hidayasari, N. (2026). Implementation of role-based access control (RBAC) in a drug stock management information system. *Sistemasi: Jurnal Sistem Informasi*, *15*(2), 684–698. https://doi.org/10.32520/stmsi.v15i2.5958'),
  pustaka('Tahu, F. K., Ginting, W., & Faizah, N. M. (2024). Optimalisasi pendataan anak serta donatur Yayasan Kasih Mandiri Bersinar dengan metode prototype. *Design Journal*, *2*(2), 42–50. https://doi.org/10.58477/dj.v2i2.181'),
];

module.exports = { JUDUL, isi };

if (require.main === module) {
  const tujuan = path.join(__dirname, '..', 'Bab-I-Pendahuluan.docx');
  if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
    console.error('Bab-I-Pendahuluan.docx sudah ada. Isian kuning yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
    process.exit(1);
  }
  Packer.toBuffer(dokumen({ judul: 'Bab I Pendahuluan', isi })).then((isiBerkas) => {
    fs.writeFileSync(tujuan, isiBerkas);
    console.log(tujuan);
  });
}
