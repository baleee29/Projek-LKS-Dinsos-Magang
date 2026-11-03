# Serah terima ke Tahap 2: Backend

Daftar singkat untuk mengganti data dummy Tahap 1 dengan backend sungguhan. "Sumber" berarti `D:\PKL\SiDaya` pada commit `9167c47`.

## 1. Class dummy dan tabel penggantinya

| Class `App\Support\Demo` | Tabel sumber |
| --- | --- |
| `DemoOrganizations` | `organizations` |
| `DemoMembers` | `organization_members` |
| `DemoClients`, `DemoClientSchema` | `lks_clients` (`raw_data`, `history`); aturan isian dari `shared/data-schemas.js` dan `shared/data-validation.js` |
| `DemoReports` | `quarterly_reports`; berkas lewat `stored_files` dan `stored_file_chunks` |
| `DemoLetters` | `lks_letters` |
| `DemoRegistrations` | `partner_registrations`, `partner_registration_files` |
| `DemoAccounts`, `DemoAdminAccount` | `users` |
| `DemoPsmMembers` | `psm_members`, `psm_directory_files`, `psm_directory_file_chunks` |
| `DemoComplaints` | `psm_complaints` |
| `DemoActivities` | `karang_taruna_activities`, `karang_taruna_activity_files` |
| `DemoReplies` | `department_replies` |
| `DemoNotifications` | `notifications` (notifikasi database Laravel per pengguna) |
| `DemoLanding` | `LandingRegistry` dan `config/landing.php` sumber, ditambah view publik |
| `DemoRegions` | `config/psm.php` sumber |
| `DemoClock` | `now('Asia/Makassar')` dan `currentPeriod()` dari `shared/data.js` |
| `DemoShell` | `auth()->user()` dan rekap `shared/workflow-summary.js` |
| `DemoState`, `DemoPaginator`, `DemoRepository` | Hapus. Pakai `paginate()->withQueryString()` |

## 2. Aksi dummy dan endpoint sumber

Lihat `routes/web.php` di sumber untuk middleware dan throttle.

| Aksi di Tahap 1 | Endpoint sumber |
| --- | --- |
| Login, logout | `POST /login`, `POST /logout` (`SessionController`) |
| Ganti kata sandi | `PUT /account/password` |
| Simpan Pengaturan Akun | `PUT /account/profile` |
| Edit profil lembaga, logo, koordinat | `PUT /organization/profile`, `POST /organization/logo`, `POST /organization/coordinates` |
| Tambah, ubah, hapus personel | `POST`, `PUT`, `DELETE /organization-members` |
| Simpan dan hapus klien | `POST /lks/clients`, `DELETE /lks/clients/{id}` |
| Buat dan kirim laporan, hapus draf | `POST /laporan-triwulan`, `DELETE /laporan-triwulan/{id}` |
| Surat masuk dan keluar | `POST /lks/letters`, `POST /lks/letters/{id}/read`, `POST /lks/letters/read-all`, `DELETE /lks/letters/{id}`, `GET /lks/letters/{id}/download` |
| Tandai notifikasi | `POST /notifications/read-all`, `POST /notifications/{id}/read`, `POST /notifications/{id}/unread` |
| Terima atau tolak pendaftaran | `POST /admin/partner-registrations/{id}/review` |
| Akun mitra | `POST /admin/partner-accounts`, `POST /admin/partner-accounts/{id}/reset-password`, `DELETE /admin/partner-accounts/{id}` |
| Pemeriksaan surat masuk Karang Taruna | `POST /admin/karang-taruna/activities/{id}/review` |
| Surat balasan | `POST /admin/department-replies`, `POST /admin/department-replies/{id}/publish`, `DELETE /admin/department-replies/{id}` |
| Unduh berkas pengaduan | `GET /admin/psm-complaints/{id}/attachment`, `GET /admin/psm-complaints/{id}/ktp`, `GET /admin/psm-complaints/export` |
| Kelola Landing Page | `PUT /admin/landing-content/{key}`, `POST /admin/landing-content/{key}/image`, `DELETE /admin/landing-content/{key}` |

Perilaku dummy ada di `resources/js/dashboard/dialogs.js` (`data-demo-action`, `data-demo-download`) dan `forms.js` (`data-demo-form`). Saat backend tersambung, ganti atribut itu per halaman. Toast berhasil hanya boleh tampil setelah server mengonfirmasi.

## 3. Logika ekspor yang perlu dipindahkan

Semua tombol unduh saat ini hanya menampilkan toast. Sumber ada di `resources/js/features/dashboards/`.

| Halaman | Modul sumber |
| --- | --- |
| Rekap Klien, Rekap Penghuni | `lks/resident-export.js`, `tools/build-resident-export-template.mjs` |
| Pengurus & SDM | `lks/staff-export.js` |
| Daftar laporan LKS | `lks/reports-export.js` |
| Laporan Admin | `shared/report-recap-export.js`, `admin/report-download.js`, `admin/report-fonts.js` |
| Surat LKS | `lks/letters-export.js` |
| Data LKS, Data Kelompok, Data Anggota PSM | `admin/partner-export-common.js`, `partner-export-lks.js`, `partner-export-kt.js`, `partner-export-psm.js` |
| Surat Pengajuan Karang Taruna | `admin/correspondence-export.js`, `karang-taruna/activity-export.js` |
| Penulis berkas | `shared/xlsx-writer.js`, `shared/pdf-loader.js`, `public/assets/vendor/jspdf/` |

jsPDF belum disalin ke project ini karena belum dipakai. Sertakan lisensinya saat memindahkan.

## 4. Aturan bisnis sumber yang wajib dipertahankan

Dari `AGENTS.md`, `docs/alur-sistem.md`, dan `docs/database.md` di sumber.

- Organisasi selalu diambil dari sesi (`users.organization_id`), bukan dari kiriman browser. Admin boleh membaca lintas organisasi.
- Edit memakai kolom `version`; versi yang tidak cocok ditolak sebagai konflik.
- Personel LKS dan Karang Taruna, klien LKS, serta anggota PSM disimpan langsung oleh pemiliknya tanpa persetujuan Admin.
- Pembuatan akun login selalu terpisah dari penambahan anggota dan penerimaan pendaftaran.
- Pendaftaran mitra hanya diterima atau ditolak Admin. Alasan penolakan wajib, dan hasilnya disampaikan lewat WhatsApp.
- Fitur pensiun tidak boleh dihidupkan lagi: pengajuan anggota, verifikasi klien, antrean revisi, registrasi publik PSM, serta nomor dan bukti pendaftaran mitra.
- Laporan LKS berstatus `Draf` atau `Terkirim`, tanpa persetujuan. Kiriman yang sudah masuk tetap privat dari draf perubahan sampai dikirim ulang.
- Admin tidak boleh membaca draf laporan.
- Penerima surat balasan hanya bisa membaca dan mengunduh. Penerbitan surat balasan tidak menyetujui kegiatan.
- Berkas memakai `StoredFiles` dengan potongan BLOB 48 KiB, dalam transaksi yang sama dengan data pemiliknya.
- Notifikasi ditulis dalam transaksi yang sama dengan perubahan data.
- Keberhasilan di UI hanya setelah server mengonfirmasi. Data pribadi tidak boleh disimpan di `localStorage`, `sessionStorage`, atau IndexedDB.
- Test hanya di database `sidaya_testing`, dan `migrate:fresh` tidak boleh dijalankan pada database aplikasi.

## 5. Perbedaan Tahap 1 terhadap sumber

- Navigasi memakai muat halaman penuh, sehingga status sidebar diciutkan dan grup yang ditutup kembali ke bawaan setiap pindah halaman.
- Filter berjalan di server lewat query string, bukan menyaring di browser.
- Tautan sumber `#members?pillar=…`, `#organizations`, dan `#organizations/psm` tidak punya rute di cakupan Tahap 1, jadi diarahkan ke direktori pilar terkait.
- Tab, filter kategori, dan paginasi tetap `<button>` seperti sumber karena CSS menargetkan elemen itu. Navigasinya lewat `data-href`.
- Beberapa `min-width: 0` ditambahkan langsung di markup Akun Mitra, Pengaduan, dan form klien untuk menghilangkan horizontal scroll, tanpa mengubah file CSS sumber.
- Link publik pada halaman login dan footer kembali ke `/login`, karena halaman publik di luar cakupan.
- Slot Kelola Landing Page berasal dari data dummy, dan tautan pratinjaunya dinonaktifkan.
- Dialog pratinjau laporan tidak memuat kalimat "Draf sudah disimpan." dari sumber, karena Tahap 1 belum menyimpan apa pun. Kembalikan kalimat itu saat penyimpanan tersambung.
- Surat tidak otomatis terbaca saat dibuka seperti di sumber; Tahap 1 memakai tombol Tandai dibaca dengan toast netral.
- Berkas dummy tidak punya isi, jadi dialog surat selalu memakai tampilan "pratinjau tidak didukung". Pratinjau PDF dan gambar dari `lks/letters-actions.js` perlu dipasang di Tahap 2.
- Kotak sukses kredensial akun (`renderSuccessBox` di `admin/partner-accounts.js`) tidak ditampilkan, karena Tahap 1 tidak membuat akun.

## 6. Komentar `TODO(Tahap 2)`

Daftar ini dibuat dari kode. Perbarui dengan `grep -rn "TODO(Tahap 2)" app resources routes`.

- `app/Http/Controllers/Auth/ChangePasswordController.php:13`: tambahkan PUT /account/password dari AccountPasswordController sumber.
- `app/Http/Controllers/Auth/DemoSessionController.php:14`: ganti dengan SessionController sumber (Auth::attempt, throttle, pemeriksaan akun aktif).
- `app/Http/Middleware/EnsureDemoRole.php:12`: ganti dengan middleware auth, EnsureActiveAccount, EnsurePasswordChanged,
- `app/Support/Demo/DemoClientSchema.php:9`: pindahkan ke FormRequest dengan aturan dari shared/data-validation.js.
- `app/Support/Demo/DemoClock.php:8`: ganti dengan now('Asia/Makassar') dan currentPeriod() dari shared/data.js.
- `app/Support/Demo/DemoLanding.php:9`: pindahkan LandingRegistry dan view publik, lalu hapus class ini.
- `app/Support/Demo/DemoPaginator.php:12`: ganti dengan $query->paginate($perPage)->withQueryString().
- `app/Support/Demo/DemoRegions.php:8`: pindahkan dari config/psm.php sumber.
- `app/Support/Demo/DemoRepository.php:13`: ganti turunan class ini dengan query Eloquent.
- `app/Support/Demo/DemoShell.php:11`: baca dari auth()->user() dan window.SidayaWorkflowSummary versi server.
- `app/Support/Demo/DemoState.php:8`: hapus class ini bersama seluruh App\Support\Demo.
- `resources/js/auth/change-password.js:2`: kirim ke PUT /account/password.
- `resources/js/dashboard/dialogs.js:62`: ganti tiap aksi dengan endpoint sumber.
- `resources/js/dashboard/forms.js:2`: kirim ke endpoint sumber setelah validasi lolos.
- `resources/js/pages/client-form.js:2`: kirim ke POST /lks/clients; aturan akhir ada di shared/data-validation.js.
- `resources/js/pages/landing-content.js:2`: PUT dan DELETE /admin/landing-content, POST untuk unggah foto.
- `resources/js/pages/partner-accounts.js:2`: POST /admin/partner-accounts dan POST /admin/partner-accounts/{id}/reset-password.
- `resources/js/pages/report-editor.js:2`: unggah lewat shared/reports-attachments.js.
- `resources/views/admin/account.blade.php:19`: PUT /account/profile.
- `resources/views/admin/correspondence/incoming.blade.php:82`: POST /admin/karang-taruna/activities/{id}/review.
- `resources/views/admin/correspondence/index.blade.php:24`: ekspor dari admin/correspondence-export.js.
- `resources/views/admin/correspondence/reply-form.blade.php:12`: POST /admin/department-replies.
- `resources/views/admin/correspondence/reply.blade.php:48`: POST /admin/department-replies/{id}/publish dan DELETE /admin/department-replies/{id}.
- `resources/views/admin/organizations/index.blade.php:14`: ekspor dari admin/partner-export-lks.js dan admin/partner-export-kt.js.
- `resources/views/admin/organizations/show-kt.blade.php:103`: ekspor dari admin/partner-export-kt.js.
- `resources/views/admin/organizations/show-lks.blade.php:13`: ekspor dari lks/staff-export.js.
- `resources/views/admin/partner-accounts.blade.php:28`: POST /admin/partner-accounts. Kata sandi tidak pernah disimpan di browser.
- `resources/views/admin/psm/complaints.blade.php:32`: GET /admin/psm-complaints/export.
- `resources/views/admin/psm/members.blade.php:13`: ekspor dari admin/partner-export-psm.js.
- `resources/views/admin/registrations/show.blade.php:65`: POST /admin/partner-registrations/{id}/review. Alasan penolakan wajib minimal 10 karakter.
- `resources/views/admin/reports/index.blade.php:65`: ekspor dari shared/report-recap-export.js.
- `resources/views/auth/change-password.blade.php:23`: kirim ke PUT /account/password (AccountPasswordController sumber).
- `resources/views/auth/login.blade.php:97`: form ini memakai autentikasi sungguhan (SessionController sumber).
- `resources/views/lks/members/index.blade.php:22`: ekspor dari lks/staff-export.js.
- `resources/views/lks/profile-dialog.blade.php:1`: PUT /organization/profile, POST /organization/logo, POST /organization/coordinates.
- `resources/views/lks/reports/index.blade.php:13`: ekspor dari lks/reports-export.js.
- `resources/views/lks/reports/new-report-dialog.blade.php:1`: POST /laporan-triwulan.
- `resources/views/shared/clients/recap.blade.php:15`: ekspor dari lks/resident-export.js.
- `resources/views/shared/letters.blade.php:176`: ekspor dari lks/letters-export.js.
- `resources/views/shared/letters.blade.php:303`: POST /lks/letters/{id}/read.
- `resources/views/shared/letters.blade.php:63`: POST /lks/letters (LksLetterController sumber).
- `resources/views/shared/notifications/detail.blade.php:16`: POST /notifications/{id}/unread.
- `resources/views/shared/notifications/index.blade.php:12`: POST /notifications/read-all.
- `resources/views/shared/reports/detail.blade.php:47`: admin/report-download.js.
- `resources/views/shared/reports/table.blade.php:29`: admin/report-download.js.
- `routes/web.php:11`: rute masuk demo diganti SessionController dengan middleware guest dan throttle.
