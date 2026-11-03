<?php

namespace App\Support\Demo;

/**
 * Pengganti $landing di halaman publik: selalu memakai isi bawaan yang tertulis di Blade.
 *
 * TODO(Tahap 2): ganti dengan App\Services\LandingContents dari sumber, yang membaca isian Admin dari database.
 */
class DemoLandingContents
{
    public function text(string $key, string $default): string
    {
        return self::render($default);
    }

    public function value(string $key, string $default, string $type = 'line'): string
    {
        return $default;
    }

    public function phoneUri(string $key, string $default): string
    {
        $digits = preg_replace('/\D+/', '', $default);

        return str_starts_with($digits, '0') ? '+62'.substr($digits, 1) : '+'.$digits;
    }

    /** src, srcset, sizes, dan alt untuk tag img. */
    public function img(string $key, string $src, string $alt, string $srcset = '', string $sizes = ''): string
    {
        return collect(['src' => $src, 'srcset' => $srcset, 'sizes' => $sizes, 'alt' => $alt])
            ->filter(fn (string $value, string $name) => $name === 'alt' || $value !== '')
            ->map(fn (string $value, string $name) => $name.'="'.e($value).'"')
            ->implode(' ');
    }

    public static function render(string $text): string
    {
        $html = preg_replace('/\*([^*\n]+)\*/u', '<em>$1</em>', e($text));

        return str_replace("\n", '<br />', $html);
    }
}
