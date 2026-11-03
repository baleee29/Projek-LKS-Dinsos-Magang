<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: organization_members (pengurus dan SDM LKS).
 * NIK sengaja berpola 0000… supaya jelas palsu.
 */
class DemoMembers extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        $serial = 100;
        $make = function (string $org, string $title, string $category, string $position, string $gender, array $extra = []) use (&$rows, &$serial) {
            $serial++;
            $rows[] = array_merge([
                'id' => 'member-'.$serial,
                'organizationId' => $org,
                'title' => $title,
                'name' => $title,
                'nik' => str_pad((string) $serial, 16, '0', STR_PAD_LEFT),
                'birthPlace' => 'Banjarbaru',
                'birthDate' => sprintf('19%02d-%02d-%02d', 70 + ($serial % 28), ($serial % 12) + 1, ($serial % 27) + 1),
                'gender' => $gender,
                'contact' => '08000000'.$serial,
                'ktpAddress' => "Jl. Contoh Warga No. {$serial}, Kota Banjarbaru",
                'npwp' => $serial % 3 ? '' : '00.000.000.0-'.$serial.'.000',
                'occupation' => '',
                'category' => $category,
                'position' => $position,
                'joined' => sprintf('20%02d-%02d-01', 18 + ($serial % 8), ($serial % 12) + 1),
                'notes' => '',
                'isChair' => false,
                'photo' => '',
                'attachments' => [],
                'version' => 1,
            ], $extra);
        };

        $own = DemoOrganizations::OWN_LKS;
        $make($own, 'Maryam Lestari', 'Pengurus', 'Ketua Lembaga', 'Perempuan', ['isChair' => true, 'notes' => 'Penanggung jawab lembaga', 'attachments' => [['name' => 'sk-kepengurusan-demo.pdf', 'size' => 248000, 'type' => 'application/pdf']]]);
        $make($own, 'Budi Santoso', 'Pengurus', 'Sekretaris', 'Laki-laki', ['notes' => 'Administrasi dan persuratan']);
        $make($own, 'Citra Melati', 'Pengurus', 'Bendahara', 'Perempuan', ['notes' => 'Pengelolaan keuangan lembaga']);
        $make($own, 'Dian Puspita', 'Staf', 'Staf Administrasi', 'Perempuan', ['notes' => 'Pendataan klien']);
        $make($own, 'Eko Wibowo', 'Staf', 'Staf Keuangan', 'Laki-laki');
        $make($own, 'Farida Hanum', 'Tenaga layanan', 'Pengasuh Utama', 'Perempuan', ['notes' => 'Pendamping asrama putri']);
        $make($own, 'Gunawan Hadi', 'Tenaga layanan', 'Pengasuh', 'Laki-laki', ['notes' => 'Pendamping asrama putra']);
        $make($own, 'Hesti Rahayu', 'Tenaga layanan', 'Pekerja Sosial', 'Perempuan', ['notes' => 'Pendampingan kasus']);
        $make($own, 'Irfan Hakim', 'Relawan', 'Relawan Pendidikan', 'Laki-laki', ['notes' => 'Bimbingan belajar akhir pekan']);
        $make($own, 'Julia Kartika', 'Relawan', 'Relawan Kesehatan', 'Perempuan', ['notes' => 'Pemeriksaan kesehatan rutin']);

        foreach (DemoOrganizations::raw() as $org) {
            if ($org['id'] === $own) {
                continue;
            }
            $make($org['id'], $org['chair'], 'Pengurus', 'Ketua Lembaga', str_contains('Rohani Dewi Nur', explode(' ', $org['chair'])[0]) ? 'Perempuan' : 'Laki-laki', ['isChair' => true, 'notes' => 'Penanggung jawab']);
            $make($org['id'], 'Kurnia Wati '.substr($org['id'], -1), 'Staf', 'Staf Administrasi', 'Perempuan');
            $make($org['id'], 'Lukman Hakim '.substr($org['id'], -1), 'Tenaga layanan', 'Pengasuh', 'Laki-laki');
        }

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return static::all()
            ->where('organizationId', $organizationId)
            ->sort(fn (array $a, array $b) => ((int) $b['isChair'] - (int) $a['isChair']) ?: strcmp($a['title'], $b['title']))
            ->values();
    }
}
