<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\DemoSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// TODO(Tahap 2): rute masuk demo diganti SessionController dengan middleware guest dan throttle.
Route::get('/login', [DemoSessionController::class, 'create'])->name('login');
Route::post('/login', [DemoSessionController::class, 'store'])->name('login.store');
Route::post('/login/demo/{role}', [DemoSessionController::class, 'demo'])->name('login.demo');
Route::post('/logout', [DemoSessionController::class, 'destroy'])->name('logout');
Route::get('/account/password', [ChangePasswordController::class, 'edit'])->name('password.edit');
