<?php

namespace App\Services;

use App\Models\IsiLanding;
use App\Support\Data\Memo;
use Illuminate\Support\Collection;

/** $landing di halaman publik: isian Admin bila ada, selain itu teks bawaan yang tertulis di Blade. */
class IsiHalaman
{
    /** @return Collection<string, IsiLanding> */
    private function isian(): Collection
    {
        return Memo::ingat('isi-landing', fn () => IsiLanding::all()->keyBy('kunci'));
    }

    public function text(string $key, string $default): string
    {
        return self::render($this->isian()[$key]->nilai_teks ?? $default);
    }

    public function value(string $key, string $default, string $type = 'line'): string
    {
        return $this->isian()[$key]->nilai_teks ?? $default;
    }

    public function phoneUri(string $key, string $default): string
    {
        $digits = preg_replace('/\D+/', '', $this->value($key, $default));

        return str_starts_with($digits, '0') ? '+62'.substr($digits, 1) : '+'.$digits;
    }

    /** src, srcset, sizes, dan alt untuk tag img. */
    public function img(string $key, string $src, string $alt, string $srcset = '', string $sizes = ''): string
    {
        $isian = $this->isian()[$key] ?? null;
        if ($isian?->berkas_id) {
            [$src, $srcset, $sizes] = ['/media/'.$isian->berkas_id, '', ''];
        }
        $alt = $isian->nilai_teks ?? $alt;

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
