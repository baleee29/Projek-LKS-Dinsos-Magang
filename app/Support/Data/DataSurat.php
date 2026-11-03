<?php

namespace App\Support\Data;

use App\Models\SuratLks;
use Illuminate\Support\Collection;

/** Surat. View tetap memakai type incoming (Dinas ke LKS) dan outgoing (LKS ke Dinas). */
class DataSurat
{
    /** @return array<string, mixed> */
    public static function tampil(SuratLks $surat): array
    {
        return [
            'id' => $surat->id,
            'organization_id' => $surat->lembaga_id,
            'type' => $surat->arah === SuratLks::DINAS_KE_LKS ? 'incoming' : 'outgoing',
            'letter_number' => (string) $surat->nomor_surat,
            'letter_date' => $surat->tanggal_surat->toDateString(),
            'subject' => $surat->perihal,
            'description' => (string) $surat->keterangan,
            'sender' => (string) $surat->pengirim,
            'destination' => (string) $surat->tujuan,
            'is_read' => $surat->dibaca_pada !== null,
            'has_file' => $surat->berkas !== null,
            'file_id' => $surat->berkas_id,
            'file_name' => (string) $surat->berkas?->nama,
            'file_type' => (string) $surat->berkas?->jenis_mime,
            'file_size' => (int) $surat->berkas?->ukuran,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return Memo::ingat('surat', fn () => SuratLks::with('berkas')->orderByDesc('tanggal_surat')->get()->map(fn (SuratLks $surat) => self::tampil($surat))->values());
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forLks(string $organizationId): Collection
    {
        return self::all()->where('organization_id', $organizationId)->values();
    }

    public static function unreadForLks(): int
    {
        return SuratLks::where('lembaga_id', DataLembaga::ownId())->where('arah', SuratLks::DINAS_KE_LKS)->whereNull('dibaca_pada')->count();
    }

    public static function unreadForAdmin(): int
    {
        return SuratLks::where('arah', SuratLks::LKS_KE_DINAS)->whereNull('dibaca_pada')->count();
    }

    public static function fileSize(int $bytes): string
    {
        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : number_format($bytes / 1024, 0, ',', '.').' KB';
    }
}
