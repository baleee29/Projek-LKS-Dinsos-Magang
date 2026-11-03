<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: psm_complaints. Hanya Admin yang membaca di Tahap 1; dashboard PSM di luar cakupan.
 */
class DemoComplaints extends DemoRepository
{
    protected static function seed(): array
    {
        $subjects = [
            'Warga lansia membutuhkan pendampingan',
            'Anak putus sekolah di lingkungan RT',
            'Keluarga rentan belum menerima bantuan',
            'Penyandang disabilitas membutuhkan alat bantu',
            'Rumah warga rusak akibat angin kencang',
        ];
        $rows = [];
        $members = DemoPsmMembers::raw();
        for ($n = 1; $n <= 12; $n++) {
            $member = $members[$n % $members->count()];
            $hasClient = $n % 3 !== 0;
            $hasPoint = $n % 4 !== 0;
            $files = $n % 2 ? [['name' => "dokumentasi-lapangan-{$n}.pdf", 'size' => 180000 + $n * 1000, 'mime' => 'application/pdf']] : [];
            $rows[] = [
                'id' => sprintf('cmp-%03d', $n),
                'complaint_number' => sprintf('PGD-2026-%04d', $n),
                'created_at' => sprintf('2026-%02d-%02dT%02d:20:00+08:00', $n % 2 ? 11 : 10, $n % 2 ? min($n, 3) : 10 + $n, 8 + ($n % 8)),
                'member_id' => $member['id'],
                'member_name' => $member['title'],
                'member_phone' => $member['contact'],
                'district' => $member['district'],
                'village' => $member['village'],
                'client_name' => $hasClient ? 'Klien Fiktif '.$n : '',
                'client_nik' => $hasClient ? str_pad((string) (7000 + $n), 16, '0', STR_PAD_LEFT) : '',
                'client_ktp' => $hasClient && $n % 2 === 0,
                'client_ktp_original_name' => $hasClient && $n % 2 === 0 ? "ktp-demo-{$n}.pdf" : '',
                'latitude' => $hasPoint ? -3.44 - ($n / 200) : null,
                'longitude' => $hasPoint ? 114.80 + ($n / 200) : null,
                'subject' => $subjects[$n % count($subjects)],
                'description' => 'Uraian fiktif untuk demo: anggota melaporkan kendala di lapangan dan meminta tindak lanjut dari Dinas Sosial Kota Banjarbaru.',
                'attachments_list' => $files,
            ];
        }

        return collect($rows)->sortByDesc('created_at')->values()->all();
    }
}
