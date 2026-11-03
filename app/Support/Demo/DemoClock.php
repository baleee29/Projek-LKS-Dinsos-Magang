<?php

namespace App\Support\Demo;

/**
 * Tanggal tetap untuk data dummy, supaya jumlah dan periode di semua halaman selalu konsisten.
 *
 * TODO(Tahap 2): ganti dengan now('Asia/Makassar') dan currentPeriod() dari shared/data.js.
 */
class DemoClock
{
    public const TODAY = '2026-11-03';

    public const YEAR = 2026;

    /** Semester berjalan: Juli–Desember. Sumber menyimpan semester 2 sebagai quarter = 3. */
    public const QUARTER = 3;

    /** @return array{year: int, quarter: int, label: string, start: string, end: string, interim: bool} */
    public static function period(int $year = self::YEAR, int $quarter = self::QUARTER): array
    {
        $first = $quarter <= 2;
        $start = $year.($first ? '-01-01' : '-07-01');
        $end = $year.($first ? '-06-30' : '-12-31');

        return [
            'year' => $year,
            'quarter' => $quarter,
            'label' => 'Semester '.($first ? '1' : '2').' '.$year,
            'start' => $start,
            'end' => $end,
            'interim' => self::TODAY <= $end,
        ];
    }
}
