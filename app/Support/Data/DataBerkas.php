<?php

namespace App\Support\Data;

use App\Models\BerkasTersimpan;
use Illuminate\Support\Collection;

class DataBerkas
{
    /** @return array{id: string, name: string, size: int, type: string, url: string} */
    public static function tampil(BerkasTersimpan $berkas): array
    {
        return ['id' => $berkas->id, 'name' => $berkas->nama, 'size' => $berkas->ukuran, 'type' => $berkas->jenis_mime, 'url' => '/berkas/'.$berkas->id];
    }

    /**
     * @param  array<int, string>|null  $ids
     * @return array<int, array<string, mixed>>
     */
    public static function daftar(?array $ids): array
    {
        if (! $ids) {
            return [];
        }
        $berkas = BerkasTersimpan::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->filter(fn (string $id) => isset($berkas[$id]))->map(fn (string $id) => self::tampil($berkas[$id]))->values()->all();
    }

    /** @return Collection<string, array<int, array<string, mixed>>> */
    public static function milikSumber(string $jenis): Collection
    {
        return Memo::ingat('berkas.'.$jenis, fn () => BerkasTersimpan::where('jenis_sumber', $jenis)->orderBy('dibuat_pada')->get()
            ->groupBy('id_sumber')->map(fn ($grup) => $grup->map(fn (BerkasTersimpan $berkas) => self::tampil($berkas))->values()->all()));
    }
}
