const fs = require('fs');
const path = require('path');
const { Packer } = require('docx');
const {
  dokumen, gambar, jarakSetelahTabel, judulBab, judulHalaman, keteranganGambar, keteranganTabel, paragraf, pustaka,
  subBab, subSubBab, tabel,
} = require('./format');

const diagram = (nama) => path.join(__dirname, '..', 'diagram', `${nama}.png`);
const layar = (nama) => path.join(__dirname, '..', 'tangkapan', `${nama}.png`);
const LEBAR = 7937;

// Menghapus rujukan ke bagian dokumen sumber yang tidak ada di laporan.
const rapikan = (keterangan) => (keterangan || '-')
  .replace(/^Lihat bagian 6\.2$/, 'Menunggu Verifikasi, Disetujui, atau Ditolak')
  .replace(/\s*\(lihat bagian \d+\)/i, '')
  .replace(/; label Admin saat ini "Nama Pengisi Data"/, '');

// Kamus data dibaca dari rancangan database proyek agar sama dengan dokumen sumbernya.
function kamusData() {
  const md = fs.readFileSync(path.join(__dirname, '..', '..', 'docs', 'rancangan-database.md'), 'utf8').replace(/\r\n/g, '\n');
  const bagian = md.slice(md.indexOf('## 5. Rincian kolom'), md.indexOf('## 6. Alur'));
  return [...bagian.matchAll(/### 5\.\d+ `(\w+)`\n([\s\S]*?)(?=\n### |$)/g)].map(([, nama, isi]) => ({
    nama,
    baris: isi.split('\n')
      .filter((l) => l.startsWith('|'))
      .slice(2)
      .map((l) => l.replace(/^\||\|$/g, '').split(' | ').map((s) => s.replace(/`/g, '').replace(/\s*\|\s*$/, '').trim()))
      .map(([kolom, tipe, keterangan]) => [kolom, tipe, rapikan(keterangan)]),
  }));
}

let nomorTabelKamus = 6;
const tabelKamus = kamusData().flatMap(({ nama, baris }) => {
  const nomor = nomorTabelKamus;
  nomorTabelKamus += 1;
  return [
    keteranganTabel(`Tabel 3.${nomor} Kamus Data Tabel ${nama}`),
    tabel([2500, 2300, 3137], [['Kolom', 'Tipe', 'Keterangan'], ...baris]),
    jarakSetelahTabel(),
  ];
});

const isi = [
  judulBab('BAB III', 'ANALISIS DAN PEMECAHAN MASALAH'),

  subBab('3.1 Usulan Sistem Baru'),
  subSubBab('3.1.1 Gambaran Sistem Usulan'),
  paragraf('SiDaya diusulkan sebagai aplikasi *web* tempat Dinas Sosial Kota Banjarbaru dan LKS bertukar data. Pengunjung membaca informasi publik dan mengirim pendaftaran LKS beserta 16 berkas. Admin meninjau pendaftaran lalu membuat akun untuk lembaga yang diterima. Pengurus LKS memakai akun itu untuk mengelola profil, pengurus, klien, laporan semester, dan surat lembaganya.'),
  paragraf('Peramban mengirim permintaan ke rute Laravel, lalu *middleware* EnsurePeran memeriksa sesi, status akun, dan peran pengguna (Gambar 3.1). *Controller* memvalidasi isian dan menyimpan data melalui model ke basis data. Aplikasi menyimpan berkas di basis data dalam potongan 48 KiB, sehingga berkas dan data pemiliknya tersimpan dalam satu transaksi.'),
  gambar(diagram('gambar-3-1-arsitektur'), 11, 15),
  keteranganGambar('Gambar 3.1 Arsitektur SiDaya'),

  subSubBab('3.1.2 Usulan *Hardware* dan *Software*'),
  paragraf('Usulan perangkat keras tercantum pada Tabel 3.1, dan perangkat lunak yang dipakai tercantum pada Tabel 3.2. Perangkat keras pengembangan dipakai untuk membangun dan menguji aplikasi, sedangkan server menjalankan aplikasi bagi pengguna. Perangkat lunak mencakup bahasa pemrograman, *framework*, basis data, dan alat pengujian. Penyediaan server dilakukan melalui Dinas Komunikasi dan Informatika (Kominfo) Kota Banjarbaru, sehingga kolom Server memuat kebutuhan minimum.'),
  paragraf('*Framework* Laravel mempercepat pengembangan karena pengembang menulis lebih sedikit kode dan lebih mudah mengintegrasikan layanan pihak ketiga (Sinlae dkk. 2024). Kecepatan itu penting karena SiDaya dibangun dalam masa PKL yang terbatas. SiDaya memakai fitur bawaan Laravel seperti *middleware*, validasi isian, dan migrasi basis data.'),
  paragraf('Penelitian yang membangun sistem notula dengan Laravel menyimpulkan bahwa *framework* ini cocok bagi pengembang karena pustakanya (*library*) langsung dapat dipakai (Kadim dkk. 2023). Pustaka bawaan yang dipakai SiDaya mencakup autentikasi, sesi, dan notifikasi. Kedua pertimbangan tersebut menjadi dasar pemilihan Laravel untuk SiDaya.'),
  keteranganTabel('Tabel 3.1 Usulan Perangkat Keras'),
  tabel([2400, 2768, 2769], [
    ['Komponen', 'Pengembangan', 'Server'],
    ['Prosesor', 'Intel Core i5-12500H', '2 inti atau lebih'],
    ['Memori', '32 GB', '4 GB atau lebih'],
    ['Penyimpanan', 'SSD NVMe 512 GB', '50 GB SSD atau lebih'],
    ['Jaringan', 'Koneksi internet', 'Koneksi internet dan domain dinas'],
  ]),
  jarakSetelahTabel(),
  keteranganTabel('Tabel 3.2 Perangkat Lunak yang Digunakan'),
  tabel([3000, 4937], [
    ['Perangkat Lunak', 'Kegunaan'],
    ['PHP 8.4 atau lebih baru', 'Bahasa pemrograman sisi server'],
    ['Laravel 13', '*Framework* aplikasi'],
    ['Blade dan JavaScript', 'Tampilan dan interaksi halaman'],
    ['MySQL atau MariaDB', 'Basis data'],
    ['Composer dan Node.js 22', 'Pengelola paket dan pembuat aset'],
    ['Vite', 'Penyusun berkas CSS dan JavaScript'],
    ['jsPDF', 'Pembuat laporan PDF di peramban'],
    ['PHPUnit', 'Pengujian otomatis'],
    ['Mermaid', 'Pembuat diagram laporan'],
    ['Google Chrome', 'Peramban untuk menjalankan dan menguji aplikasi'],
  ]),
  jarakSetelahTabel(),

  subBab('3.2 Perancangan Model Sistem'),
  paragraf('Sistem dimodelkan dengan *Unified Modeling Language* (UML), yaitu bahasa pemodelan standar untuk menentukan, mencatat, dan membangun perangkat lunak (Triandini dkk. 2022). Diagram pada subbab ini digambar dengan Mermaid dan terdiri atas *use case diagram*, *activity diagram*, *sequence diagram*, dan *class diagram*. Tiap diagram menggambarkan sisi sistem yang berbeda, mulai dari pengguna sampai struktur kelas.'),

  subSubBab('3.2.1 *Use Case Diagram*'),
  paragraf('SiDaya memiliki tiga aktor (Tabel 3.3) dan 17 *use case* (Tabel 3.4). Hubungan keduanya tampak pada Gambar 3.2 dan Gambar 3.3. Mermaid belum menyediakan jenis diagram *use case*, sehingga diagram ini digambar dengan jenis *flowchart*.'),
  keteranganTabel('Tabel 3.3 Deskripsi Aktor'),
  tabel([1700, 6237], [
    ['Aktor', 'Peran'],
    ['Pengunjung', 'Membaca halaman publik dan mengirim pendaftaran LKS tanpa akun'],
    ['LKS', 'Pengurus lembaga yang mengelola data lembaganya sendiri'],
    ['Admin', 'Petugas Dinas Sosial yang meninjau pendaftaran, membaca data lintas lembaga, dan mengelola akun'],
  ]),
  jarakSetelahTabel(),
  gambar(diagram('gambar-3-2-use-case-pengunjung-lks'), 11, 17),
  keteranganGambar('Gambar 3.2 *Use Case Diagram* Pengunjung dan LKS'),
  gambar(diagram('gambar-3-3-use-case-admin'), 11, 17),
  keteranganGambar('Gambar 3.3 *Use Case Diagram* Admin'),
  keteranganTabel('Tabel 3.4 Daftar *Use Case*'),
  tabel([950, 4187, 2800], [
    ['Kode', '*Use Case*', 'Aktor'],
    ['UC-01', 'Melihat informasi publik', 'Pengunjung'],
    ['UC-02', 'Mengirim pendaftaran LKS', 'Pengunjung'],
    ['UC-03', 'Masuk dan keluar', 'LKS, Admin'],
    ['UC-04', 'Mengelola notifikasi', 'LKS, Admin'],
    ['UC-05', 'Mengelola profil lembaga, logo, dan koordinat', 'LKS'],
    ['UC-06', 'Mengelola pengurus dan SDM', 'LKS'],
    ['UC-07', 'Mengelola klien dan rekap penghuni', 'LKS'],
    ['UC-08', 'Mengelola laporan semester', 'LKS'],
    ['UC-09', 'Mengelola surat', 'LKS, Admin'],
    ['UC-10', 'Meninjau pendaftaran LKS', 'Admin'],
    ['UC-11', 'Membuat akun, mengatur ulang sandi, dan mencabut akun LKS', 'Admin'],
    ['UC-12', 'Melihat data lembaga, pengurus, dan klien', 'Admin'],
    ['UC-13', 'Melihat dan mengunduh laporan terkirim', 'Admin'],
    ['UC-14', 'Mengelola isi halaman publik', 'Admin'],
    ['UC-15', 'Mengubah profil dan sandi Admin', 'Admin'],
    ['UC-16', 'Melihat ringkasan dan panduan', 'LKS, Admin'],
    ['UC-17', 'Mengekspor Excel dan PDF', 'LKS, Admin'],
  ]),
  jarakSetelahTabel(),
  paragraf('Tabel 3.5 memuat pembagian hak akses tiap aktor, yang diterapkan pada rute dan kueri data. Pengurus LKS hanya mengelola data lembaganya sendiri. Admin membaca data lintas lembaga, tetapi tidak mengubah profil, pengurus, dan klien milik lembaga.'),
  keteranganTabel('Tabel 3.5 Matriks Hak Akses'),
  tabel([3337, 1300, 1650, 1650], [
    ['Fitur', 'Pengunjung', 'LKS', 'Admin'],
    ['Halaman publik dan pendaftaran', 'Ya', 'Ya', 'Ya'],
    ['Notifikasi', '-', 'Milik sendiri', 'Milik sendiri'],
    ['Profil lembaga', '-', 'Kelola lembaga sendiri', 'Baca'],
    ['Pengurus dan klien', '-', 'Kelola lembaga sendiri', 'Baca'],
    ['Laporan semester', '-', 'Kelola lembaga sendiri', 'Baca yang terkirim'],
    ['Surat', '-', 'Kirim dan baca', 'Kirim dan baca'],
    ['Ekspor data', '-', 'Lembaga sendiri', 'Lintas lembaga'],
    ['Peninjauan pendaftaran', '-', '-', 'Ya'],
    ['Pengelolaan akun LKS', '-', '-', 'Ya'],
    ['Isi halaman publik', '-', '-', 'Ya'],
  ]),
  jarakSetelahTabel(),

  subSubBab('3.2.2 *Activity Diagram*'),
  paragraf('Saat pengguna masuk, aplikasi memeriksa kecocokan data, status akun, kewajiban mengganti sandi, dan peran sebelum membuka dasbor (Gambar 3.4). Data yang salah memunculkan pesan kesalahan, dan aplikasi membatasi percobaan berulang. Akun yang dicabut tidak dapat masuk ke dasbor.'),
  gambar(diagram('gambar-3-4-aktivitas-masuk'), 14, 18),
  keteranganGambar('Gambar 3.4 *Activity Diagram* Masuk ke Aplikasi'),
  paragraf('Admin mengelola akun LKS dengan alur pada Gambar 3.5. Satu pendaftaran hanya dapat menghasilkan satu akun. Saat Admin mengatur ulang sandi atau mencabut akun, aplikasi mengakhiri sesi akun tersebut.'),
  gambar(diagram('gambar-3-5-aktivitas-akun'), 14, 18),
  keteranganGambar('Gambar 3.5 *Activity Diagram* Pengelolaan Akun LKS'),
  paragraf('Perubahan setelah laporan terkirim tersimpan sebagai draf privat, sehingga Admin tetap membaca kiriman terakhir sampai LKS mengirim ulang (Gambar 3.6). Pengurus LKS membuka periode semester, mengisi laporan, lalu menyimpan draf atau mengirimnya. Aplikasi mengirim notifikasi kepada Admin saat laporan terkirim.'),
  gambar(diagram('gambar-3-6-aktivitas-laporan'), 14, 18),
  keteranganGambar('Gambar 3.6 *Activity Diagram* Laporan Semester'),

  subSubBab('3.2.3 *Sequence Diagram*'),
  paragraf('Pendaftaran LKS berakhir pada keputusan Admin (Gambar 3.7). Saat menerima, Admin memicu pembuatan data lembaga. Saat menolak, Admin wajib menulis alasan, dan aplikasi menyiapkan tautan WhatsApp berisi alasan itu.'),
  gambar(diagram('gambar-3-7-urutan-pendaftaran'), 13, 15),
  keteranganGambar('Gambar 3.7 *Sequence Diagram* Pendaftaran LKS'),
  paragraf('Profil, pengurus, dan klien memakai pola penyimpanan yang sama (Gambar 3.8). Aplikasi mengambil identitas lembaga dari sesi dan menolak perubahan dengan kode 409 bila versi data berbeda, sehingga dua pengguna tidak saling menimpa. Admin hanya membaca data tersebut dan tidak memiliki tombol ubah.'),
  gambar(diagram('gambar-3-8-urutan-data-lks'), 13, 15),
  keteranganGambar('Gambar 3.8 *Sequence Diagram* Pengelolaan Data LKS'),
  paragraf('Aplikasi menentukan arah surat dari peran pengirim dan memeriksa hak akses sebelum penerima mengunduh berkas (Gambar 3.9). Aplikasi menyimpan berkas, surat, dan notifikasi dalam satu transaksi. Penerima menandai surat sudah dibaca, lalu aplikasi mencatat waktu bacanya.'),
  gambar(diagram('gambar-3-9-urutan-surat'), 12, 13),
  keteranganGambar('Gambar 3.9 *Sequence Diagram* Surat'),

  subSubBab('3.2.4 *Class Diagram*'),
  paragraf('Lembaga menjadi pusat data pada *class diagram*, karena akun, pengurus, klien, laporan, dan surat merujuk ke kelas ini (Gambar 3.10). Tiap kelas model mewakili satu tabel pada basis data. Pendaftaran LKS terhubung ke berkas pendaftaran, sedangkan surat dapat memiliki satu berkas tersimpan.'),
  gambar(diagram('gambar-3-10-kelas'), 14, 14),
  keteranganGambar('Gambar 3.10 *Class Diagram* SiDaya'),

  subBab('3.3 Perancangan Database'),
  subSubBab('3.3.1 Tabel-Tabel'),
  paragraf('Perancangan basis data menghasilkan 13 tabel aplikasi dengan nama bahasa Indonesia pada mesin MySQL atau MariaDB, yang dikelompokkan menurut fungsi (Gambar 3.11). Kelompok tersebut meliputi akun dan sesi, lembaga dan pendaftaran, pelayanan dan pelaporan, serta berkas dan halaman publik. Pengelompokan ini memudahkan pembaca memahami fungsi tiap tabel.'),
  gambar(diagram('gambar-3-11-daftar-tabel'), 14, 8),
  keteranganGambar('Gambar 3.11 Daftar Tabel Basis Data SiDaya'),

  subSubBab('3.3.2 Kamus Data'),
  paragraf('Rincian kolom tiap tabel tercantum pada Tabel 3.6 sampai Tabel 3.18. Selain kolom itu, tabel umumnya memiliki kolom dibuat_pada dan diperbarui_pada. Kolom versi mencegah perubahan yang bentrok. Tabel notifikasi dan sesi memakai nama kolom bawaan Laravel, karena *framework* menulis langsung ke kolom tersebut.'),
  ...tabelKamus,

  subSubBab('3.3.3 Tabel Relasi'),
  paragraf('Relasi antartabel tampak pada Gambar 3.12, dan Tabel 3.19 mencantumkan kolom penghubungnya. Tabel lembaga menjadi induk bagi akun, pengurus, klien, laporan, dan surat. Tabel berkas tersimpan menjadi induk bagi potongan berkas.'),
  gambar(diagram('gambar-3-12-erd'), 14, 8),
  keteranganGambar('Gambar 3.12 *Entity Relationship Diagram* SiDaya'),
  keteranganTabel('Tabel 3.19 Relasi Antartabel'),
  tabel([2500, 3137, 2300], [
    ['Tabel Induk', 'Kolom pada Tabel Anak', 'Kardinalitas'],
    ['lembaga', 'pengguna.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'pendaftaran_lks.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'pengurus_lks.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'klien_lks.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'laporan_semester.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'surat_lks.lembaga_id', 'Satu ke banyak'],
    ['lembaga', 'berkas_tersimpan.lembaga_id', 'Satu ke banyak'],
    ['pendaftaran_lks', 'berkas_pendaftaran.pendaftaran_id', 'Satu ke banyak'],
    ['pendaftaran_lks', 'pengguna.pendaftaran_id', 'Satu ke satu'],
    ['berkas_tersimpan', 'potongan_berkas.berkas_id', 'Satu ke banyak'],
    ['berkas_tersimpan', 'berkas_pendaftaran.berkas_id', 'Satu ke satu'],
    ['berkas_tersimpan', 'surat_lks.berkas_id', 'Satu ke banyak'],
    ['pengguna', 'notifikasi.notifiable_id', 'Satu ke banyak'],
    ['pengguna', 'sesi.user_id', 'Satu ke banyak'],
  ]),
  jarakSetelahTabel(),

  subBab('3.4 Perancangan Antarmuka Sistem'),
  paragraf('Aplikasi membagi menu menjadi halaman publik, dasbor Admin, dan dasbor LKS (Gambar 3.13). Halaman publik melayani Pengunjung tanpa akun. Dasbor Admin dan dasbor LKS hanya terbuka setelah pengguna masuk sesuai perannya.'),
  gambar(diagram('gambar-3-13-struktur-menu'), 11, 15),
  keteranganGambar('Gambar 3.13 Struktur Menu SiDaya'),

  subSubBab('3.4.1 Perancangan Antarmuka Masukan Sistem'),
  paragraf('Perancangan masukan mencakup lima formulir utama, yaitu masuk (Gambar 3.14), pendaftaran LKS (Gambar 3.15), tambah personel (Gambar 3.16), tambah klien (Gambar 3.17), dan laporan semester (Gambar 3.18). Kolom kiri berisi label isian, dan kolom kanan berisi bentuk isiannya. Rancangan ini menjadi acuan saat formulir dibangun pada aplikasi.'),
  gambar(diagram('gambar-3-14-rancang-masuk'), 8, 6),
  keteranganGambar('Gambar 3.14 Rancangan Antarmuka Masuk'),
  gambar(diagram('gambar-3-15-rancang-pendaftaran'), 10, 12),
  keteranganGambar('Gambar 3.15 Rancangan Antarmuka Pendaftaran LKS'),
  gambar(diagram('gambar-3-16-rancang-pengurus'), 10, 12),
  keteranganGambar('Gambar 3.16 Rancangan Antarmuka Tambah Personel'),
  gambar(diagram('gambar-3-17-rancang-klien'), 12, 7),
  keteranganGambar('Gambar 3.17 Rancangan Antarmuka Tambah Klien'),
  gambar(diagram('gambar-3-18-rancang-laporan'), 9, 7),
  keteranganGambar('Gambar 3.18 Rancangan Antarmuka Laporan Semester'),

  subSubBab('3.4.2 Perancangan Antarmuka Keluaran Sistem'),
  paragraf('Pedoman PKL mensyaratkan minimal lima laporan (Fakultas Teknologi Informasi UNISKA 2020). Perancangan keluaran mencakup lima laporan yang dapat diunduh pengguna dalam bentuk PDF dan Excel (Tabel 3.20), yaitu daftar LKS (Gambar 3.19), rekap penghuni (Gambar 3.20), data pengurus (Gambar 3.21), rekap laporan semester (Gambar 3.22), dan daftar surat (Gambar 3.23). Tiap laporan memuat kepala surat Dinas Sosial dan kolom sesuai jenis datanya.'),
  keteranganTabel('Tabel 3.20 Daftar Laporan Keluaran'),
  tabel([600, 3300, 1700, 2337], [
    ['No', 'Laporan', 'Pengguna', 'Isi Utama'],
    ['1', 'Daftar LKS', 'Admin', 'Lembaga, ketua, dan wilayah'],
    ['2', 'Rekap Penghuni LKSA', 'Admin, LKS', 'Identitas klien per lembaga'],
    ['3', 'Data Pengurus dan SDM', 'LKS', 'Nama, NIK, dan jabatan'],
    ['4', 'Rekap Laporan Semester', 'Admin, LKS', 'Periode, berkas, dan status'],
    ['5', 'Daftar Surat', 'Admin, LKS', 'Nomor, perihal, dan berkas'],
  ]),
  jarakSetelahTabel(),
  gambar(diagram('gambar-3-19-rancang-keluaran-lks'), 14, 5),
  keteranganGambar('Gambar 3.19 Rancangan Laporan Daftar LKS'),
  gambar(diagram('gambar-3-20-rancang-keluaran-penghuni'), 14, 5),
  keteranganGambar('Gambar 3.20 Rancangan Laporan Rekap Penghuni LKSA'),
  gambar(diagram('gambar-3-21-rancang-keluaran-pengurus'), 12, 5),
  keteranganGambar('Gambar 3.21 Rancangan Laporan Data Pengurus dan SDM'),
  gambar(diagram('gambar-3-22-rancang-keluaran-semester'), 13, 5),
  keteranganGambar('Gambar 3.22 Rancangan Laporan Rekap Laporan Semester'),
  gambar(diagram('gambar-3-23-rancang-keluaran-surat'), 14, 5),
  keteranganGambar('Gambar 3.23 Rancangan Laporan Daftar Surat'),

  subBab('3.5 Hasil Tampilan Program/Aplikasi'),
  paragraf('Tampilan berikut berasal dari aplikasi yang berjalan di komputer lokal. Tampilan ini memakai data contoh fiktif agar data warga tidak tampil pada laporan. Bagian masukan memuat formulir utama, dan bagian keluaran memuat ringkasan serta laporan PDF.'),

  subSubBab('3.5.1 Tampilan Antarmuka Masukan Sistem'),
  paragraf('Admin dan pengurus LKS masuk dengan surel atau nama pengguna (*username*) dan kata sandi (Gambar 3.24). Aplikasi memeriksa data tersebut dan membuka dasbor sesuai peran pengguna. Bila data salah, pengguna tetap berada di halaman masuk dan melihat pesan kesalahan.'),
  gambar(layar('login'), 14, 10),
  keteranganGambar('Gambar 3.24 Tampilan Halaman Masuk'),
  paragraf('Pengunjung mengisi data lembaga dan mengunggah 16 berkas tanpa akun (Gambar 3.25). Aplikasi memvalidasi isian dan berkas sebelum menampilkan ringkasan. Setelah pengunjung mengonfirmasi, aplikasi menyimpan pendaftaran dengan status Menunggu Verifikasi.'),
  gambar(layar('pendaftaran-lks'), 14, 10),
  keteranganGambar('Gambar 3.25 Tampilan Formulir Pendaftaran LKS'),
  paragraf('Admin memeriksa data dan berkas pada detail pendaftaran, lalu menerima atau menolaknya (Gambar 3.26). Penolakan wajib menyertakan alasan minimal 10 karakter. Saat Admin menerima pendaftaran, aplikasi membuat data lembaga.'),
  gambar(layar('admin-detail-pendaftaran'), 14, 10),
  keteranganGambar('Gambar 3.26 Tampilan Detail Pendaftaran'),
  paragraf('Admin membuat akun LKS dan dapat mengambil data dari pendaftaran berstatus diterima (Gambar 3.27). Aplikasi dapat membuat nama pengguna dan kata sandi acak. Setelah akun tersimpan, Admin dapat mengirim data masuk lewat WhatsApp.'),
  gambar(layar('admin-akun-lks'), 14, 10),
  keteranganGambar('Gambar 3.27 Tampilan Formulir Akun LKS'),
  paragraf('Pengurus LKS menyimpan data personel (Gambar 3.28) dan klien (Gambar 3.29) tanpa persetujuan Admin. Formulir personel meminta identitas dan jabatan, sedangkan formulir klien terbagi dalam empat bagian. Aplikasi menolak NIK personel yang tidak berjumlah 16 digit.'),
  gambar(layar('lks-tambah-pengurus'), 14, 10),
  keteranganGambar('Gambar 3.28 Tampilan Formulir Tambah Personel'),
  gambar(layar('lks-tambah-klien'), 14, 10),
  keteranganGambar('Gambar 3.29 Tampilan Formulir Tambah Klien'),
  paragraf('Pengurus dapat menyimpan draf laporan semester atau mengirimnya setelah periode berakhir (Gambar 3.30). Aplikasi menonaktifkan tombol kirim selama periode masih berjalan. Setelah pengurus mengirim, Admin menerima notifikasi dan dapat membaca laporan.'),
  gambar(layar('lks-isi-laporan'), 14, 10),
  keteranganGambar('Gambar 3.30 Tampilan Formulir Laporan Semester'),

  subSubBab('3.5.2 Tampilan Antarmuka Keluaran Sistem'),
  paragraf('Admin dan pengurus LKS melihat ringkasan data pada dasbor masing-masing (Gambar 3.31 dan Gambar 3.32). Aplikasi menghasilkan lima laporan PDF, yaitu daftar LKS (Gambar 3.33), rekap penghuni (Gambar 3.34), data pengurus (Gambar 3.35), rekap laporan semester (Gambar 3.36), dan daftar surat (Gambar 3.37). Tiap gambar memperlihatkan halaman pertama laporan.'),
  gambar(layar('admin-ringkasan'), 14, 10),
  keteranganGambar('Gambar 3.31 Tampilan Ringkasan Admin'),
  gambar(layar('lks-ringkasan'), 14, 10),
  keteranganGambar('Gambar 3.32 Tampilan Ringkasan LKS'),
  gambar(layar('laporan-daftar-lks'), 14, 10),
  keteranganGambar('Gambar 3.33 Tampilan Laporan Daftar LKS'),
  gambar(layar('laporan-rekap-penghuni'), 14, 10),
  keteranganGambar('Gambar 3.34 Tampilan Laporan Rekap Penghuni LKSA'),
  gambar(layar('laporan-pengurus'), 14, 10),
  keteranganGambar('Gambar 3.35 Tampilan Laporan Data Pengurus dan SDM'),
  gambar(layar('laporan-semester'), 14, 10),
  keteranganGambar('Gambar 3.36 Tampilan Laporan Rekap Laporan Semester'),
  gambar(layar('laporan-surat'), 14, 10),
  keteranganGambar('Gambar 3.37 Tampilan Laporan Daftar Surat'),

  ...require('./black-box').isi,

  judulHalaman('DAFTAR PUSTAKA BAB III'),
  pustaka('Anggi, N. D., & Ilhadi, V. (2026). Sistem informasi pengajuan bantuan penyandang disabilitas berbasis web pada Dinas Sosial Kota Lhokseumawe. *Jurnal Manajemen Informatika Jayakarta*, *6*(1), 46–55. https://doi.org/10.52362/jmijayakarta.v6i1.2267'),
  pustaka('Fakultas Teknologi Informasi UNISKA. (2020). *Pedoman Praktek Kerja Lapangan Program Sarjana* (Revisi 2.0). Universitas Islam Kalimantan Muhammad Arsyad Al Banjari.'),
  pustaka('Kadim, A. A., Hadjaratie, L., & Muthia. (2023). Implementasi framework Laravel dalam pembuatan sistem pencatatan notula berbasis website. *Jurnal Sistem Informasi Bisnis*, *13*(1), 45–51. https://doi.org/10.21456/vol13iss1pp45-51'),
  pustaka('Kusumadewa, S., Azinar, A. W., Sumarno, & Fitrani, A. S. (2026). Implementasi role-based access control (RBAC) pada sistem manajemen data pekerja berbasis framework Laravel. *JATI (Jurnal Mahasiswa Teknik Informatika)*, *10*(5), 8085–8092. https://doi.org/10.36040/jati.v10i5.19234'),
  pustaka('Sinlae, F., Irwanda, E., Maulana, Z., & Syahputra, V. E. (2024). Penggunaan framework Laravel dalam membangun aplikasi website berbasis PHP. *Jurnal Siber Multi Disiplin*, *2*(2), 119–132. https://doi.org/10.38035/jsmd.v2i2.186'),
];

module.exports = { isi };

if (require.main === module) {
  const tujuan = path.join(__dirname, '..', 'Bab-III-Analisis-dan-Pemecahan-Masalah.docx');
  if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
    console.error('Bab-III-Analisis-dan-Pemecahan-Masalah.docx sudah ada. Isian kuning yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
    process.exit(1);
  }
  Packer.toBuffer(dokumen({ judul: 'Bab III Analisis dan Pemecahan Masalah', halamanAwal: 20, isi })).then((isiBerkas) => {
    fs.writeFileSync(tujuan, isiBerkas);
    console.log(tujuan);
  });
}
