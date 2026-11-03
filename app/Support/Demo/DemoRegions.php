<?php

namespace App\Support\Demo;

/**
 * Kecamatan dan kelurahan Kota Banjarbaru, sama dengan LOCALITIES di admin/psm-complaints.js.
 *
 * TODO(Tahap 2): pindahkan dari config/psm.php sumber.
 */
class DemoRegions
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
        return $district
            ? (self::LOCALITIES[$district] ?? [])
            : array_merge(...array_values(self::LOCALITIES));
    }
}
