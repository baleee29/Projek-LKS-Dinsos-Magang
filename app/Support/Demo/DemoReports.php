<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: quarterly_reports. LKS memakai status aktif Draf/Terkirim (statuses di shared/data.js).
 * Diajukan, Ditinjau, Perlu Revisi, dan Disetujui hanya ada pada arsip lama Karang Taruna,
 * sama seperti legacy reports di shared/workspace-reports.js.
 */
class DemoReports extends DemoRepository
{
    public const ACTIVE_STATUSES = ['Draf', 'Terkirim'];

    protected static function seed(): array
    {
        $rows = [];
        $make = function (string $id, string $org, string $pillar, int $year, int $quarter, string $status, array $extra = []) use (&$rows) {
            $sent = $status !== 'Draf';
            $rows[] = array_merge([
                'id' => $id,
                'organizationId' => $org,
                'pillar' => $pillar,
                'year' => $year,
                'quarter' => $quarter,
                'status' => $status,
                'title' => 'Laporan Kegiatan dan Layanan Semester '.($quarter <= 2 ? 1 : 2)." {$year}",
                'summary' => 'Ringkasan kegiatan, layanan, dan keuangan lembaga selama satu semester.',
                'updated' => sprintf('%d-%02d-12T10:00:00+08:00', $year, $quarter <= 2 ? 6 : 10),
                'submittedAt' => $sent ? sprintf('%d-%02d-20T09:30:00+08:00', $quarter <= 2 ? $year : $year + 1, $quarter <= 2 ? 7 : 1) : null,
                'hasDraftChanges' => false,
                'attachments' => $sent ? [
                    ['id' => $id.'-a1', 'name' => 'laporan-semester-demo.pdf', 'size' => 842000, 'type' => 'application/pdf'],
                    ['id' => $id.'-a2', 'name' => 'rekap-keuangan-demo.xlsx', 'size' => 96000, 'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                ] : [],
                'version' => 1,
            ], $extra);
        };

        $own = DemoOrganizations::OWN_LKS;
        $make('RPT-LKS001-2026-S2', $own, 'lks', 2026, 3, 'Draf', ['title' => 'Laporan Semester 2 2026 (berjalan)', 'updated' => '2026-10-28T14:12:00+08:00']);
        $make('RPT-LKS001-2026-S1', $own, 'lks', 2026, 1, 'Terkirim', ['hasDraftChanges' => true]);
        $make('RPT-LKS001-2025-S2', $own, 'lks', 2025, 3, 'Terkirim');
        $make('RPT-LKS001-2025-S1', $own, 'lks', 2025, 1, 'Terkirim');
        $make('RPT-LKS001-2024-S2', $own, 'lks', 2024, 3, 'Draf', ['title' => 'Draf Laporan Semester 2 2024']);
        $make('RPT-LKS001-2024-S1', $own, 'lks', 2024, 1, 'Terkirim');

        $make('RPT-LKS002-2026-S1', 'lks-002', 'lks', 2026, 1, 'Terkirim');
        $make('RPT-LKS003-2026-S1', 'lks-003', 'lks', 2026, 1, 'Terkirim', ['hasDraftChanges' => true]);
        $make('RPT-LKS004-2025-S2', 'lks-004', 'lks', 2025, 3, 'Terkirim');
        $make('RPT-LKS005-2026-S2', 'lks-005', 'lks', 2026, 3, 'Draf');
        $make('RPT-LKS006-2026-S1', 'lks-006', 'lks', 2026, 1, 'Terkirim');
        $make('RPT-LKS007-2026-S1', 'lks-007', 'lks', 2026, 1, 'Terkirim');

        $make('RPT-KT001-2024-T1', 'kt-001', 'karang-taruna', 2024, 1, 'Diajukan', ['title' => 'Arsip laporan triwulan I 2024']);
        $make('RPT-KT002-2024-T2', 'kt-002', 'karang-taruna', 2024, 2, 'Ditinjau', ['title' => 'Arsip laporan triwulan II 2024']);
        $make('RPT-KT003-2024-T3', 'kt-003', 'karang-taruna', 2024, 3, 'Perlu Revisi', ['title' => 'Arsip laporan triwulan III 2024']);
        $make('RPT-KT004-2024-T4', 'kt-004', 'karang-taruna', 2024, 4, 'Disetujui', ['title' => 'Arsip laporan triwulan IV 2024']);

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return static::all()->where('organizationId', $organizationId)->values();
    }

    /** Admin tidak melihat draf; hanya laporan LKS terkirim. @return Collection<int, array<string, mixed>> */
    public static function submittedLks(): Collection
    {
        return static::all()->where('pillar', 'lks')->where('status', 'Terkirim')->values();
    }

    /** @param  array<string, mixed>  $report */
    public static function periodLabel(array $report): string
    {
        return DemoClock::period((int) $report['year'], (int) $report['quarter'])['label'];
    }
}
