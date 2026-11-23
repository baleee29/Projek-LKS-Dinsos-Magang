// Subbab 3.5.3 dari hasil uji peramban pada tangkapan/black-box/hasil.json.
const fs = require('fs');
const path = require('path');
const { Packer } = require('docx');
const {
  dokumen, gambar, jarakSetelahTabel, keteranganGambar, keteranganTabel, paragraf, subSubBab, tabel,
} = require('./format');

const FOLDER = path.join(__dirname, '..', 'tangkapan', 'black-box');
const hasil = JSON.parse(fs.readFileSync(path.join(FOLDER, 'hasil.json'), 'utf8'));
const layar = (kode) => path.join(FOLDER, `${kode}.png`);

// Merapikan catatan teknis skrip menjadi kalimat tabel tanpa mengubah hasil yang teramati.
const rapikan = (h) => h.nyata
  .replace(/^Respons (null|\d+)(, |\. )?/, '')
  .replace(/Data tetap \d+\. /, 'Data tidak bertambah. ')
  .replace(/, jumlah (\d+) ke (\d+)/, 'Jumlah data $1 menjadi $2')
  .replace(/^jumlah (\d+) ke (\d+)/, 'Jumlah data $1 menjadi $2')
  .replace(/^Jumlah data/, 'Jumlah data')
  .replace(/jumlah tetap \d+\. /, 'Data tidak bertambah. ')
  .replace(/Biodata belum disimpanKode klien sudah dipakai di lembaga ini\. \| /, '')
  .replace(/ \| [^|]*$/, '')
  .replace(/^Pesan: /, 'Pesan tampil: ')
  .replace(/\. Pesan: /, '. Pesan tampil: ')
  .replace(/Aktif=0, /, 'Akun nonaktif, ')
  .replace(/^Kode (\d+)$/, 'Kode $1')
  .replace(/^Membuka dasbor mengarah/, 'Dasbor mengarah')
  .replace(/^Membuka /, 'Terbuka ')
  .replace(/Belum dibaca (\d+) ke (\d+)/, 'Belum dibaca $1 menjadi $2')
  .replace(/, respons null/, '')
  .trim()
  .replace(/^[a-z]/, (huruf) => huruf.toUpperCase()) || '-';

const sesuai = hasil.filter((h) => h.status === 'Sesuai').length;
const tidak = hasil.filter((h) => h.status !== 'Sesuai');
const ringkasTidak = tidak.length
  ? `${tidak.length === 1 ? 'Satu' : tidak.length} skenario belum sesuai, yaitu ${tidak.map((h) => h.kode).join(', ')}.`
  : 'Tidak ada skenario yang gagal.';

const isi = [
  subSubBab('3.5.3 Pengujian'),
  paragraf(`SiDaya diuji dengan metode kotak hitam (*black box testing*), yaitu memeriksa masukan dan keluaran tanpa melihat kode program (Kusumadewa dkk. 2026). Skrip otomatis menjalankan ${hasil.length} skenario lewat antarmuka peramban Google Chrome pada server lokal dengan data contoh fiktif. ${tidak.length ? `Sebanyak ${sesuai} skenario sesuai harapan (Tabel 3.21). ${ringkasTidak}` : 'Seluruh skenario memberi hasil sesuai harapan (Tabel 3.21), sehingga tingkat keberhasilannya 100% pada cakupan yang diuji.'}`),
  keteranganTabel('Tabel 3.21 Hasil Pengujian *Black Box*'),
  tabel([750, 2437, 1750, 2000, 1000], [
    ['Kode', 'Skenario dan Data Masukan', 'Hasil yang Diharapkan', 'Hasil Nyata', 'Status'],
    ...hasil.map((h) => [h.kode, `${h.skenario}. Masukan: ${h.masukan}`, h.diharapkan, rapikan(h), h.status]),
  ]),
  jarakSetelahTabel(),
  paragraf('Pengujian otomatis PHPUnit menjalankan 52 kasus uji pada basis data pengujian yang terpisah, dan seluruh kasus lulus dengan 543 pemeriksaan. Hasil tersebut melengkapi pengujian lewat antarmuka. Pengujian kotak hitam ini belum mencakup unggah logo dan foto, deteksi koordinat, penghapusan data, pengelolaan isi halaman publik, dan tampilan pada telepon genggam. Pengujian ini juga belum melibatkan pembimbing lapangan.'),
  paragraf('Hasil pengujian sejalan dengan penelitian terdahulu, dengan tambahan pemeriksaan pada pemisahan data antarlembaga. Sistem stok obat berbasis Laravel 11 dengan RBAC lulus seluruh skenario *black box* dan memperoleh skor *System Usability Scale* (SUS) rata-rata 76 (Purba dan Hidayasari 2026). Pengujian SiDaya juga mencoba pengurus LKS yang membuka data klien milik lembaga lain, dan aplikasi menolak permintaan itu dengan kode 404. Hasil ini belum mengukur kepuasan pengguna, sehingga SUS dapat dipakai pada pengujian berikutnya.'),
  paragraf('Kepuasan pengguna juga dapat diukur dengan kerangka PIECES. Kerangka itu pernah dipakai pada 150 responden pengguna situs pemerintah daerah (Malioy dkk. 2024). Pengukuran semacam itu memerlukan petugas dan pengurus LKS yang sudah memakai SiDaya.'),
  paragraf('Pengujian SiDaya juga belum menilai ketahanan aplikasi terhadap serangan. Kerentanan aplikasi dapat dinilai dengan panduan NIST SP 800-115 dan OWASP (Darojat dkk. 2022).'),
  paragraf('Saat pengguna salah mengisi kata sandi, aplikasi menolak dan menampilkan pesan (Gambar 3.38). Formulir pendaftaran menampilkan daftar isian yang belum lengkap (Gambar 3.39), lalu menampilkan halaman hasil setelah pendaftaran tersimpan (Gambar 3.40). Aplikasi menolak pengurus LKS yang membuka dasbor Admin dengan kode HTTP 403 (Gambar 3.41), menolak NIK yang kurang digit (Gambar 3.42), dan menolak username yang sudah dipakai (Gambar 3.43). Admin dapat menolak pendaftaran dengan alasan (Gambar 3.44), dan pengurus dapat mengirim laporan pada periode yang sudah berakhir (Gambar 3.45).'),
  gambar(layar('M-04'), 14, 10),
  keteranganGambar('Gambar 3.38 Pengujian Masuk dengan Kata Sandi Salah'),
  gambar(layar('D-01'), 14, 10),
  keteranganGambar('Gambar 3.39 Pengujian Pendaftaran dengan Formulir Kosong'),
  gambar(layar('D-03'), 14, 10),
  keteranganGambar('Gambar 3.40 Pengujian Pendaftaran dengan Data Lengkap'),
  gambar(layar('A-02'), 14, 10),
  keteranganGambar('Gambar 3.41 Pengujian Penolakan Akses Pengurus LKS'),
  gambar(layar('U-02'), 14, 10),
  keteranganGambar('Gambar 3.42 Pengujian NIK Kurang Digit'),
  gambar(layar('K-01'), 14, 10),
  keteranganGambar('Gambar 3.43 Pengujian Username yang Sudah Dipakai'),
  gambar(layar('T-03'), 14, 10),
  keteranganGambar('Gambar 3.44 Pengujian Penolakan Pendaftaran dengan Alasan'),
  gambar(layar('R-03'), 14, 10),
  keteranganGambar('Gambar 3.45 Pengujian Pengiriman Laporan Semester'),
];

module.exports = { isi };

if (require.main === module) {
  const tujuan = path.join(__dirname, '..', 'Black-Box-Testing-Subbab-3.5.3.docx');
  if (fs.existsSync(tujuan) && !process.argv.includes('--timpa')) {
    console.error('Black-Box-Testing-Subbab-3.5.3.docx sudah ada. Isian yang sudah diketik bisa hilang. Tambahkan --timpa bila memang ingin menimpanya.');
    process.exit(1);
  }
  Packer.toBuffer(dokumen({ judul: 'Black Box Testing', halamanAwal: 61, isi })).then((isiBerkas) => {
    fs.writeFileSync(tujuan, isiBerkas);
    console.log(tujuan);
  });
}
