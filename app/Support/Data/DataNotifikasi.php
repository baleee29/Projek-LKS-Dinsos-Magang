<?php

namespace App\Support\Data;

use App\Models\Notifikasi;
use Illuminate\Support\Collection;

/** Notifikasi milik pengguna yang sedang masuk. */
class DataNotifikasi
{
    /** @return array<string, mixed> */
    public static function tampil(Notifikasi $notifikasi): array
    {
        $data = $notifikasi->data;

        return [
            'id' => $notifikasi->id,
            'title' => (string) ($data['title'] ?? ''),
            'message' => (string) ($data['message'] ?? ''),
            'organizationTitle' => (string) ($data['organizationTitle'] ?? ''),
            'periodLabel' => (string) ($data['periodLabel'] ?? ''),
            'at' => $notifikasi->created_at?->toIso8601String() ?? '',
            'read' => $notifikasi->read_at !== null,
            'url' => (string) ($data['url'] ?? ''),
            'note' => (string) ($data['note'] ?? ''),
            'exists' => ! empty($data['fields']),
            'fields' => $data['fields'] ?? [],
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function milikSaya(): Collection
    {
        return Memo::ingat('notifikasi', fn () => auth()->user()->notifications()->get()->map(fn (Notifikasi $notifikasi) => self::tampil($notifikasi))->values());
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return self::milikSaya()->firstWhere('id', $id);
    }
}
