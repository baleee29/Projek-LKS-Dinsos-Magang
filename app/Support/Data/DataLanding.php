<?php

namespace App\Support\Data;

use App\Models\IsiLanding;
use App\Support\LandingRegistry;

/** Slot Kelola Landing Page: isi bawaan dari Blade digabung dengan isian tersimpan. */
class DataLanding
{
    /** @return array{max_kb: int, min_width: int, mimes: array<int, string>} */
    public static function imageLimits(): array
    {
        return ['max_kb' => 2048, 'min_width' => 320, 'mimes' => ['image/jpeg', 'image/png', 'image/webp']];
    }

    /** @return array<int, array<string, mixed>> */
    public static function pages(): array
    {
        $isian = IsiLanding::all()->keyBy('kunci');

        return array_map(function (array $page) use ($isian) {
            $page['groups'] = array_map(function (array $group) use ($isian) {
                $group['slots'] = array_map(function (array $slot) use ($isian) {
                    $baris = $isian[$slot['key']] ?? null;
                    $bawaan = $slot['type'] === 'image' ? ($slot['alt'] ?? '') : $slot['default'];

                    return $slot + [
                        'value' => $baris->nilai_teks ?? $bawaan,
                        'image_url' => $baris?->berkas_id ? '/media/'.$baris->berkas_id : $slot['default'],
                        'changed' => $baris !== null,
                    ];
                }, $group['slots']);

                return $group;
            }, $page['groups']);

            return $page;
        }, app(LandingRegistry::class)->pages());
    }
}
