<?php

namespace App\Support\Data;

use App\Models\PengurusLks;
use Illuminate\Support\Collection;

/** Pengurus dan SDM dalam bentuk yang dibaca view. */
class DataPengurus
{
    /** Nama isian di form dan view => kolom. */
    public const KOLOM = [
        'title' => 'nama', 'nik' => 'nik', 'birthPlace' => 'tempat_lahir', 'birthDate' => 'tanggal_lahir', 'gender' => 'jenis_kelamin',
        'contact' => 'kontak', 'ktpAddress' => 'alamat_ktp', 'npwp' => 'npwp', 'occupation' => 'pekerjaan', 'category' => 'kategori',
        'position' => 'jabatan', 'joined' => 'tanggal_bergabung', 'notes' => 'catatan',
    ];

    /** @return array<string, mixed> */
    public static function tampil(PengurusLks $pengurus): array
    {
        $baris = ['id' => $pengurus->id, 'organizationId' => $pengurus->lembaga_id, 'name' => $pengurus->nama];
        foreach (self::KOLOM as $isian => $kolom) {
            $nilai = $pengurus->{$kolom};
            $baris[$isian] = $nilai instanceof \DateTimeInterface ? $nilai->format('Y-m-d') : (string) $nilai;
        }

        return $baris + [
            'isChair' => $pengurus->ketua,
            'photo' => $pengurus->foto_berkas_id ? '/berkas/'.$pengurus->foto_berkas_id : '',
            'attachments' => DataBerkas::milikSumber('pengurus')[$pengurus->id] ?? [],
            'version' => $pengurus->versi,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function raw(): Collection
    {
        return Memo::ingat('pengurus', fn () => PengurusLks::orderByDesc('ketua')->orderBy('nama')->get()->map(fn (PengurusLks $pengurus) => self::tampil($pengurus))->values());
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return self::raw();
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return self::raw()->firstWhere('id', $id);
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return self::raw()->where('organizationId', $organizationId)->values();
    }
}
