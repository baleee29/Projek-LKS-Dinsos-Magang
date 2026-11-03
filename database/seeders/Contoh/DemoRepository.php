<?php

namespace Database\Seeders\Contoh;

use Illuminate\Support\Collection;

/** Dasar data contoh untuk seeder. Bentuk field-nya dipetakan ke tabel oleh DataContohSeeder. */
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
        return static::raw();
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return static::raw()->firstWhere('id', $id);
    }
}
