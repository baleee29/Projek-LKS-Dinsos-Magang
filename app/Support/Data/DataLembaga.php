<?php

namespace App\Support\Data;

use App\Models\Lembaga;
use App\Models\PendaftaranLks;
use Illuminate\Support\Collection;

/** Profil lembaga dalam bentuk yang dibaca view. */
class DataLembaga
{
    /** @return array<string, mixed> */
    public static function tampil(Lembaga $lembaga, ?string $pendaftaranId = null): array
    {
        return [
            'id' => $lembaga->id,
            'pillar' => 'lks',
            'title' => $lembaga->nama,
            'chair' => (string) $lembaga->ketua,
            'district' => (string) $lembaga->kecamatan,
            'village' => (string) $lembaga->kelurahan,
            'address' => (string) $lembaga->alamat,
            'contact' => (string) $lembaga->kontak,
            'legal' => (string) $lembaga->legalitas,
            'term' => (string) $lembaga->masa_bakti,
            'description' => (string) $lembaga->deskripsi,
            'logo' => $lembaga->logo_berkas_id ? '/berkas/'.$lembaga->logo_berkas_id : '',
            'latitude' => $lembaga->lintang,
            'longitude' => $lembaga->bujur,
            'registeredFrom' => $lembaga->terdaftar_sejak?->toDateString() ?? '',
            'registrationId' => $pendaftaranId,
            'status' => 'Aktif',
            'verified' => true,
            'version' => $lembaga->versi,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function raw(): Collection
    {
        return Memo::ingat('lembaga', function () {
            $pendaftaran = PendaftaranLks::whereNotNull('lembaga_id')->pluck('id', 'lembaga_id');

            return Lembaga::orderBy('nama')->get()->map(fn (Lembaga $lembaga) => self::tampil($lembaga, $pendaftaran[$lembaga->id] ?? null))->values();
        });
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return self::raw();
    }

    /** @return array<string, mixed>|null */
    public static function find(?string $id): ?array
    {
        return $id ? self::raw()->firstWhere('id', $id) : null;
    }

    /** Lembaga selalu dari sesi, tidak pernah dari kiriman browser. */
    public static function ownId(): ?string
    {
        return auth()->user()?->lembaga_id;
    }

    /** @return array<string, mixed> */
    public static function own(): array
    {
        $lembaga = self::find(self::ownId());
        abort_unless($lembaga, 403);

        return $lembaga;
    }
}
