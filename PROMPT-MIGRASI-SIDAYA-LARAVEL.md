# Prompt: Migrasi Dashboard SiDaya ke Full Laravel (Tahap 1: UI)

Bangun ulang dashboard **Admin** dan **LKS** dari aplikasi SiDaya menjadi project Laravel murni berbasis Blade, tanpa Vue dan Inertia.

Tahap ini hanya mengerjakan **tampilan dan interaksi frontend dengan data dummy**. Backend menyusul di Tahap 2, jadi susun kode agar data dummy mudah diganti dengan database.

Balas dan tulis teks antarmuka dalam Bahasa Indonesia.

## 1. Lokasi

| | Lokasi | Aturan |
|---|---|---|
| **Sumber** | `D:\PKL\SiDaya` (commit `9167c47`) | **Hanya dibaca.** Jangan ubah, jalankan migrasi, atau menulis apa pun di sini. |
| **Target** | `D:\Iqbal\PKL Project` | Project Laravel baru dibuat di sini. |

Jangan salin dari sumber: `.env`, `vendor`, `node_modules`, `storage`, `public/build`, `laporan-pkl`, dan data asli dari database atau backup.

`public/styles` dan `public/scripts` di sumber adalah junction ke `resources/`. Salin dari `resources/`, bukan dari junction. Aturan `D:\SIDAYA-Tools` di `AGENTS.md` sumber tidak berlaku di target; jalankan `composer install` dan `npm install` secara normal.

## 2. Teknologi

- Laravel 13, PHP 8.4 atau lebih baru (versi lokal: PHP 8.5), Blade, Vite.
- JavaScript vanilla sebagai ES module, hanya untuk interaksi yang memang butuh browser.
- CSS memakai file asli dari sumber. Jangan pakai Tailwind atau Bootstrap, dan jangan menulis ulang desain.
- Jangan pasang Vue, Inertia, React, atau Livewire.
- Tanpa database di Tahap 1. Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, dan jangan buat migrasi bisnis.

## 3. Cakupan

**Dikerjakan**
- Halaman login dan ganti kata sandi.
- Seluruh dashboard role `admin` dan `lks`, termasuk layout, sidebar, topbar, tema gelap, dan tampilan mobile.

**Tidak dikerjakan**
- Dashboard role `psm`, `karang-taruna`, dan `psm-member`.
- Halaman publik: beranda, profil, pilar, kontak, dan formulir pendaftaran.
- Backend sungguhan: autentikasi, penyimpanan, upload, notifikasi, serta pembuatan file Excel/PDF.
- Fitur yang sudah dipensiunkan di sumber dan tidak boleh dihidupkan lagi: pengajuan anggota (`member-applications`), verifikasi klien (`resident-verifications`), antrean revisi (`revisions`), registrasi publik PSM, serta nomor dan bukti pendaftaran mitra. Rute kompatibilitas 410 dan redirect lamanya juga tidak perlu dibawa.

**Menu PSM dan Karang Taruna di sidebar Admin tetap dibuat**, karena itu fitur milik Admin. Yang dihapus hanya dashboard role PSM dan Karang Taruna.

## 4. Aturan Kemiripan Tampilan

Hasilnya harus terlihat sama dengan aplikasi lama.

1. Pertahankan nama class, id, atribut `data-*`, struktur HTML, dan teks. CSS sumber bergantung pada semua itu.
2. Sebagian besar markup sumber tidak ada di file Vue, melainkan dirender JavaScript lewat string HTML di `resources/js/features/dashboards/`. Pindahkan string itu ke Blade dan pertahankan hasil akhir HTML-nya. Baca modul sumbernya, jangan menebak.
3. Pecah markup yang berulang menjadi Blade component: kartu ringkasan, tabel, filter, badge status, paginasi, dialog, dan empty state.
4. Pertahankan palet: Admin memakai palet bersama, LKS memakai palet emerald dari `lks/palette.css` yang di-scope ke `body[data-role="lks"]`.
5. Layout body memakai `class="quarterly-dashboard"` dan `data-role="admin|lks"`.
6. Ikon: pindahkan `DashboardSymbols.vue` dan `DashboardIcon.vue` menjadi `<x-dashboard.symbols />` dan `<x-dashboard.icon name="..." />`.
7. Font (Plus Jakarta Sans), logo, dan aset lain tetap memakai URL root-relative (`/assets/...`). Sertakan juga file lisensi dan `provenance` yang ada di folder aset tersebut.

Sebagai pembanding visual, aplikasi lama bisa dijalankan dengan `tools\sidaya.cmd serve` di folder sumber. Perintah ini tidak boleh mengubah file di sumber; kalau butuh setup atau build, baca `docs/development.md` dulu dan tanyakan ke user.

## 5. Struktur Target

```text
app/
  Http/Controllers/
    Auth/                 Login dan ganti kata sandi (demo)
    Admin/                Satu controller per area fitur
    Lks/                  Satu controller per area fitur
  Http/Middleware/EnsureDemoRole.php
  Support/Demo/           Sumber data dummy, satu class per domain
config/navigation.php     Menu Admin dan LKS
resources/
  css/                    Salinan CSS sumber, jalur folder dipertahankan
    entries/admin.css     Daftar @import, urutan sesuai manifest
    entries/lks.css
    entries/auth.css
  js/
    dashboard/            shell.js, dialogs.js, filters.js, forms.js, dst.
    pages/                Modul khusus halaman, misalnya client-form.js
  views/
    layouts/dashboard.blade.php
    layouts/auth.blade.php
    components/dashboard/ Sidebar, topbar, icon, tabel, filter, dialog, dst.
    auth/
    admin/                Dikelompokkan per fitur
    lks/                  Dikelompokkan per fitur
    shared/               Partial yang dipakai kedua role
public/assets/            Hanya aset yang dipakai dashboard dan login
routes/web.php
```

**Pemisahan data:** view hanya menerima data dari controller, dan controller hanya mengambil data dari class di `App\Support\Demo`. Jangan menulis data dummy di Blade atau JavaScript. Bentuk field dibuat dekat dengan kolom tabel sumber (`docs/database.md`), supaya di Tahap 2 cukup mengganti isi class tersebut dengan Eloquent.

### Urutan CSS

Urutan CSS memengaruhi hasil, jadi ikuti persis key `styles` per role di `resources/js/features/dashboards/manifest.json`, lalu tambahkan `shared/brand.css` (dimuat `resources/views/app.blade.php`).

Admin juga memuat CSS dari folder role lain, jadi file berikut **tetap wajib disalin**:
- `lks/` (clients, workspace, client-lists, client-detail, profile, letters)
- `karang-taruna/` (profile, member-detail)
- `psm/` (members, member-accounts/accounts, complaints, uploads)
- `shared/psm-complaint-detail.css`

Login memakai `auth/login/*.css` dan `public/shared/footer.css`.

## 6. Autentikasi Demo

- `/login` menampilkan desain lengkap dari `Login.vue`, termasuk maskot. Sertakan tombol masuk cepat **Admin** dan **LKS**, yang mengisi `session('demo_role')` lalu mengarah ke `/{role}/overview`.
- Maskot PSM dan Karang Taruna di halaman login dipertahankan sebagai bagian desain.
- Form login demo tidak memeriksa kata sandi dan tidak menyimpan kredensial. Rute masuk demo hanya aktif saat `APP_ENV=local`.
- `EnsureDemoRole` menolak akses ke role lain dengan 403, dan mengarahkan yang belum masuk ke `/login`.
- Logout membersihkan session lewat `POST /logout` dengan CSRF.
- Beri komentar `TODO(Tahap 2)` di titik yang akan diganti autentikasi sungguhan.

## 7. Perilaku Bersama

**Bekerja penuh di Tahap 1**
- Sidebar: grup buka-tutup dan menu aktif, termasuk halaman detail yang menandai menu induknya (logika ada di `adminDestination()` dan `active()` pada `DashboardSidebar.vue`). Pada halaman detail, menu aktif ditentukan dari data dummy.
- Sidebar diciutkan di desktop; drawer mobile di lebar 980px ke bawah, dengan backdrop, tombol Escape, focus trap, dan pengembalian fokus.
- Tema terang/gelap lewat class `dark` pada `<html>`. Hanya pilihan tema yang boleh disimpan di `localStorage`, dengan `try/catch`.
- Breadcrumb topbar, avatar inisial, banner layanan, skip link, dan dialog konfirmasi logout.
- Pencarian, filter, tab, dan paginasi dilakukan **di server lewat query string** terhadap data dummy, memakai `LengthAwarePaginator`. Pakai nama parameter sumber: `q, pillar, district, village, status, year, quarter, page, org, residence, category, gender, tab, complaint, filter`.
- Lencana jumlah pada sidebar dihitung dari data dummy. Polling 15 detik dari sumber tidak dibawa.
- Validasi form di browser, pesan error, wizard atau bagian form, pratinjau file dan foto, serta area seret-lepas.
- Halaman 404/403 dengan gaya dashboard.

**Tampil tetapi belum menyimpan**
- Form simpan, kirim, hapus, setujui, tolak, reset, dan terbitkan: tampilkan dialog konfirmasi yang sama seperti sumber, lalu toast netral *"Mode demo: perubahan belum disimpan."* Jangan pernah menampilkan pesan seolah data sudah tersimpan.
- Unduh Excel, PDF, dan berkas: tombol tampil, lalu toast *"Unduhan tersedia setelah backend terhubung."* Tandai sumber logika di komentar untuk Tahap 2 (`*-export.js`, `report-download.js`, `xlsx-writer.js`).

## 8. Fitur Admin

Base URL `/admin`. Kolom Sumber merujuk ke `resources/js/features/dashboards/`.

Urutan sidebar: **Ruang Kerja** → **Penerimaan & Verifikasi** → kartu pilar **LKS** → **PSM** → **Karang Taruna** → **Bantuan** → footer (Pengaturan Akun, Ganti Kata Sandi, Logout).

| Grup | Menu | URL | Isi halaman | Sumber |
|---|---|---|---|---|
| Ruang Kerja | Ringkasan | `/overview` | Entitas mitra terdaftar, tahun anggaran dan periode, pekerjaan Admin yang perlu ditindaklanjuti, ringkasan tiga mitra, rekap laporan LKS, aktivitas terbaru | `admin/overview.js`, `admin/monitoring-data.js` |
| | Notifikasi | `/notifications`, `/notification-detail/{id}` | Daftar, filter jenis mitra, tandai semua dibaca, tandai belum dibaca, detail | `admin/notifications.js`, `shared/workspace-notifications.js` |
| Penerimaan & Verifikasi | Pendaftaran Mitra: LKS dan Karang Taruna | `/registrations/lks`, `/registrations/karang-taruna`, `/registration/{id}` | Tabel, cari, filter status dan kecamatan. Detail: status, dokumen, riwayat, keputusan Terima/Tolak (alasan wajib saat menolak, kirim alasan via WhatsApp) | `admin/registrations.js` |
| | Buat Akun Mitra | `/partner-accounts` | Form akun (ambil dari pendaftaran, pilar, WhatsApp, nama, username, kata sandi), panduan alur, daftar akun aktif, dialog Reset Kata Sandi dengan tombol Acak | `admin/partner-accounts.js` |
| LKS | Data LKS | `/organizations/lks`, `/organization/{id}`, `/member/{id}` | Direktori lembaga, filter, detail profil lembaga, pengurus, klien, dan detail personel, unduh | `admin/partner-workspace.js`, `shared/workspace-profile.js` |
| | Rekap Penghuni | `/occupancy`, `/resident/{id}` | Rekap klien lintas LKS, filter, demografi, detail biodata | `lks/client-lists.js`, `lks/client-detail.js` |
| | Laporan Semester | `/reports/lks`, `/report/{id}` | Daftar laporan, filter, detail, lampiran, unduh rekap | `shared/workspace-reports.js`, `shared/reports.js`, `admin/report-download.js` |
| | Surat Masuk & Keluar | `/letters/lks` | Tab surat, filter LKS/bulan/tahun, form surat keluar dan unggah berkas, tandai dibaca, unduh, hapus | `lks/letters*.js` |
| PSM | Data Anggota | `/members/psm` | Tabel anggota, filter kecamatan/kelurahan, unduh | `admin/partner-workspace.js`, `psm/members.js` |
| | Pengaduan Anggota | `/complaints/psm` | Daftar pengaduan, cari, filter wilayah, detail, lampiran dan KTP | `admin/psm-complaints.js`, `shared/psm-complaint-detail.js` |
| Karang Taruna | Data Kelompok | `/organizations/karang-taruna`, `/organization/{id}`, `/member/{id}` | Direktori kelompok dan detail | `admin/partner-workspace.js`, `karang-taruna/profile.js`, `karang-taruna/member-detail.js` |
| | Surat Pengajuan: Surat Masuk | `/incoming-letters/karang-taruna`, `/incoming-letter/{id}` | Daftar dan filter, unduh PDF dan Excel. Detail: pengajuan, lampiran, riwayat, catatan, Mulai tinjau / Minta perbaikan / Setujui / Tolak | `admin/correspondence.js` |
| | Surat Pengajuan: Surat Balasan | `/reply-letters/karang-taruna`, `/new-reply-letter`, `/reply-letter/{id}`, `/edit-reply-letter/{id}` | Daftar, form draf, detail, terbitkan, hapus | `admin/correspondence-replies.js` |
| Bantuan | Panduan Admin | `/guide` | Panduan penggunaan | `shared/workspace.js` |
| | Kelola Landing Page | `/landing-content` | Pilih halaman, edit teks dan foto per slot, simpan, kembalikan bawaan. Slot diambil dari data dummy; karena halaman publik di luar cakupan, tautan pratinjaunya dinonaktifkan | `admin/landing-content.js`, `config/landing.php` |
| Footer | Pengaturan Akun | `/account` | Biodata, notifikasi, batalkan dan simpan, peringatan perubahan belum disimpan | `admin/account-settings.js` |

## 9. Fitur LKS

Base URL `/lks`. Sidebar: **Ruang kerja** → **Organisasi** → **Pendataan** → **Pelaporan** → **Bantuan** → Logout.

| Grup | Menu | URL | Isi halaman | Sumber |
|---|---|---|---|---|
| Ruang kerja | Ringkasan | `/overview` | Buat/buka laporan, laporan periode berjalan, catatan terakhir Admin, tindak lanjut, ringkasan pengurus, biodata klien terbaru, laporan semester, peta titik lokasi | `lks/overview.js` |
| | Notifikasi | `/notifications`, `/notification-detail/{id}` | Daftar, tandai dibaca, detail | `shared/workspace-notifications.js` |
| Organisasi | Profil & Legalitas | `/profile` | Identitas, logo, titik koordinat dan peta, dokumen legalitas, ringkasan operasional, dialog edit profil | `lks/profile.js`, `shared/workspace-profile.js` |
| | Pengurus & SDM | `/members`, `/new-member`, `/edit-member/{id}`, `/member/{id}` | Tabel personel, cari, filter kategori, unduh, form (foto, identitas, keanggotaan, berkas pendukung), detail, hapus | `lks/staff.js`, `lks/member-form.js` |
| | Surat Masuk & Keluar | `/letters` | Tab masuk/keluar, cari, filter bulan/tahun, form nomor, tanggal, perihal, keterangan, unggah berkas maks. 5 MB, tandai dibaca, unduh, hapus | `lks/letters*.js` |
| Pendataan | Tambah Klien | `/new-resident`, `/edit-resident/{id}` | Form 4 bagian (lihat di bawah), pratinjau biodata, indikator kelengkapan isian wajib | `lks/client-form*.js` |
| | Rekap Klien | `/occupancy`, `/resident/{id}` | Cari nama atau nomor registrasi, filter, demografi, tabel, detail, unduh | `lks/client-lists.js`, `lks/client-detail.js`, `lks/clients.js` |
| Pelaporan | Laporan Semester | `/reports`, `/drafts`, `/report/{id}`, `/edit/{id}` | Daftar dan draf, filter periode, editor laporan, unggah lampiran, pratinjau dan kirim, hapus draf, detail | `lks/reports.js`, `lks/report-editor.js`, `shared/reports-editor.js`, `shared/reports-attachments.js` |
| Bantuan | Panduan Pelaporan | `/guide` | Panduan penggunaan | `shared/workspace.js` |

**Bagian form klien** (`lks/client-form-sections.js`)
- **A. Identitas Klien / Anak Asuh:** nama, kode, kategori, jenis kelamin, agama, NIK, nomor KK, tempat dan tanggal lahir, tanggal lahir tidak diketahui, perkiraan usia.
- **B. Data Orang Tua / Wali dan Alamat:** status, nama, hubungan, dan kontak wali, alamat asal, alamat wali.
- **C. Status Sosial, Bantuan & Pendidikan:** yatim/piatu, kerentanan ekonomi, pendidikan, layanan, status tinggal, jaminan kesehatan, kebutuhan dan rencana perawatan, dokumen.
- **D. Informasi Masuk & Penanggung Jawab:** tanggal masuk dan keluar, alasan keluar, sumber rujukan, alasan masuk, pendamping, penanggung jawab, catatan.

Ikuti daftar field dan aturan isian lengkap dari file sumbernya.

**Status laporan:** ambil daftar pasti dari `shared/workspace-reports.js` sekitar baris 26 (termasuk Draf, Perlu Revisi, Disetujui). Editor hanya untuk status yang bisa diedit menurut sumber.

**Status lain di data dummy:** Draf, Dikirim, Menunggu Pemeriksaan, Menunggu Verifikasi, Perlu Perbaikan, Disetujui, Ditolak, Diterima, Diterbitkan. Pakai hanya pada fitur yang memang memakainya di sumber.

## 10. Data Dummy

- Seluruh isi harus **fiktif**. Dilarang menyalin data dari database, backup, `laporan-pkl`, atau `.env`. Pakai nama fiktif, NIK dan nomor KK yang jelas palsu seperti `0000000000000001`, nomor WhatsApp fiktif, dan domain `sidaya.example`.
- Wilayah memakai kecamatan dan kelurahan Kota Banjarbaru agar filter terasa nyata.
- Jumlah minimal: 8 LKS, 6 Karang Taruna, 10 anggota PSM, 40 klien, 15 personel, 12 laporan dengan semua status, 15 surat, 10 pendaftaran, 8 akun mitra, 10 pengaduan, 15 notifikasi.
- Akun LKS demo terhubung ke satu LKS. Halaman LKS hanya menampilkan data lembaganya.
- Data antarhalaman harus konsisten: jumlah di ringkasan dan lencana sidebar sama dengan isi tabelnya.
- Sediakan satu cara menampilkan empty state dan error state agar bisa diperiksa, misalnya `?demo_state=empty`, hanya saat `APP_ENV=local`.

## 11. Urutan Pengerjaan

Selesaikan dan periksa per tahap, lalu laporkan sebelum lanjut.

1. **Fondasi:** buat project Laravel, salin CSS dan aset, siapkan entri Vite, `.env.example`, rute, login demo, dan middleware.
2. **Shell:** layout, sidebar kedua role, topbar, tema, drawer mobile, dialog logout, ikon, dan halaman 404/403.
3. **Komponen bersama:** kartu ringkasan, tabel responsif, filter, paginasi, badge, dialog, toast, dan empty state.
4. **LKS:** Ringkasan → Profil → Pengurus → Surat → Klien → Laporan → Notifikasi → Panduan.
5. **Admin (mitra LKS):** Ringkasan → Pendaftaran → Akun Mitra → Data LKS → Rekap → Laporan → Surat.
6. **Admin (lainnya):** PSM → Karang Taruna → Landing Page → Pengaturan Akun → Notifikasi → Panduan.
7. **Pemeriksaan akhir** sesuai bagian 12, lalu tulis `README.md` dan `docs/tahap-2-backend.md`.

## 12. Kriteria Selesai

- `composer install`, `npm install`, `npm run build`, dan `php artisan serve` berjalan tanpa error, tanpa database.
- `package.json` dan `composer.json` tidak mengandung Vue maupun Inertia, dan tidak ada file `.vue` di project.
- Semua URL di bagian 8 dan 9 mengembalikan 200 untuk role yang benar dan 403 untuk role lain. Tulis feature test untuk ini (`php artisan test`).
- Setiap halaman dibandingkan berdampingan dengan aplikasi lama pada lebar 1440px dan 375px, dalam tema terang dan gelap.
- Konsol browser bersih dan tidak ada aset 404.
- Tidak ada horizontal scroll di mobile. Navigasi keyboard berfungsi dan fokus terlihat.
- Tidak ada aksi yang memberi kesan data tersimpan.
- `git` diinisialisasi dan setiap tahap punya commit sendiri, **tanpa atribusi Claude** (tanpa `Co-Authored-By`).

## 13. Serah Terima ke Tahap 2

`docs/tahap-2-backend.md` berisi daftar singkat:
- Class `App\Support\Demo` dan tabel sumber penggantinya.
- Setiap aksi dummy dan endpoint sumber yang setara (`routes/web.php` di sumber).
- Logika ekspor Excel/PDF yang perlu dipindahkan.
- Aturan bisnis sumber yang wajib dipertahankan, dari `AGENTS.md` dan `docs/alur-sistem.md`.
- Semua komentar `TODO(Tahap 2)`.

Jangan mulai Tahap 2 sebelum user meminta.

## 14. Jika Ragu

Sumber adalah acuan utama. Bila prompt ini berbeda dengan perilaku kode sumber, atau sebuah fitur tidak bisa dibuat tanpa backend, ikuti tampilan sumber, catat perbedaannya di laporan tahap, dan lanjutkan. Tanyakan ke user hanya untuk keputusan yang mengubah cakupan.
