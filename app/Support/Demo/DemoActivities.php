<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: karang_taruna_activities dan karang_taruna_activity_files.
 * Sumber menampilkan Diajukan sebagai Menunggu Pemeriksaan dan Perlu Revisi sebagai Perlu Perbaikan.
 */
class DemoActivities extends DemoRepository
{
    public const TYPES = [
        'baksos' => 'Bakti Sosial & Lingkungan Hidup',
        'pelatihan' => 'Pelatihan / Workshop Kepemudaan',
        'peringatan' => 'Peringatan Hari Besar Nasional (PHBN)',
        'turnamen' => 'Turnamen & Festival Olahraga Pemuda',
        'lainnya' => 'Lainnya (Penyuluhan / Bazaar Komunitas)',
    ];

    public const STATUSES = ['Menunggu Pemeriksaan', 'Ditinjau', 'Perlu Perbaikan', 'Disetujui', 'Ditolak'];

    protected static function seed(): array
    {
        $rows = [];
        $make = function (string $id, string $org, string $title, string $type, string $date, string $status, string $note = '') use (&$rows) {
            $n = count($rows) + 1;
            $created = sprintf('2026-%02d-%02dT10:00:00+08:00', 9 + ($n % 2), 5 + $n);
            $history = [['status' => 'Menunggu Pemeriksaan', 'at' => $created, 'actor' => DemoOrganizations::find($org)['title'], 'note' => 'Pengajuan dikirim.']];
            if ($status !== 'Menunggu Pemeriksaan') {
                $history[] = ['status' => $status, 'at' => sprintf('2026-10-%02dT13:00:00+08:00', 10 + $n), 'actor' => 'Admin', 'note' => $note];
            }

            $rows[] = [
                'id' => $id,
                'organizationId' => $org,
                'title' => $title,
                'type' => $type,
                'date' => $date,
                'location' => 'Balai Warga Contoh, Kota Banjarbaru',
                'participants' => 30 + ($n * 5),
                'budget' => 2500000 + ($n * 500000),
                'description' => 'Kegiatan kepemudaan untuk warga sekitar, disusun bersama pengurus kelompok.',
                'status' => $status,
                'reviewNote' => $note,
                'letterNumber' => sprintf('%03d/KT-DEMO/X/2026', $n),
                'updated' => $history[array_key_last($history)]['at'],
                'createdAt' => $created,
                'submittedAt' => $created,
                'history' => $history,
                'attachments' => [
                    ['id' => $id.'-p', 'kind' => 'proposal', 'name' => 'proposal-kegiatan-demo.pdf', 'size' => 512000, 'type' => 'application/pdf'],
                    ['id' => $id.'-l', 'kind' => 'lainnya', 'name' => 'rincian-anggaran-demo.pdf', 'size' => 128000, 'type' => 'application/pdf'],
                ],
            ];
        };

        $make('act-001', 'kt-001', 'Pelatihan Keterampilan Remaja', 'pelatihan', '2026-11-20', 'Menunggu Pemeriksaan');
        $make('act-002', 'kt-002', 'Permohonan Dukungan Bakti Sosial', 'baksos', '2026-11-28', 'Menunggu Pemeriksaan');
        $make('act-003', 'kt-003', 'Festival Olahraga Pemuda Cempaka', 'turnamen', '2026-12-05', 'Ditinjau');
        $make('act-004', 'kt-004', 'Peringatan Hari Pahlawan Bersama Warga', 'peringatan', '2026-11-10', 'Perlu Perbaikan', 'Lengkapi rincian anggaran.');
        $make('act-005', 'kt-005', 'Bazaar Komunitas Syamsudin', 'lainnya', '2026-12-12', 'Disetujui', 'Kegiatan sesuai rencana kerja.');
        $make('act-006', 'kt-006', 'Penyuluhan Bahaya Narkoba', 'lainnya', '2026-10-30', 'Ditolak', 'Jadwal bertabrakan dengan program kelurahan.');
        $make('act-007', 'kt-001', 'Gerakan Bersih Sungai', 'baksos', '2026-12-20', 'Ditinjau');

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function pending(): Collection
    {
        return static::all()->whereIn('status', ['Menunggu Pemeriksaan', 'Ditinjau'])->values();
    }
}
