# SiDaya: Dashboard Admin dan LKS

Dashboard **Admin** dan **LKS** SiDaya yang dibangun ulang dengan Laravel dan Blade, tanpa Vue dan Inertia. Dashboard Admin hanya mengelola LKS; fitur PSM dan Karang Taruna tidak disertakan.

Project ini ada di **Tahap 1**: tampilan dan interaksi lengkap dengan data dummy. Belum ada database, autentikasi sungguhan, penyimpanan, upload, notifikasi, maupun pembuatan berkas Excel/PDF. Rencana backend ada di [docs/tahap-2-backend.md](docs/tahap-2-backend.md).

## Prasyarat

- PHP 8.4 atau lebih baru
- Composer
- Node.js 22 atau lebih baru

## Menjalankan

```sh
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000/login`, lalu pilih tombol masuk cepat **Admin** atau **LKS**. Form login juga bisa dipakai dengan isian yang memuat kata `admin` atau `lks`; kata sandi tidak diperiksa.

Masuk demo hanya aktif saat `APP_ENV=local`.

## Pemeriksaan

```sh
php artisan test
```

Tes memeriksa akses setiap URL per peran, login demo, batas data per lembaga, dan kelengkapan data dummy.

Untuk melihat empty state dan error state, tambahkan `?demo_state=empty` atau `?demo_state=error` pada URL dashboard. Parameter ini hanya berlaku saat `APP_ENV=local`.

## Susunan kode

| Lokasi | Isi |
| --- | --- |
| `app/Support/Demo/` | Sumber data dummy, satu class per domain. Bentuk field mengikuti tabel sumber. |
| `app/Support/` | Logika daftar yang dipakai kedua peran, helper format, dan menu aktif. |
| `app/Http/Controllers/{Auth,Admin,Lks}/` | Mengambil data dari `App\Support` dan mengirimkannya ke view. |
| `config/navigation.php` | Menu sidebar kedua peran. |
| `resources/css/` | Salinan CSS sumber. Urutan muat ada di `entries/`; jangan diubah karena memengaruhi hasil. |
| `resources/js/dashboard/` | Perilaku bersama: shell, dialog, filter, form, dan toast. |
| `resources/js/pages/` | Modul khusus halaman. |
| `resources/views/` | `layouts/`, `components/dashboard/`, `auth/`, `admin/`, `lks/`, dan `shared/`. |

Data dummy tidak ditulis di Blade atau JavaScript, jadi Tahap 2 cukup mengganti isi class di `App\Support\Demo`.

## Perilaku aksi

Tidak ada aksi yang menyimpan data. Form simpan, kirim, hapus, setujui, tolak, reset, dan terbitkan menampilkan konfirmasi seperti aplikasi lama, lalu toast *"Mode demo: perubahan belum disimpan."* Tombol unduh menampilkan *"Unduhan tersedia setelah backend terhubung."*

Hanya pilihan tema yang disimpan di browser (`localStorage`).

## Lisensi aset

Font Plus Jakarta Sans memakai lisensi di `public/assets/fonts/OFL.txt`. Asal logo dan gambar dicatat pada berkas `provenance` di folder masing-masing.
