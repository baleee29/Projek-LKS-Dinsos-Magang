<?php

use App\Models\Pengguna;
use App\Support\KeamananAkun;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pemulihan bila Admin lupa sandi. Hanya pemegang akses server yang bisa menjalankannya.
Artisan::command('account:reset-admin {email : Email akun Admin}', function (string $email) {
    $akun = Pengguna::where('peran', 'admin')->where('email', strtolower($email))->first();
    if (! $akun) {
        $this->error('Akun Admin dengan email itu tidak ditemukan.');

        return 1;
    }
    $sandi = 'ADM#'.str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT);
    $akun->forceFill(['kata_sandi' => $sandi, 'wajib_ganti_sandi' => true, 'aktif' => true])->save();
    KeamananAkun::akhiriSesi($akun);
    $this->info('Kata sandi sementara: '.$sandi);
    $this->line('Sandi ini wajib diganti saat masuk berikutnya.');

    return 0;
})->purpose('Membuat kata sandi sementara untuk akun Admin');
