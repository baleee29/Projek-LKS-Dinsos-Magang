<?php

namespace App\Support\Data;

use App\Models\BerkasPendaftaran;
use App\Models\PendaftaranLks;
use Illuminate\Support\Collection;

/** Pendaftaran LKS. Status tersimpan Disetujui ditampilkan sebagai Diterima. */
class DataPendaftaran
{
    /** @return array<string, mixed> */
    public static function tampil(PendaftaranLks $pendaftaran): array
    {
        $dibuat = $pendaftaran->dibuat_pada?->toIso8601String() ?? '';
        $ditinjau = $pendaftaran->ditinjau_pada?->toIso8601String();
        $riwayat = [['status' => PendaftaranLks::MENUNGGU, 'at' => $dibuat, 'actor' => 'Pendaftar', 'note' => 'Pendaftaran dikirim.']];
        if ($ditinjau) {
            $riwayat[] = ['status' => $pendaftaran->status, 'at' => $ditinjau, 'actor' => 'Admin', 'note' => (string) $pendaftaran->catatan];
        }

        return [
            'id' => $pendaftaran->id,
            'pillar' => 'lks',
            'organization_name' => $pendaftaran->nama_lembaga,
            'leader_name' => $pendaftaran->nama_ketua,
            'whatsapp' => $pendaftaran->whatsapp,
            'license_number' => (string) $pendaftaran->nomor_izin,
            'bank_account' => (string) $pendaftaran->rekening_bank,
            'focus_area' => $pendaftaran->bidang_fokus,
            'service_priorities' => $pendaftaran->prioritas_pelayanan ?? [],
            'service_types' => $pendaftaran->jenis_pelayanan ?? [],
            'service_targets' => $pendaftaran->sasaran_pelayanan ?? [],
            'activity_types' => $pendaftaran->jenis_kegiatan ?? [],
            'service_scopes' => $pendaftaran->sifat_pelayanan ?? [],
            'status' => $pendaftaran->status,
            'notes' => (string) $pendaftaran->catatan,
            'organization_id' => $pendaftaran->lembaga_id,
            'files' => $pendaftaran->berkas->map(fn (BerkasPendaftaran $berkas) => [
                'id' => $berkas->berkas_id,
                'key' => $berkas->kunci_berkas,
                'label' => $berkas->label_berkas ?: $berkas->kunci_berkas,
                'name' => (string) $berkas->berkas?->nama,
                'type' => (string) $berkas->berkas?->jenis_mime,
                'size' => (int) $berkas->berkas?->ukuran,
            ])->values()->all(),
            'created_at' => $dibuat,
            'updated_at' => $ditinjau ?? $dibuat,
            'reviewed_at' => $ditinjau,
            'version' => $pendaftaran->versi,
            'history' => $riwayat,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return Memo::ingat('pendaftaran', fn () => PendaftaranLks::with('berkas.berkas')->orderByDesc('dibuat_pada')->get()->map(fn (PendaftaranLks $baris) => self::tampil($baris))->values());
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return self::all()->firstWhere('id', $id);
    }

    public static function pending(): int
    {
        return PendaftaranLks::where('status', PendaftaranLks::MENUNGGU)->count();
    }

    public static function statusLabel(string $status): string
    {
        return $status === PendaftaranLks::DISETUJUI ? 'Diterima' : $status;
    }
}
