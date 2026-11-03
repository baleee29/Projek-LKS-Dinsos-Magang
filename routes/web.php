<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\DemoSessionController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Lks;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

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
    Route::get('/registrations/{pillar}', [Admin\RegistrationController::class, 'index'])->whereIn('pillar', ['lks', 'karang-taruna']);
    Route::get('/registration/{id}', [Admin\RegistrationController::class, 'show']);
    Route::get('/partner-accounts', Admin\PartnerAccountController::class);
    Route::get('/organizations/{pillar}', [Admin\OrganizationController::class, 'index'])->whereIn('pillar', ['lks', 'karang-taruna']);
    Route::get('/organization/{id}', [Admin\OrganizationController::class, 'show']);
    Route::get('/member/{id}', [Admin\PartnerMemberController::class, 'show']);
    Route::get('/occupancy', [Admin\OccupancyController::class, 'index']);
    Route::get('/resident/{id}', [Admin\OccupancyController::class, 'show']);
    Route::get('/reports/lks', [Admin\ReportController::class, 'index']);
    Route::get('/report/{id}', [Admin\ReportController::class, 'show']);
    Route::get('/letters/lks', Admin\LetterController::class);
    Route::get('/members/psm', [Admin\PartnerMemberController::class, 'psm']);
    Route::get('/complaints/psm', Admin\ComplaintController::class);
    Route::get('/incoming-letters/karang-taruna', [Admin\CorrespondenceController::class, 'incoming']);
    Route::get('/incoming-letter/{id}', [Admin\CorrespondenceController::class, 'incomingShow']);
    Route::get('/reply-letters/karang-taruna', [Admin\CorrespondenceController::class, 'replies']);
    Route::get('/new-reply-letter', [Admin\CorrespondenceController::class, 'replyCreate']);
    Route::get('/reply-letter/{id}', [Admin\CorrespondenceController::class, 'replyShow']);
    Route::get('/edit-reply-letter/{id}', [Admin\CorrespondenceController::class, 'replyEdit']);
    Route::get('/guide', Admin\GuideController::class);
    Route::get('/landing-content', Admin\LandingContentController::class);
    Route::get('/account', Admin\AccountController::class);
});

// Fallback di dalam grup web agar session terbaca dan halaman 404 memakai kerangka dashboard.
Route::fallback(fn () => abort(404));
