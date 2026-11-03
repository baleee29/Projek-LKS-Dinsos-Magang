<?php

namespace App\Support\Data;

use App\Models\Pengguna;
use Illuminate\Support\Collection;

class DataAkun
{
    /** Hanya akun LKS yang masih aktif. @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return Memo::ingat('akun', fn () => Pengguna::where('peran', 'lks')->where('aktif', true)->orderBy('nama')->get()->map(fn (Pengguna $akun) => [
            'id' => $akun->id,
            'name' => $akun->nama,
            'role' => $akun->peran,
            'username' => (string) $akun->nama_pengguna,
            'email' => $akun->email,
            'phone' => (string) $akun->telepon,
            'created_at' => $akun->dibuat_pada?->toDateString() ?? '',
        ])->values());
    }

    /** @return array<string, mixed> */
    public static function admin(): array
    {
        $akun = auth()->user();

        return [
            'name' => $akun->nama,
            'position' => (string) $akun->jabatan,
            'email' => $akun->email,
            'phone' => (string) $akun->telepon,
            'notificationPreferences' => ['showUnreadBadge' => $akun->preferensi_notifikasi['showUnreadBadge'] ?? true],
            'version' => $akun->versi,
        ];
    }
}
