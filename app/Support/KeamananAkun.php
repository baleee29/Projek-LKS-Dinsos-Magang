<?php

namespace App\Support;

use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class KeamananAkun
{
    public static function aturanSandi(): Password
    {
        return Password::min(10)->letters()->numbers();
    }

    /** Mengakhiri sesi dan token "ingat saya", kecuali sesi yang sedang dipakai bila disebut. */
    public static function akhiriSesi(Pengguna $akun, ?string $kecuali = null): void
    {
        DB::table(config('session.table'))->where('user_id', $akun->id)
            ->when($kecuali, fn ($query) => $query->where('id', '!=', $kecuali))
            ->delete();
        $akun->forceFill(['remember_token' => Str::random(60)])->saveQuietly();
    }

    /** Tautan WhatsApp berisi pesan. Nomor lokal 08… menjadi 628…. */
    public static function tautanWhatsapp(string $nomor, string $pesan = ''): string
    {
        $angka = preg_replace('/\D+/', '', $nomor);
        $angka = str_starts_with($angka, '0') ? '62'.substr($angka, 1) : $angka;

        return 'https://wa.me/'.$angka.($pesan !== '' ? '?text='.rawurlencode($pesan) : '');
    }
}
