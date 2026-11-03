# SiDaya: Dashboard Admin dan LKS

Dashboard **Admin** dan **LKS** serta halaman publik SiDaya yang dibangun ulang dengan Laravel dan Blade, tanpa Vue dan Inertia. Seluruh isi hanya menyangkut LKS; halaman Pilar Sosial hanya menampilkan LKS, sedangkan fitur dan penjelasan PSM, Karang Taruna, dan PUB tidak disertakan.

Data tersimpan di MySQL/MariaDB. Rancangan tabel dan alurnya ada di [docs/rancangan-database.md](docs/rancangan-database.md).

## Prasyarat

- PHP 8.4.1 atau lebih baru, dengan ekstensi `pdo_mysql`, `fileinfo`, dan `gd`
- MySQL atau MariaDB
- Composer
- Node.js 22 atau lebih baru

Formulir pendaftaran mengunggah 16 berkas sampai 5 MB per berkas, jadi `php.ini` perlu mengizinkannya:

```ini
upload_max_filesize = 6M
post_max_size = 100M
max_file_uploads = 30
```

## Menjalankan

Buat database `sidaya` dan `sidaya_uji` dengan `utf8mb4`, lalu:

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Halaman publik ada di `http://127.0.0.1:8000/`. Dashboard ada di `/login`.

Saat `APP_ENV=local` atau `testing`, seeder mengisi data contoh fiktif:

| Akun | Isian masuk | Kata sandi |
| --- | --- | --- |
| Admin | `admin@sidaya.example` atau `admin` | `SidayaAdmin2026` |
| LKS | `lks-001@sidaya.example` atau `lks_001_demo` | `SidayaLks2026` |

Saat `APP_ENV=local`, halaman login juga punya tombol masuk cepat tanpa kata sandi. Di lingkungan lain tombol dan rutenya tidak aktif, seeder tidak membuat data contoh, dan `ADMIN_PASSWORD` wajib diisi. Pastikan `APP_ENV=production` di server.

Bila Admin lupa sandi, jalankan `php artisan account:reset-admin email@admin` di server untuk mendapat sandi sementara. LKS meminta reset ke Admin.

## Pemeriksaan

```sh
php artisan test
```

Tes berjalan di database `sidaya_uji` dan menghapus isinya. `tests/TestCase.php` menolak database lain.

## Susunan kode

| Lokasi | Isi |
| --- | --- |
| `app/Models/` | Model tiap tabel, memakai nama Indonesia. |
| `app/Support/Data/` | Membaca database dan memetakannya ke bentuk data yang dibaca view. |
| `app/Support/` | Logika daftar yang dipakai kedua peran, helper format, menu aktif, dan keamanan akun. |
| `app/Services/` | Penyimpanan berkas, pengiriman notifikasi, dan isi landing page. |
| `app/Http/Controllers/{Admin,Lks}/` | Halaman baca per peran. |
| `app/Http/Controllers/Aksi/` | Semua aksi tulis, dengan validasi dan transaksi. |
| `app/Http/Controllers/EksporController.php` | Data ekspor PDF dan Excel. |
| `database/migrations/` | Skema 13 tabel. |
| `database/seeders/` | Akun Admin pertama dan data contoh (`Contoh/`). |
| `config/navigation.php` | Menu sidebar kedua peran. |
| `resources/css/` | Salinan CSS sumber. Urutan muat ada di `entries/`; jangan diubah karena memengaruhi hasil. |
| `resources/js/dashboard/` | Perilaku bersama: shell, dialog, filter, form, pengiriman (`api.js`), ekspor, dan toast. |
| `resources/js/pages/` | Modul khusus halaman. |
| `public/assets/ekspor/` | Modul ekspor PDF dan Excel dari sumber, dimuat saat dipakai. |
| `resources/views/` | `layouts/`, `components/dashboard/`, `auth/`, `admin/`, `lks/`, `public/`, dan `shared/`. |

## Perilaku aksi

Tombol dan form memakai atribut `data-form-url`, `data-action-url`, `data-download-url`, dan `data-export-url`. Pesan berhasil hanya tampil setelah server mengonfirmasi. Data pribadi tidak disimpan di browser; hanya pilihan tema yang memakai `localStorage`.
