<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Dasar sumber data dummy. Satu turunan per domain, dengan bentuk field mengikuti sumber.
 *
 * all() menghormati ?demo_state=empty untuk daftar dan ringkasan. find() tetap membaca data
 * lengkap, supaya menu aktif dan halaman detail tidak bergantung pada pratinjau empty state.
 *
 * TODO(Tahap 2): ganti turunan class ini dengan query Eloquent.
 */
abstract class DemoRepository
{
    /** @var array<class-string, Collection<int, array<string, mixed>>> */
    private static array $memo = [];

    /** @return array<int, array<string, mixed>> */
    abstract protected static function seed(): array;

    /** @return Collection<int, array<string, mixed>> */
    public static function raw(): Collection
    {
        return self::$memo[static::class] ??= collect(static::seed())->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return DemoState::isEmpty() ? collect() : static::raw();
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return static::raw()->firstWhere('id', $id);
    }
}
