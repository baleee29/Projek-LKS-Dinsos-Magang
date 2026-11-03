<?php

namespace App\Support\Data;

use App\Models\LaporanSemester;
use Illuminate\Support\Collection;

/** Laporan semester. Admin melihat kiriman terakhir; LKS melihat draf perubahannya sendiri. */
class DataLaporan
{
    public const ACTIVE_STATUSES = ['Draf', 'Terkirim'];

    /** @return array<string, mixed> */
    public static function tampil(LaporanSemester $laporan, bool $admin): array
    {
        $draf = $admin ? null : $laporan->draf_data;

        return [
            'id' => $laporan->id,
            'organizationId' => $laporan->lembaga_id,
            'pillar' => 'lks',
            'year' => $laporan->tahun,
            'quarter' => $laporan->semester === 1 ? 1 : 3,
            'status' => $laporan->status,
            'title' => (string) ($draf['judul'] ?? $laporan->judul),
            'summary' => (string) ($laporan->data['ringkasan'] ?? ''),
            'updated' => $laporan->diperbarui_pada?->toIso8601String() ?? '',
            'submittedAt' => $laporan->dikirim_pada?->toIso8601String(),
            'hasDraftChanges' => ! $admin && ! empty($laporan->draf_data),
            'attachments' => DataBerkas::daftar($draf['lampiran'] ?? $laporan->lampiran),
            'version' => $laporan->versi,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return Memo::ingat('laporan', function () {
            $admin = auth()->user()?->peran === 'admin';
            $query = LaporanSemester::orderByDesc('tahun')->orderByDesc('semester');
            // Admin tidak boleh membaca draf.
            $admin ? $query->where('status', LaporanSemester::TERKIRIM) : $query->where('lembaga_id', DataLembaga::ownId());

            return $query->get()->map(fn (LaporanSemester $laporan) => self::tampil($laporan, $admin))->values();
        });
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return self::all()->where('organizationId', $organizationId)->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function submitted(): Collection
    {
        return self::all()->where('status', 'Terkirim')->values();
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return self::all()->firstWhere('id', $id);
    }

    /** @param  array<string, mixed>  $report */
    public static function periodLabel(array $report): string
    {
        return Kalender::period((int) $report['year'], (int) $report['quarter'])['label'];
    }
}
