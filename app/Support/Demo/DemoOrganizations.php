<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: organizations. Seluruh nama, kontak, dan legalitas fiktif.
 */
class DemoOrganizations extends DemoRepository
{
    /** Lembaga milik akun LKS demo. Halaman LKS hanya menampilkan data lembaga ini. */
    public const OWN_LKS = 'lks-001';

    public const PSM = 'psm-001';

    protected static function seed(): array
    {
        $rows = [];
        $lks = [
            ['Panti Asuhan Cahaya Kasih', 'Maryam Lestari', 'Banjarbaru Selatan', 'Loktabat Selatan', -3.4581, 114.8312, true],
            ['LKSA Pelita Harapan', 'Yusuf Pratama', 'Banjarbaru Utara', 'Mentaos', -3.4412, 114.8403, true],
            ['Yayasan Griya Lansia Sejahtera', 'Rohani Wulandari', 'Cempaka', 'Bangkal', -3.5021, 114.8702, true],
            ['LKS Disabilitas Mandiri Berkarya', 'Ahmad Fauzan', 'Landasan Ulin', 'Guntung Payung', -3.4388, 114.7721, false],
            ['Panti Asuhan Bintang Timur', 'Dewi Anggraini', 'Liang Anggang', 'Landasan Ulin Tengah', -3.4217, 114.7433, true],
            ['LKSA Tunas Bangsa', 'Hendra Saputra', 'Banjarbaru Utara', 'Komet', -3.4356, 114.8288, true],
            ['Yayasan Peduli Sesama Banua', 'Nur Aisyah', 'Cempaka', 'Palam', -3.4889, 114.8215, false],
            ['LKS Rumah Singgah Mentari', 'Bambang Irawan', 'Banjarbaru Selatan', 'Kemuning', -3.4633, 114.8197, true],
        ];
        foreach ($lks as $index => [$title, $chair, $district, $village, $lat, $lng, $coords]) {
            $n = $index + 1;
            $rows[] = [
                'id' => sprintf('lks-%03d', $n),
                'pillar' => 'lks',
                'title' => $title,
                'chair' => $chair,
                'district' => $district,
                'village' => $village,
                'address' => "Jl. Contoh Sejahtera No. {$n}, Kel. {$village}, Kec. {$district}, Kota Banjarbaru",
                'contact' => sprintf('0800000010%02d', $n),
                'legal' => "Akta Pendirian: 00{$n}/DEMO/2019\nTanda Daftar Yayasan: TD-00{$n}/DEMO/2020\nNPWP Lembaga: 00.000.000.{$n}-000.000",
                'term' => $n % 2 ? '2024–2029' : '',
                'logo' => '',
                'latitude' => $coords ? $lat : null,
                'longitude' => $coords ? $lng : null,
                'registeredFrom' => sprintf('20%02d-%02d-15', 18 + ($n % 5), ($n % 12) + 1),
                'registrationId' => $n <= 3 ? sprintf('reg-lks-%03d', 10 + $n) : null,
                'status' => 'Aktif',
                'verified' => true,
                'version' => 1,
            ];
        }

        $groups = [
            ['Karang Taruna Bina Remaja', 'Rizky Maulana', 'Banjarbaru Selatan', 'Kemuning', -3.4640, 114.8240],
            ['Karang Taruna Muda Berkarya', 'Salsabila Putri', 'Banjarbaru Utara', 'Sungai Ulin', -3.4300, 114.8520],
            ['Karang Taruna Cempaka Bersatu', 'Fajar Nugroho', 'Cempaka', 'Cempaka', -3.4950, 114.8480],
            ['Karang Taruna Guntung Mandiri', 'Annisa Rahma', 'Landasan Ulin', 'Guntung Manggis', -3.4510, 114.7960],
            ['Karang Taruna Syamsudin Jaya', 'Dimas Prakoso', 'Landasan Ulin', 'Syamsudin Noor', -3.4410, 114.7610],
            ['Karang Taruna Anggang Sejati', 'Mega Sari', 'Liang Anggang', 'Landasan Ulin Barat', -3.4190, 114.7290],
        ];
        foreach ($groups as $index => [$title, $chair, $district, $village, $lat, $lng]) {
            $n = $index + 1;
            $rows[] = [
                'id' => sprintf('kt-%03d', $n),
                'pillar' => 'karang-taruna',
                'title' => $title,
                'chair' => $chair,
                'district' => $district,
                'village' => $village,
                'address' => "Jl. Contoh Pemuda No. {$n}, Kel. {$village}, Kec. {$district}, Kota Banjarbaru",
                'contact' => sprintf('0800000020%02d', $n),
                'legal' => "SK Pengukuhan: 00{$n}/KT-DEMO/2023",
                'term' => '2023–2028',
                'logo' => '',
                'latitude' => $lat,
                'longitude' => $lng,
                'registeredFrom' => sprintf('2023-%02d-10', $n + 1),
                'registrationId' => $n <= 2 ? sprintf('reg-kt-%03d', 10 + $n) : null,
                'status' => 'Aktif',
                'verified' => true,
                'version' => 1,
            ];
        }

        $rows[] = [
            'id' => self::PSM,
            'pillar' => 'psm',
            'title' => 'PSM Kota Banjarbaru',
            'chair' => 'Ketua PSM Banjarbaru',
            'district' => 'Banjarbaru Utara',
            'village' => 'Mentaos',
            'address' => 'Jl. Contoh Pengabdian No. 1, Kel. Mentaos, Kec. Banjarbaru Utara, Kota Banjarbaru',
            'contact' => '080000003001',
            'legal' => '',
            'term' => '',
            'logo' => '',
            'latitude' => null,
            'longitude' => null,
            'registeredFrom' => '2022-01-10',
            'registrationId' => null,
            'status' => 'Aktif',
            'verified' => true,
            'version' => 1,
        ];

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function byPillar(string $pillar): Collection
    {
        return static::all()->where('pillar', $pillar)->values();
    }

    /** @return array<string, mixed> */
    public static function own(): array
    {
        return static::find(self::OWN_LKS);
    }
}
