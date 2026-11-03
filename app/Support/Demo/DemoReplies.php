<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: department_replies. Status: Draf dan Diterbitkan (published_at terisi).
 * Penerbitan tidak menyetujui kegiatan.
 */
class DemoReplies extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        $make = function (string $id, ?string $activity, string $org, string $subject, string $status) use (&$rows) {
            $n = count($rows) + 1;
            $published = $status === 'Diterbitkan' ? sprintf('2026-10-%02dT11:00:00+08:00', 12 + $n) : null;
            $rows[] = [
                'id' => $id,
                'activityId' => $activity,
                'organizationId' => $org,
                'number' => sprintf('460/%03d/DINSOS-DEMO/2026', 100 + $n),
                'date' => sprintf('2026-10-%02d', 12 + $n),
                'subject' => $subject,
                'body' => 'Menindaklanjuti surat pengajuan kelompok, Dinas Sosial menyampaikan tanggapan sebagai berikut. Isi surat ini fiktif untuk keperluan demo.',
                'signatory' => 'Kepala Dinas Sosial Kota Banjarbaru',
                'status' => $status,
                'publishedAt' => $published,
                'updated' => $published ?? sprintf('2026-10-%02dT16:00:00+08:00', 12 + $n),
                'file' => $status === 'Diterbitkan' ? ['name' => "surat-balasan-demo-{$n}.pdf", 'size' => 204000, 'type' => 'application/pdf'] : null,
            ];
        };

        $make('reply-001', 'act-005', 'kt-005', 'Balasan Pengajuan Bazaar Komunitas', 'Diterbitkan');
        $make('reply-002', 'act-003', 'kt-002', 'Balasan Permohonan Dukungan Kegiatan', 'Diterbitkan');
        $make('reply-003', 'act-006', 'kt-006', 'Balasan Pengajuan Penyuluhan', 'Diterbitkan');
        $make('reply-004', 'act-004', 'kt-004', 'Draf Balasan Peringatan Hari Pahlawan', 'Draf');
        $make('reply-005', 'act-001', 'kt-001', 'Draf Balasan Pelatihan Keterampilan', 'Draf');

        return $rows;
    }
}
