<?php

namespace App\Services;

use App\Models\Pengguna;
use App\Notifications\NotifikasiDashboard;
use Illuminate\Support\Facades\Notification;

class Pemberitahu
{
    /** @param  array<string, mixed>  $isi */
    public static function keAdmin(array $isi): void
    {
        Notification::sendNow(Pengguna::where('peran', 'admin')->where('aktif', true)->get(), new NotifikasiDashboard($isi));
    }

    /** @param  array<string, mixed>  $isi */
    public static function keLembaga(string $lembagaId, array $isi): void
    {
        Notification::sendNow(Pengguna::where('peran', 'lks')->where('lembaga_id', $lembagaId)->where('aktif', true)->get(), new NotifikasiDashboard($isi));
    }

    /** @param  array<string, mixed>  $isi */
    public static function ke(Pengguna $akun, array $isi): void
    {
        Notification::sendNow($akun, new NotifikasiDashboard($isi));
    }
}
