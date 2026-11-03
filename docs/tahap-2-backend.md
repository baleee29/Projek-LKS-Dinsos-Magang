# Serah terima Tahap 2: Backend

Backend sudah dibangun mengikuti [rancangan-database.md](rancangan-database.md). Kelas `App\Support\Demo` sudah dihapus, dan data contoh dipindah ke `database/seeders/`.

## Aturan yang dijaga

- Lembaga selalu diambil dari sesi (`pengguna.lembaga_id`), bukan dari kiriman browser. Admin boleh membaca lintas lembaga.
- Edit memakai kolom `versi`; versi yang tidak cocok ditolak dengan 409.
- Personel dan klien disimpan langsung oleh lembaga pemiliknya tanpa persetujuan Admin.
- Pembuatan akun selalu terpisah dari penambahan personel dan penerimaan pendaftaran.
- Pendaftaran hanya diterima atau ditolak Admin. Alasan penolakan wajib, dan hasilnya disampaikan lewat WhatsApp.
- Laporan berstatus `Draf` atau `Terkirim`, tanpa persetujuan. Perubahan setelah terkirim tetap privat sampai dikirim ulang. Admin tidak boleh membaca draf.
- Berkas memakai potongan BLOB 48 KiB, dalam transaksi yang sama dengan data pemiliknya. Notifikasi juga ditulis dalam transaksi yang sama.
- Pesan berhasil hanya tampil setelah server mengonfirmasi. Data pribadi tidak disimpan di browser.
- Tes hanya berjalan di `sidaya_uji`. Jangan menjalankan `migrate:fresh` pada database aplikasi yang sudah berisi data asli.
- Fitur pensiun tidak dihidupkan lagi: pengajuan anggota, verifikasi klien, antrean revisi, serta nomor dan bukti pendaftaran.

## Perbedaan dari project sumber

- **Ekspor:** PDF dan Excel memakai satu pembuat tabel bersama (`resources/js/dashboard/ekspor.js`). Isinya mengikuti filter halaman, tetapi tata letaknya tidak sama dengan modul ekspor sumber (kop, rekap per bagian, dan font tertanam).
- **Pratinjau surat:** dialog surat menampilkan tombol buka atau unduh, tanpa pratinjau PDF dan gambar di dalam dialog.
- **Daftar dan filter:** data dibaca dari database, lalu difilter dan dibagi halaman di PHP, sehingga tampilan tetap sama. Perlu dipindah ke query SQL bila datanya sudah sangat besar.
- **Wilayah lembaga:** formulir pendaftaran dan dialog Edit Profil tidak punya isian kecamatan dan kelurahan, jadi lembaga baru belum punya wilayah dan tidak muncul pada filter wilayah.
- **Navigasi:** memakai muat halaman penuh, sehingga status sidebar kembali ke bawaan setiap pindah halaman.
- **Halaman publik:** yang dibangun hanya Beranda, Profil, Kontak, Pilar Sosial (hanya berisi LKS), LKS, dan Pendaftaran LKS.
