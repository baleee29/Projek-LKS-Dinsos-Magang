<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /** Membuat akun Admin pertama. Data contoh hanya untuk lingkungan lokal dan pengujian. */
    public function run(): void
    {
        $contoh = app()->environment(['local', 'testing']);
        $sandi = env('ADMIN_PASSWORD') ?: ($contoh ? DataContohSeeder::SANDI_ADMIN : null);
        if (! $sandi) {
            throw new RuntimeException('Isi ADMIN_PASSWORD di .env sebelum menjalankan seeder.');
        }
        Pengguna::firstOrCreate(['email' => env('ADMIN_EMAIL', 'admin@sidaya.example')], [
            'nama' => 'Admin Dinas Sosial',
            'nama_pengguna' => 'admin',
            'telepon' => '080000000001',
            'kata_sandi' => $sandi,
            'peran' => 'admin',
            'jabatan' => 'Dinas Sosial Kota Banjarbaru',
            'preferensi_notifikasi' => ['showUnreadBadge' => true],
        ]);
        if ($contoh) {
            $this->call(DataContohSeeder::class);
        }
    }
}
