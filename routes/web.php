<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Aksi;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\EksporController;
use App\Http\Controllers\Lks;
use Illuminate\Support\Facades\Route;

// Halaman publik memakai URL sumber. Hanya halaman umum dan LKS; pilar lain tidak dibawa.
Route::view('/', 'public.home');
Route::view('/index.html', 'public.home');
Route::view('/profil.html', 'public.profile');
Route::view('/kontak.html', 'public.contact');
Route::view('/pilar-sosial.html', 'public.pillars');
Route::view('/pilar/lks.html', 'public.lks');
Route::view('/pilar/pendaftaran-lks.html', 'public.registration-lks');

// Formulir publik tanpa akun: dibatasi percobaannya dan dicegah kiriman ganda lewat request_id.
Route::post('/pendaftaran/mitra', [Aksi\PendaftaranController::class, 'store'])->middleware('throttle:10,1');
Route::get('/media/{id}', [BerkasController::class, 'media'])->whereUuid('id');

Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
Route::post('/login/demo/{role}', [SessionController::class, 'demo'])->name('login.demo');
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware('peran')->group(function () {
    Route::get('/account/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/account/password', [PasswordController::class, 'update']);
    Route::get('/berkas/{id}', [BerkasController::class, 'unduh'])->whereUuid('id');
    Route::get('/ekspor/klien', [EksporController::class, 'klien']);
    Route::get('/ekspor/pengurus', [EksporController::class, 'pengurus']);
    Route::get('/ekspor/lembaga', [EksporController::class, 'lembaga']);
    Route::get('/ekspor/laporan', [EksporController::class, 'laporan']);
    Route::get('/ekspor/laporan/{id}', [EksporController::class, 'laporanDetail'])->whereUuid('id');
    Route::get('/ekspor/surat', [EksporController::class, 'surat']);
    Route::post('/notifications/read-all', [Aksi\NotifikasiController::class, 'bacaSemua']);
    Route::post('/notifications/{id}/unread', [Aksi\NotifikasiController::class, 'belumDibaca'])->whereUuid('id');
});

Route::prefix('lks')->middleware('peran:lks')->group(function () {
    Route::get('/overview', Lks\OverviewController::class);
    Route::get('/notifications', [Lks\NotificationController::class, 'index']);
    Route::get('/notification-detail/{id}', [Lks\NotificationController::class, 'show']);
    Route::get('/profile', Lks\ProfileController::class);
    Route::get('/members', [Lks\MemberController::class, 'index']);
    Route::get('/new-member', [Lks\MemberController::class, 'create']);
    Route::get('/edit-member/{id}', [Lks\MemberController::class, 'edit']);
    Route::get('/member/{id}', [Lks\MemberController::class, 'show']);
    Route::get('/letters', Lks\LetterController::class);
    Route::get('/new-resident', [Lks\ClientController::class, 'create']);
    Route::get('/edit-resident/{id}', [Lks\ClientController::class, 'edit']);
    Route::get('/occupancy', [Lks\ClientController::class, 'occupancy']);
    Route::get('/resident/{id}', [Lks\ClientController::class, 'show']);
    Route::get('/reports', [Lks\ReportController::class, 'index']);
    Route::get('/drafts', [Lks\ReportController::class, 'drafts']);
    Route::get('/report/{id}', [Lks\ReportController::class, 'show']);
    Route::get('/edit/{id}', [Lks\ReportController::class, 'edit']);
    Route::get('/guide', Lks\GuideController::class);

    Route::post('/profile', [Aksi\ProfilLembagaController::class, 'update']);
    Route::post('/members', [Aksi\PengurusController::class, 'store']);
    Route::post('/members/{id}', [Aksi\PengurusController::class, 'update'])->whereUuid('id');
    Route::delete('/members/{id}', [Aksi\PengurusController::class, 'destroy'])->whereUuid('id');
    Route::post('/clients', [Aksi\KlienController::class, 'store']);
    Route::post('/clients/{id}', [Aksi\KlienController::class, 'update'])->whereUuid('id');
    Route::delete('/clients/{id}', [Aksi\KlienController::class, 'destroy'])->whereUuid('id');
    Route::post('/reports', [Aksi\LaporanController::class, 'bukaPeriode']);
    Route::post('/reports/{id}', [Aksi\LaporanController::class, 'simpan'])->whereUuid('id');
    Route::delete('/reports/{id}', [Aksi\LaporanController::class, 'destroy'])->whereUuid('id');
    Route::post('/letters', [Aksi\SuratController::class, 'store']);
    Route::post('/letters/{id}/read', [Aksi\SuratController::class, 'read'])->whereUuid('id');
    Route::delete('/letters/{id}', [Aksi\SuratController::class, 'destroy'])->whereUuid('id');
});

Route::prefix('admin')->middleware('peran:admin')->group(function () {
    Route::get('/overview', Admin\OverviewController::class);
    Route::get('/notifications', [Admin\NotificationController::class, 'index']);
    Route::get('/notification-detail/{id}', [Admin\NotificationController::class, 'show']);
    Route::get('/registrations/lks', [Admin\RegistrationController::class, 'index']);
    Route::get('/registration/{id}', [Admin\RegistrationController::class, 'show']);
    Route::get('/partner-accounts', Admin\PartnerAccountController::class);
    Route::get('/organizations/lks', [Admin\OrganizationController::class, 'index']);
    Route::get('/organization/{id}', [Admin\OrganizationController::class, 'show']);
    Route::get('/member/{id}', [Admin\OrganizationController::class, 'member']);
    Route::get('/occupancy', [Admin\OccupancyController::class, 'index']);
    Route::get('/resident/{id}', [Admin\OccupancyController::class, 'show']);
    Route::get('/reports/lks', [Admin\ReportController::class, 'index']);
    Route::get('/report/{id}', [Admin\ReportController::class, 'show']);
    Route::get('/letters/lks', Admin\LetterController::class);
    Route::get('/guide', Admin\GuideController::class);
    Route::get('/landing-content', Admin\LandingContentController::class);
    Route::get('/account', Admin\AccountController::class);

    Route::post('/registration/{id}/review', [Aksi\PendaftaranController::class, 'tinjau'])->whereUuid('id');
    Route::post('/partner-accounts', [Aksi\AkunLksController::class, 'store']);
    Route::post('/partner-accounts/{id}/reset-password', [Aksi\AkunLksController::class, 'reset'])->whereNumber('id');
    Route::delete('/partner-accounts/{id}', [Aksi\AkunLksController::class, 'cabut'])->whereNumber('id');
    Route::post('/letters', [Aksi\SuratController::class, 'store']);
    Route::post('/letters/{id}/read', [Aksi\SuratController::class, 'read'])->whereUuid('id');
    Route::delete('/letters/{id}', [Aksi\SuratController::class, 'destroy'])->whereUuid('id');
    Route::post('/landing-content/{kunci}', [Aksi\IsiLandingController::class, 'simpan']);
    Route::post('/landing-content/{kunci}/image', [Aksi\IsiLandingController::class, 'foto']);
    Route::delete('/landing-content/{kunci}', [Aksi\IsiLandingController::class, 'kembalikan']);
});

Route::put('/account/profile', [Aksi\AkunAdminController::class, 'update'])->middleware('peran:admin');

// Fallback di dalam grup web agar session terbaca dan halaman 404 memakai kerangka dashboard.
Route::fallback(fn () => abort(404));
