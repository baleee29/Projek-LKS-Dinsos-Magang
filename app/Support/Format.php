<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Format tampilan yang di sumber ada di helper JavaScript (workspace-helpers.js, lks/ui.js).
 */
class Format
{
    private const TIMEZONE = 'Asia/Makassar';

    /** Setara Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }). */
    public static function date(?string $value, string $fallback = 'Belum dicatat'): string
    {
        if (! $value) {
            return $fallback;
        }

        return Carbon::parse($value, self::TIMEZONE)->locale('id')->translatedFormat('j M Y');
    }

    public static function dateTime(?string $value, string $fallback = 'Waktu belum dicatat'): string
    {
        if (! $value) {
            return $fallback;
        }

        return Carbon::parse($value, self::TIMEZONE)->locale('id')->translatedFormat('j M Y, H.i').' WITA';
    }

    public static function initials(?string $name, string $fallback = 'K'): string
    {
        $clean = trim((string) preg_replace('/\([^)]*\)/', '', (string) $name)) ?: $fallback;
        $parts = array_slice(preg_split('/\s+/', $clean), 0, 2);

        return mb_strtoupper(implode('', array_map(fn ($part) => mb_substr($part, 0, 1), $parts)));
    }

    public static function maskNik(?string $nik): string
    {
        return preg_match('/^\d{16}$/', (string) $nik)
            ? substr($nik, 0, 6).'••••••'.substr($nik, -4)
            : 'Belum dicatat';
    }

    /** Peta status ke kelas CSS dari workspace-helpers.js. */
    public static function statusClass(string $status): string
    {
        return [
            'Draf' => 'draft',
            'Terkirim' => 'approved',
            'Diajukan' => 'pending',
            'Ditinjau' => 'progress',
            'Perlu Revisi' => 'revision',
            'Disetujui' => 'approved',
            'Diterima' => 'approved',
            'Menunggu Persetujuan' => 'pending',
            'Menunggu Verifikasi' => 'pending',
            'Menunggu Pemeriksaan' => 'pending',
            'Perlu Perbaikan' => 'revision',
            'Ditolak' => 'rejected',
            'Terbit' => 'approved',
            'Diterbitkan' => 'approved',
        ][$status] ?? 'draft';
    }

    /** @param  array<string, mixed>  $client */
    public static function living(array $client): bool
    {
        return $client['serviceType'] === 'Tinggal di panti'
            && $client['residenceStatus'] === 'Masih menerima layanan';
    }

    /** @param  array<string, mixed>  $client */
    public static function existence(array $client): string
    {
        if ($client['residenceStatus'] === 'Sudah keluar') {
            return 'Sudah keluar';
        }

        return self::living($client) ? 'Masih tinggal di panti' : ($client['serviceType'] ?: 'Belum dicatat');
    }

    /** @param  array<string, mixed>  $client */
    public static function age(array $client, string $today): string
    {
        if (! empty($client['birthDateUnknown'])) {
            return ($client['approximateAge'] ?? '') === ''
                ? 'Usia belum diketahui'
                : "± {$client['approximateAge']} tahun (perkiraan)";
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($client['birthDate'] ?? ''))) {
            return 'Usia belum diketahui';
        }

        return Carbon::parse($client['birthDate'])->diffInYears(Carbon::parse($today), true) >= 0
            ? ((int) substr($today, 0, 4) - (int) substr($client['birthDate'], 0, 4) - (substr($today, 5) < substr($client['birthDate'], 5) ? 1 : 0)).' tahun'
            : 'Usia belum diketahui';
    }

    /** @return array{has: bool, text: string, mapUrl: string, embedUrl: string} */
    public static function coordinates(mixed $latitude, mixed $longitude): array
    {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return ['has' => false, 'text' => 'Belum dicatat', 'mapUrl' => '', 'embedUrl' => ''];
        }

        $lat = (float) $latitude;
        $lng = (float) $longitude;
        $latFixed = number_format($lat, 6, '.', '');
        $lngFixed = number_format($lng, 6, '.', '');
        $bbox = implode('%2C', array_map(
            fn (float $value) => number_format($value, 6, '.', ''),
            [$lng - 0.008, $lat - 0.005, $lng + 0.008, $lat + 0.005],
        ));

        return [
            'has' => true,
            'text' => "{$latFixed}, {$lngFixed}",
            'mapUrl' => "https://www.google.com/maps?q={$latFixed},{$lngFixed}",
            'embedUrl' => "https://www.openstreetmap.org/export/embed.html?bbox={$bbox}&layer=mapnik&marker={$latFixed}%2C{$lngFixed}",
        ];
    }
}
