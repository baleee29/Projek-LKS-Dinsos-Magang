<?php

namespace App\Support\Data;

/** Tanggal dan periode laporan menurut WITA. Di view, semester 2 memakai quarter = 3 seperti sumber. */
class Kalender
{
    public static function today(): string
    {
        return now('Asia/Makassar')->toDateString();
    }

    public static function year(): int
    {
        return (int) now('Asia/Makassar')->year;
    }

    public static function quarter(): int
    {
        return (int) now('Asia/Makassar')->month <= 6 ? 1 : 3;
    }

    /** @return array{year: int, quarter: int, label: string, start: string, end: string, interim: bool} */
    public static function period(?int $year = null, ?int $quarter = null): array
    {
        $year ??= self::year();
        $quarter ??= self::quarter();
        $first = $quarter <= 2;
        $end = $year.($first ? '-06-30' : '-12-31');

        return [
            'year' => $year,
            'quarter' => $quarter,
            'label' => 'Semester '.($first ? '1' : '2').' '.$year,
            'start' => $year.($first ? '-01-01' : '-07-01'),
            'end' => $end,
            'interim' => self::today() <= $end,
        ];
    }
}
