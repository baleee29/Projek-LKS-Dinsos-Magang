const fs = require('fs');
const path = require('path');
const { Packer } = require('docx');
const { butirNomor, dokumen, judulBab, judulHalaman, paragraf, pustaka, subBab } = require('./format');

const isi = [
  judulBab('BAB IV', 'PENUTUP'),

  subBab('4.1 Kesimpulan'),
  paragraf('Berdasarkan hasil PKL dan tujuan pada subbab 1.4.1, kesimpulan laporan ini sebagai berikut.'),
  butirNomor('1. Prosedur pendataan dan pelaporan LKS di Bidang Pemberdayaan Sosial terdiri atas pendaftaran, pendataan pengurus dan klien, serta pelaporan dan surat. Data dari tiap LKS masuk melalui berkas fisik, WhatsApp, dan lembar kerja, lalu petugas menyalinnya ke lembar kerja gabungan. Akibatnya, rekapitulasi memakan waktu lama, data dapat tercatat ganda, dan akses data klien belum dibatasi.'),
  butirNomor('2. Rancangan SiDaya terdiri atas tiga aktor, 17 *use case*, dan 13 tabel basis data. RBAC membagi hak Pengunjung, LKS, dan Admin, sehingga pengurus LKS hanya mengelola data lembaganya sendiri, sedangkan Admin membaca data lintas lembaga.'),
  butirNomor('3. SiDaya dibangun dengan Laravel 13, Blade, dan MySQL atau MariaDB. Aplikasi menyediakan pendaftaran LKS secara daring, pengelolaan profil lembaga, pengurus, klien, laporan semester, dan surat, notifikasi, serta lima laporan dalam bentuk PDF dan Excel.'),
  butirNomor('4. Pengujian kotak hitam atas 41 skenario memberi hasil sesuai harapan, dan 52 kasus uji PHPUnit lulus dengan 543 pemeriksaan. Aplikasi menolak pengurus LKS yang membuka halaman Admin atau data lembaga lain, serta menolak Admin yang membuka laporan draf. Pengujian berjalan di komputer lokal dengan data contoh fiktif.'),

  subBab('4.2 Saran'),
  paragraf('Berdasarkan kesimpulan tersebut, saran untuk dinas dan pengembang berikutnya sebagai berikut.'),
  butirNomor('1. Dinas perlu memasang SiDaya pada *server* produksi melalui Dinas Komunikasi dan Informatika dengan koneksi HTTPS dan cadangan basis data yang rutin. Sebelum aplikasi dipakai, kerentanannya perlu dinilai dengan panduan NIST SP 800-115 dan OWASP (Darojat dkk. 2022).'),
  butirNomor('2. Dinas perlu melatih petugas dan pengurus LKS sebelum memakai aplikasi. Keterbatasan sumber daya manusia dan kurangnya pelatihan teknis tercatat sebagai penghambat penerapan sistem pemerintahan berbasis elektronik (Sumarhaeti 2025).'),
  butirNomor('3. Dinas perlu menyiapkan anggaran dan infrastruktur untuk pemeliharaan SiDaya. Kesiapan pemerintahan elektronik di Kabupaten Balangan, Kalimantan Selatan, masih terhambat infrastruktur, anggaran, dan sumber daya manusia yang kompeten (Maisyarah dkk. 2024).'),
  butirNomor('4. Pengujian berikutnya perlu melibatkan petugas dan pengurus LKS untuk mengukur kepuasan pengguna, misalnya dengan *System Usability Scale* (Purba dan Hidayasari 2026).'),
  butirNomor('5. Isi laporan semester dapat disesuaikan dengan format laporan LKS pada lampiran Peraturan Menteri Sosial Nomor 5 Tahun 2024, yang mencakup kegiatan, keuangan, donor, sumber daya manusia, aset, serta sarana dan prasarana (Kementerian Sosial Republik Indonesia 2024).'),
  butirNomor('6. Pengembang perlu memindahkan proses penyaringan dan pembagian halaman daftar dari PHP ke kueri SQL, agar aplikasi tetap cepat saat data LKS bertambah.'),
  butirNomor('7. Pengurus LKS perlu dapat mengganti kata sandi sendiri dan memulihkannya lewat surel, tanpa meminta Admin mengatur ulang sandi.'),
  butirNomor('8. Dialog surat perlu menampilkan pratinjau PDF dan gambar, dan dasbor Admin dapat menampilkan grafik perkembangan jumlah klien LKS antarsemester.'),

  judulHalaman('DAFTAR PUSTAKA BAB IV'),
  pustaka('Sumarhaeti, H. (2025). Implementasi kebijakan sistem pemerintahan berbasis elektronik di Kabupaten Cianjur. *Journal of Indonesian Rural and Regional Government*, *9*(2), 241–248. https://doi.org/10.47431/jirreg.v9i2.715'),
  pustaka('Darojat, E. Z., Sediyono, E., & Sembiring, I. (2022). Vulnerability assessment website e-government dengan NIST SP 800-115 dan OWASP menggunakan web vulnerability scanner. *Jurnal Sistem Informasi Bisnis*, *12*(1), 36–44. https://doi.org/10.21456/vol12iss1pp36-44'),
  pustaka('Maisyarah, Roziqin, A., Nugroho, R. A., & Te, A. Y. C. (2024). Examining of e-readiness in the local government toward smart city: A study from Balangan Regency. *Jurnal Ilmu Sosial*, *23*(1), 37–63. https://doi.org/10.14710/jis.23.1.2024.37-63'),
  pustaka('Malioy, R. J., Sediyono, E., & Maria, E. (2024). Evaluasi kepuasan pengguna website menggunakan framework PIECES: Studi pada Pemerintah Kabupaten Maluku Barat Daya. *Jurnal Sistem Informasi Bisnis*, *14*(2), 171–180. https://doi.org/10.21456/vol14iss2pp171-180'),
  pustaka('Triandini, E., Fauzan, R., Siahaan, D. O., Rochimah, S., Suardika, I. G., & Karolita, D. (2022). Software similarity measurements using UML diagrams: A systematic literature review. *Register: Jurnal Ilmiah Teknologi Sistem Informasi*, *8*(1), 10–23. https://doi.org/10.26594/register.v8i1.2248'),
];

module.exports = { isi };

if (require.main === module) {
  const tujuan = path.join(__dirname, '..', 'Bab-IV-Penutup.docx');
  if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
    console.error('Bab-IV-Penutup.docx sudah ada. Isian kuning yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
    process.exit(1);
  }
  Packer.toBuffer(dokumen({ judul: 'Bab IV Penutup', halamanAwal: 64, isi })).then((isiBerkas) => {
    fs.writeFileSync(tujuan, isiBerkas);
    console.log(tujuan);
  });
}
