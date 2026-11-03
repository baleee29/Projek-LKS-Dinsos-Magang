<?php

namespace App\Support\Data;

use App\Models\Pengguna;
use App\Support\Format;

/** Identitas topbar dan sidebar serta lencana menu. */
class DataRangka
{
    /** @return array{name: string, organization: string, initials: string, account: string} */
    public static function identity(Pengguna $akun): array
    {
        if ($akun->peran === 'admin') {
            return [
                'name' => $akun->nama,
                'organization' => (string) $akun->jabatan,
                'initials' => Format::initials($akun->nama),
                'account' => $akun->nama,
            ];
        }
        $lembaga = DataLembaga::own();

        return [
            'name' => $lembaga['chair'] ?: $akun->nama,
            'organization' => $lembaga['title'],
            'initials' => 'L',
            'account' => $lembaga['title'],
        ];
    }

    /** @return array<string, int|null> */
    public static function counts(Pengguna $akun): array
    {
        $belumDibaca = $akun->notifications()->whereNull('read_at')->count();
        if ($akun->peran === 'admin') {
            return [
                'unread' => ($akun->preferensi_notifikasi['showUnreadBadge'] ?? true) ? $belumDibaca : 0,
                'registrationsLKS' => DataPendaftaran::pending() ?: null,
                'unreadLettersLKS' => DataSurat::unreadForAdmin() ?: null,
            ];
        }

        return ['unread' => $belumDibaca, 'unreadLetters' => DataSurat::unreadForLks() ?: null];
    }
}
