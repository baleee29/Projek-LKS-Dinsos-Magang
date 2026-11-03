<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\DemoSessionController;
use App\Http\Controllers\Admin;
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

// TODO(Tahap 2): rute masuk demo diganti SessionController dengan middleware guest dan throttle.
Route::get('/login', [DemoSessionController::class, 'create'])->name('login');
Route::post('/login', [DemoSessionController::class, 'store'])->name('login.store');
Route::post('/login/demo/{role}', [DemoSessionController::class, 'demo'])->name('login.demo');
Route::post('/logout', [DemoSessionController::class, 'destroy'])->name('logout');
Route::get('/account/password', [ChangePasswordController::class, 'edit'])->name('password.edit');

Route::prefix('lks')->middleware('demo.role:lks')->group(function () {
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
});

Route::prefix('admin')->middleware('demo.role:admin')->group(function () {
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
});

// Fallback di dalam grup web agar session terbaca dan halaman 404 memakai kerangka dashboard.
Route::fallback(fn () => abort(404));
