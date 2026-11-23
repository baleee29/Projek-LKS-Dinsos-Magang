// Menulis berkas Mermaid Bab III ke folder diagram. Jalankan: node diagram-bab-3.js, lalu render dengan mmdc.
const fs = require('fs');
const path = require('path');

const FOLDER = path.join(__dirname, '..', 'diagram');

const form = (judul, isian, tombol) => [
  'block-beta',
  '  columns 2',
  `  judul["<b>${judul}</b>"]:2`,
  ...isian.flatMap(([label, contoh], i) => [`  l${i}["${label}"]`, `  i${i}["${contoh}"]`]),
  `  tombol["${tombol}"]:2`,
  '  style judul fill:#d9d9d9,stroke:#000',
  '  style tombol fill:#d9d9d9,stroke:#000',
].join('\n');

const laporan = (judul, kolom) => [
  'block-beta',
  `  columns ${kolom.length}`,
  `  a["<b>PEMERINTAH KOTA BANJARBARU, DINAS SOSIAL</b>"]:${kolom.length}`,
  `  b["<b>${judul}</b>"]:${kolom.length}`,
  ...kolom.map((k, i) => `  k${i}["${k}"]`),
  ...kolom.map((k, i) => `  x${i}["xxx"]`),
  ...kolom.map((k, i) => `  y${i}["xxx"]`),
  '  style a fill:#d9d9d9,stroke:#000',
  '  style b fill:#d9d9d9,stroke:#000',
].join('\n');

const DIAGRAM = {
  'gambar-3-1-arsitektur': `flowchart TD
    P["Pengunjung, pengurus LKS, dan Admin"] --> B["Peramban: halaman Blade,<br/>JavaScript, dan ekspor PDF atau Excel"]
    B -->|Permintaan HTTP| R["Rute Laravel"]
    R --> M["Middleware EnsurePeran:<br/>sesi, status akun, dan peran"]
    M -->|Peran tidak cocok| T["Tolak dengan HTTP 403"]
    M -->|Lolos| C["Controller dan validasi"]
    C --> S["Model dan layanan berkas"]
    S --> D[("Basis data MySQL atau MariaDB<br/>13 tabel aplikasi")]
    C --> V["Tampilan Blade atau data JSON"]
    V --> B`,

  'gambar-3-2-use-case-pengunjung-lks': `flowchart LR
    Pengunjung(["Pengunjung"])
    LKS(["LKS"])
    Pengunjung --- P1(["UC-01 Melihat informasi publik"])
    Pengunjung --- P2(["UC-02 Mengirim pendaftaran LKS"])
    LKS --- L3(["UC-03 Masuk dan keluar"])
    LKS --- L4(["UC-04 Mengelola notifikasi"])
    LKS --- L5(["UC-05 Mengelola profil lembaga"])
    LKS --- L6(["UC-06 Mengelola pengurus dan SDM"])
    LKS --- L7(["UC-07 Mengelola klien"])
    LKS --- L8(["UC-08 Mengelola laporan semester"])
    LKS --- L9(["UC-09 Mengelola surat"])
    LKS --- L16(["UC-16 Melihat ringkasan dan panduan"])
    LKS --- L17(["UC-17 Mengekspor data"])`,

  'gambar-3-3-use-case-admin': `flowchart LR
    Admin(["Admin"])
    Admin --- A3(["UC-03 Masuk dan keluar"])
    Admin --- A4(["UC-04 Mengelola notifikasi"])
    Admin --- A9(["UC-09 Mengelola surat"])
    Admin --- A10(["UC-10 Meninjau pendaftaran LKS"])
    Admin --- A11(["UC-11 Mengelola akun LKS"])
    Admin --- A12(["UC-12 Melihat data lintas LKS"])
    Admin --- A13(["UC-13 Melihat laporan terkirim"])
    Admin --- A14(["UC-14 Mengelola isi landing page"])
    Admin --- A15(["UC-15 Mengelola akun Admin"])
    Admin --- A16(["UC-16 Melihat ringkasan dan panduan"])
    Admin --- A17(["UC-17 Mengekspor data"])`,

  'gambar-3-4-aktivitas-masuk': `flowchart TD
    A(["Mulai"]) --> B["Mengisi email atau username<br/>dan kata sandi"]
    B --> C{"Cocok?"}
    C -->|Tidak| D["Menampilkan pesan kesalahan<br/>dan membatasi percobaan"]
    D --> B
    C -->|Ya| E{"Aktif?"}
    E -->|Tidak| F["Menolak akun yang dicabut"]
    F --> Z(["Selesai"])
    E -->|Ya| G{"Ganti sandi?"}
    G -->|Ya, hanya Admin| H["Mengganti kata sandi"]
    H --> I
    G -->|Tidak| I{"Peran"}
    I -->|admin| J["Membuka dasbor Admin"]
    I -->|lks| K["Membuka dasbor LKS"]
    J --> Z
    K --> Z`,

  'gambar-3-5-aktivitas-akun': `flowchart TD
    A(["Mulai"]) --> B{"Aksi Admin"}
    B -->|Buat akun| C{"Dari<br/>pendaftaran?"}
    C -->|Ya| D{"Berakun?"}
    D -->|Ya| E["Menolak akun ganda"]
    D -->|Tidak| F["Memakai lembaga<br/>dari pendaftaran"]
    C -->|Tidak| G["Membuat lembaga baru"]
    F --> H["Menyimpan akun dengan<br/>kata sandi sementara"]
    G --> H
    B -->|Reset sandi| J["Menyimpan sandi baru<br/>dan mengakhiri sesi"]
    B -->|Cabut akun| M["Menonaktifkan akun<br/>dan mengakhiri sesi"]
    H --> I["Menampilkan sandi sekali<br/>untuk WhatsApp"]
    J --> I
    E --> Z(["Selesai"])
    I --> Z
    M --> Z`,

  'gambar-3-6-aktivitas-laporan': `flowchart TD
    A(["Mulai"]) --> B["LKS membuka periode semester"]
    B --> C["LKS mengisi laporan dan lampiran"]
    C --> D{"Tombol"}
    D -->|Simpan draf| E{"Status saat ini"}
    E -->|Draf| F["Menyimpan ke isi laporan"]
    E -->|Terkirim| G["Menyimpan ke draf privat"]
    F --> C
    G --> C
    D -->|Kirim| H["Menyalin isi, mengubah status<br/>menjadi Terkirim, dan mencatat riwayat"]
    H --> I["Mengirim notifikasi ke Admin"]
    I --> J["Admin membaca dan mengunduh"]
    J --> Z(["Selesai"])`,

  'gambar-3-7-urutan-pendaftaran': `sequenceDiagram
    actor P as Pengunjung
    actor A as Admin
    participant S as SiDaya
    participant D as Basis Data
    P->>S: Kirim isian dan 16 berkas
    S->>S: Validasi isian dan berkas
    S->>D: Simpan pendaftaran, berkas, notifikasi
    S-->>P: Pendaftaran diterima sistem
    A->>S: Buka detail dan unduh berkas
    alt Menerima
        A->>S: Terima
        S->>D: Buat lembaga dan ubah status
        S-->>A: Tawarkan pembuatan akun
    else Menolak
        A->>S: Tolak dengan alasan
        S->>D: Ubah status dan simpan alasan
        S-->>A: Tautan WhatsApp berisi alasan
    end`,

  'gambar-3-8-urutan-data-lks': `sequenceDiagram
    actor L as LKS
    participant S as SiDaya
    participant D as Basis Data
    actor A as Admin
    L->>S: Kirim isian dan versi data
    S->>S: Ambil lembaga dari sesi
    S->>S: Validasi isian
    alt Isian tidak valid
        S-->>L: Tampilkan kesalahan
    else Isian valid
        S->>D: Baca dan kunci baris
        alt Versi berbeda
            S-->>L: Konflik 409
        else Versi sama
            S->>D: Simpan data dan riwayat
            S-->>L: Berhasil
        end
    end
    A->>S: Buka Data LKS
    S->>D: Baca lintas lembaga
    S-->>A: Tampilkan tanpa tombol ubah`,

  'gambar-3-9-urutan-surat': `sequenceDiagram
    actor P as Pengirim
    participant S as SiDaya
    participant D as Basis Data
    actor R as Penerima
    P->>S: Kirim data surat dan berkas
    S->>S: Tentukan arah dari peran
    S->>D: Simpan berkas, surat, notifikasi
    S-->>P: Berhasil
    R->>S: Buka surat dan tandai dibaca
    S->>D: Isi waktu dibaca
    R->>S: Unduh berkas
    S->>S: Periksa hak akses
    S->>D: Baca potongan berkas
    S-->>R: Kirim berkas`,

  'gambar-3-10-kelas': `classDiagram
    direction TB
    class Lembaga { +uuid id
      +string nama
      +int versi }
    class Pengguna { +bigint id
      +string email
      +string peran
      +bool aktif }
    class PendaftaranLks { +uuid id
      +string nama_lembaga
      +string status }
    class BerkasPendaftaran { +uuid id
      +string kunci_berkas }
    class PengurusLks { +uuid id
      +string nama
      +string kategori }
    class KlienLks { +uuid id
      +string kode
      +string nama }
    class LaporanSemester { +uuid id
      +int tahun
      +int semester
      +string status }
    class SuratLks { +uuid id
      +string arah
      +string perihal }
    class BerkasTersimpan { +uuid id
      +string jenis_sumber
      +string sha256 }
    Lembaga "1" --> "*" Pengguna
    Lembaga "1" --> "*" PengurusLks
    Lembaga "1" --> "*" KlienLks
    Lembaga "1" --> "*" LaporanSemester
    Lembaga "1" --> "*" SuratLks
    Lembaga "0..1" --> "*" PendaftaranLks
    PendaftaranLks "1" --> "*" BerkasPendaftaran
    BerkasPendaftaran "1" --> "1" BerkasTersimpan
    SuratLks "*" --> "0..1" BerkasTersimpan`,

  'gambar-3-11-daftar-tabel': `flowchart TD
    subgraph G1["Akun dan sesi"]
        T1["pengguna"]
        T13["sesi"]
        T12["notifikasi"]
    end
    subgraph G2["Lembaga dan pendaftaran"]
        T2["lembaga"]
        T3["pendaftaran_lks"]
        T4["berkas_pendaftaran"]
    end
    subgraph G3["Pelayanan dan pelaporan"]
        T5["pengurus_lks"]
        T6["klien_lks"]
        T7["laporan_semester"]
        T8["surat_lks"]
    end
    subgraph G4["Berkas dan halaman publik"]
        T9["berkas_tersimpan"]
        T10["potongan_berkas"]
        T11["isi_landing"]
    end
    T1 ~~~ T13 ~~~ T12
    T2 ~~~ T3 ~~~ T4
    T5 ~~~ T6 ~~~ T7 ~~~ T8
    T9 ~~~ T10 ~~~ T11`,

  'gambar-3-12-erd': `erDiagram
    lembaga ||--o{ pengguna : "memiliki akun"
    lembaga |o--o{ pendaftaran_lks : "berasal dari"
    lembaga ||--o{ pengurus_lks : "memiliki"
    lembaga ||--o{ klien_lks : "melayani"
    lembaga ||--o{ laporan_semester : "melaporkan"
    lembaga ||--o{ surat_lks : "bertukar"
    lembaga |o--o{ berkas_tersimpan : "memiliki"
    pendaftaran_lks ||--o{ berkas_pendaftaran : "melampirkan"
    pengguna ||--o{ notifikasi : "menerima"
    pengguna |o--o{ sesi : "membuka"
    pengguna |o--o{ isi_landing : "memperbarui"
    berkas_tersimpan ||--|{ potongan_berkas : "terdiri dari"
    berkas_tersimpan ||--o| berkas_pendaftaran : "isi dari"`,

  'gambar-3-13-struktur-menu': `flowchart LR
    S["SiDaya"] --> P["Halaman publik"]
    P --> P1["Beranda, Profil, Pilar Sosial,<br/>LKS, Kontak"]
    P --> P2["Pendaftaran LKS"]
    S --> A["Dasbor Admin"]
    A --> A1["Ringkasan dan Notifikasi"]
    A --> A2["Pendaftaran LKS dan Buat Akun LKS"]
    A --> A3["Data LKS, Rekap Penghuni,<br/>Laporan Semester, Surat"]
    A --> A4["Panduan, Landing Page,<br/>Pengaturan Akun"]
    S --> L["Dasbor LKS"]
    L --> L1["Ringkasan dan Notifikasi"]
    L --> L2["Profil, Pengurus dan SDM, Surat"]
    L --> L3["Tambah Klien dan Rekap Klien"]
    L --> L4["Laporan Semester dan Panduan"]`,

  'gambar-3-14-rancang-masuk': form('Masuk ke Akun', [
    ['Email atau username', 'Isian teks'], ['Kata sandi', 'Isian sandi'],
  ], 'Masuk'),
  'gambar-3-15-rancang-pendaftaran': form('Pendaftaran LKS', [
    ['Nama lembaga', 'Isian teks'], ['Nama pengisi data', 'Isian teks'], ['Nomor WhatsApp', 'Isian angka'],
    ['Kecamatan dan kelurahan', 'Pilihan wilayah'], ['Nomor izin dan rekening', 'Isian teks'],
    ['Bidang fokus', 'Isian panjang'], ['Pilihan pelayanan', 'Lima kelompok centang'], ['Berkas 1 sampai 16', 'Unggah berkas'],
  ], 'Periksa Ringkasan dan Ajukan'),
  'gambar-3-16-rancang-pengurus': form('Tambah Personel LKS', [
    ['Foto profil', 'Unggah gambar'], ['Nama dan NIK', 'Isian teks'], ['Tempat dan tanggal lahir', 'Isian teks dan tanggal'],
    ['Jenis kelamin dan kontak', 'Pilihan dan isian'], ['Alamat KTP dan NPWP', 'Isian teks'],
    ['Kategori dan jabatan', 'Pilihan dan isian'], ['Tanggal bergabung', 'Isian tanggal'], ['Berkas pendukung', 'Unggah berkas'],
  ], 'Simpan'),
  'gambar-3-17-rancang-klien': form('Tambah Klien', [
    ['Bagian A: identitas klien', 'Foto, nama, NIK, nomor KK, lahir'], ['Bagian B: keluarga dan wali', 'Wali, kontak, alamat asal'],
    ['Bagian C: layanan dan pendidikan', 'Bentuk layanan, status, sekolah'], ['Bagian D: penempatan dan pendamping', 'Rujukan, rencana, dokumen'],
  ], 'Simpan Klien'),
  'gambar-3-18-rancang-laporan': form('Laporan Semester', [
    ['Periode', 'Tahun dan semester'], ['Profil lembaga', 'Terisi dari data lembaga'],
    ['Isi laporan', 'Isian kegiatan dan rekap'], ['Lampiran', 'Unggah berkas'],
  ], 'Simpan Draf atau Kirim'),
  'gambar-3-19-rancang-keluaran-lks': laporan('DAFTAR LEMBAGA KESEJAHTERAAN SOSIAL', ['NO', 'NAMA LEMBAGA', 'KETUA', 'WHATSAPP', 'KECAMATAN', 'KELURAHAN', 'LEGALITAS']),
  'gambar-3-20-rancang-keluaran-penghuni': laporan('REKAP PENGHUNI LKSA', ['NO', 'NAMA', 'L/P', 'NIK', 'NO. KK', 'TTL', 'ALAMAT ASAL', 'STATUS']),
  'gambar-3-21-rancang-keluaran-pengurus': laporan('DATA PENGURUS DAN SDM', ['NO', 'NAMA LENGKAP', 'NIK', 'JABATAN', 'PEKERJAAN']),
  'gambar-3-22-rancang-keluaran-semester': laporan('REKAP LAPORAN SEMESTER', ['NO', 'PERIODE', 'JUDUL', 'TANGGAL', 'BERKAS', 'STATUS', 'CATATAN']),
  'gambar-3-23-rancang-keluaran-surat': laporan('DAFTAR SURAT', ['NO', 'TANGGAL', 'NOMOR SURAT', 'LKS TERKAIT', 'PERIHAL', 'KETERANGAN', 'BERKAS']),
};

Object.entries(DIAGRAM).forEach(([nama, isi]) => fs.writeFileSync(path.join(FOLDER, `${nama}.mmd`), `${isi}\n`));
const dasar = JSON.parse(fs.readFileSync(path.join(FOLDER, 'konfigurasi.json'), 'utf8'));
fs.writeFileSync(path.join(FOLDER, 'konfigurasi-3.json'), JSON.stringify({
  ...dasar,
  flowchart: { ...dasar.flowchart, padding: 6, nodeSpacing: 18, rankSpacing: 16, diagramPadding: 4 },
  sequence: { wrap: true, width: 120, messageMargin: 30, boxMargin: 6, mirrorActors: false },
  er: { fontSize: 14, entityPadding: 10 },
}, null, 2));
console.log(Object.keys(DIAGRAM).length);
