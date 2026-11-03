<?php

namespace App\Support\Demo;

use App\Support\LandingRegistry;

/**
 * Isi Kelola Landing Page: slot dari halaman publik dengan isi bawaan, karena belum ada yang tersimpan.
 *
 * TODO(Tahap 2): gabungkan dengan isian tersimpan seperti LandingContentController sumber.
 */
class DemoLanding
{
    /** @return array{max_kb: int, min_width: int, mimes: array<int, string>} */
    public static function imageLimits(): array
    {
        return ['max_kb' => 2048, 'min_width' => 320, 'mimes' => ['image/jpeg', 'image/png', 'image/webp']];
    }

    /** @return array<int, array<string, mixed>> */
    public static function pages(): array
    {
        return array_map(function (array $page) {
            $page['groups'] = array_map(function (array $group) {
                $group['slots'] = array_map(fn (array $slot) => $slot + [
                    'value' => $slot['type'] === 'image' ? $slot['alt'] : $slot['default'],
                    'image_url' => $slot['default'],
                    'changed' => false,
                ], $group['slots']);

                return $group;
            }, $page['groups']);

            return $page;
        }, (new LandingRegistry)->pages());
    }
}
