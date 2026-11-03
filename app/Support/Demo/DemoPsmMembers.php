<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: psm_members. Ditambahkan langsung oleh Ketua PSM, tanpa persetujuan Admin.
 */
class DemoPsmMembers extends DemoRepository
{
    protected static function seed(): array
    {
        $names = ['Abdul Rahman', 'Bunga Citra', 'Cahyo Purnomo', 'Dewi Sartika', 'Edi Susanto', 'Fatimah Azzahra', 'Guntur Wibisono', 'Hikmah Lestari', 'Imam Syafii', 'Jamilah Sari', 'Kurniawan Adi', 'Lina Marlina'];
        $occupations = ['Wiraswasta', 'Guru', 'Petani', 'Ibu Rumah Tangga', 'Pedagang'];
        $districts = DemoRegions::districts();

        $rows = [];
        foreach ($names as $index => $name) {
            $n = $index + 1;
            $district = $districts[$index % count($districts)];
            $villages = DemoRegions::villages($district);
            $rows[] = [
                'id' => sprintf('psm-member-%03d', $n),
                'pillar' => 'psm',
                'organizationId' => DemoOrganizations::PSM,
                'title' => $name,
                'name' => $name,
                'position' => $n === 1 ? 'Ketua Kecamatan' : 'Anggota PSM',
                'occupation' => $occupations[$index % count($occupations)],
                'district' => $district,
                'village' => $villages[$index % count($villages)],
                'contact' => sprintf('0800000060%02d', $n),
                'ktpAddress' => "Jl. Contoh Relawan No. {$n}, Kota Banjarbaru",
                'joined' => sprintf('2024-%02d-10', ($n % 12) + 1),
                'nik' => $n % 3 === 0 ? '' : str_pad((string) (6000 + $n), 16, '0', STR_PAD_LEFT),
                'birthPlace' => $n % 3 === 0 ? '' : 'Banjarbaru',
                'birthDate' => $n % 3 === 0 ? '' : sprintf('19%02d-%02d-12', 70 + $n, ($n % 12) + 1),
                'gender' => $n % 2 ? 'Laki-laki' : 'Perempuan',
                'npwp' => '',
                'notes' => $n % 4 === 0 ? 'Aktif mendampingi warga lanjut usia.' : '',
                'isChair' => false,
                'photo' => '',
                'attachments' => $n % 5 === 0 ? [['name' => 'sertifikat-pelatihan-demo.pdf', 'size' => 190000, 'type' => 'application/pdf']] : [],
            ];
        }

        return $rows;
    }
}
