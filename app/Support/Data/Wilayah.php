<?php

namespace App\Support\Data;

/** Kecamatan dan kelurahan Kota Banjarbaru. */
class Wilayah
{
    public const LOCALITIES = [
        'Banjarbaru Selatan' => ['Kemuning', 'Loktabat Selatan', 'Guntung Paikat', 'Sungai Besar'],
        'Banjarbaru Utara' => ['Loktabat Utara', 'Mentaos', 'Komet', 'Sungai Ulin'],
        'Cempaka' => ['Cempaka', 'Bangkal', 'Palam', 'Sungai Tiung'],
        'Landasan Ulin' => ['Landasan Ulin Timur', 'Guntung Manggis', 'Guntung Payung', 'Syamsudin Noor'],
        'Liang Anggang' => ['Landasan Ulin Barat', 'Landasan Ulin Selatan', 'Landasan Ulin Tengah', 'Landasan Ulin Utara'],
    ];

    /** @return array<int, string> */
    public static function districts(): array
    {
        return array_keys(self::LOCALITIES);
    }

    /** @return array<int, string> */
    public static function villages(?string $district = null): array
    {
        return $district !== null && $district !== ''
            ? (self::LOCALITIES[$district] ?? [])
            : array_merge(...array_values(self::LOCALITIES));
    }
}
