<?php

namespace App\Support;

/**
 * Lists the editable slots of the public pages by reading the $landing->text()/value()/img()
 * calls in their Blade files, so each default text lives only in Blade.
 */
class LandingRegistry
{
    private const TAG_LABELS = [
        'h1' => 'Judul utama', 'h2' => 'Judul bagian', 'h3' => 'Subjudul', 'h4' => 'Subjudul kecil', 'h5' => 'Subjudul kecil',
        'p' => 'Paragraf', 'li' => 'Butir daftar', 'dt' => 'Istilah', 'dd' => 'Keterangan', 'figcaption' => 'Keterangan foto',
        'address' => 'Alamat', 'blockquote' => 'Kutipan', 'summary' => 'Pertanyaan', 'th' => 'Judul kolom', 'td' => 'Isi tabel',
        'span' => 'Label', 'small' => 'Keterangan singkat', 'strong' => 'Teks tebal', 'b' => 'Teks tebal', 'label' => 'Label', 'caption' => 'Judul tabel',
    ];

    private const VALUE_LABELS = [
        'dinsos.phone' => 'Nomor telepon', 'dinsos.email' => 'Alamat email',
        'dinsos.address' => 'Alamat kantor lengkap', 'dinsos.maps' => 'Tautan Google Maps',
    ];

    private ?array $pages = null;

    private ?array $slots = null;

    /** Pages → groups → slots, in page order. */
    public function pages(): array
    {
        if ($this->pages !== null) {
            return $this->pages;
        }
        $pages = [];
        $shared = [];
        foreach (config('landing.pages') as $pageKey => $page) {
            $groups = [];
            foreach ($this->scan(resource_path('views/'.$page['view'])) as $slot) {
                $prefix = explode('.', $slot['key'])[0];
                if (array_key_exists($prefix, config('landing.shared'))) {
                    $shared[$slot['key']] ??= $slot + ['pages' => []];
                    $shared[$slot['key']]['pages'][$pageKey] = $page['label'];

                    continue;
                }
                if ($prefix !== $pageKey) {
                    continue;
                }
                $group = explode('.', $slot['key'])[1] ?? 'utama';
                $groups[$group] ??= ['key' => $group, 'label' => null, 'slots' => []];
                $groups[$group]['label'] ??= in_array($slot['tag'], ['h1', 'h2'], true) ? self::plain($slot['default']) : null;
                $groups[$group]['slots'][] = $slot;
            }
            foreach ($groups as &$group) {
                $group['label'] ??= ucfirst(str_replace('-', ' ', $group['key']));
            }
            unset($group);
            $pages[] = ['key' => $pageKey, 'label' => $page['label'], 'url' => $page['url'], 'groups' => array_values($groups)];
        }
        foreach (config('landing.shared') as $prefix => $label) {
            $slots = array_values(array_filter($shared, fn (array $slot) => str_starts_with($slot['key'], $prefix.'.')));
            array_unshift($pages, ['key' => $prefix, 'label' => $label, 'url' => '/kontak.html', 'shared' => true, 'groups' => [['key' => 'utama', 'label' => $label, 'slots' => $slots]]]);
        }

        return $this->pages = $pages;
    }

    public function find(string $key): ?array
    {
        if ($this->slots === null) {
            $this->slots = [];
            foreach ($this->pages() as $page) {
                foreach ($page['groups'] as $group) {
                    foreach ($group['slots'] as $slot) {
                        $this->slots[$slot['key']] = $slot;
                    }
                }
            }
        }

        return $this->slots[$key] ?? null;
    }

    private function scan(string $file): array
    {
        $source = (string) @file_get_contents($file);
        $slots = [];
        preg_match_all('/\$landing->(text|value|img)\(/', $source, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[1] as $index => [$method]) {
            $start = $matches[0][$index][1];
            $args = self::arguments($source, $start + strlen($matches[0][$index][0]));
            if (count($args) < 2 || isset($slots[$args[0]])) {
                continue;
            }
            $tag = preg_match('/<([a-z0-9]+)\b[^<>]*>\s*$/i', substr($source, max(0, $start - 400), min(400, $start) - 4), $m) ? strtolower($m[1]) : '';
            $slots[$args[0]] = match ($method) {
                'img' => ['key' => $args[0], 'type' => 'image', 'tag' => 'img', 'label' => 'Foto', 'default' => $args[1], 'alt' => $args[2] ?? ''],
                'value' => ['key' => $args[0], 'type' => $args[2] ?? 'line', 'tag' => $tag, 'label' => self::VALUE_LABELS[$args[0]] ?? 'Isian', 'default' => $args[1], 'max' => 300],
                default => ['key' => $args[0], 'type' => 'text', 'tag' => $tag, 'label' => self::TAG_LABELS[$tag] ?? 'Teks', 'default' => $args[1],
                    'max' => min(3000, max(80, (int) ceil(mb_strlen($args[1]) * 2))), 'multiline' => str_contains($args[1], "\n") || mb_strlen($args[1]) > 90],
            };
        }

        return array_values($slots);
    }

    /** Reads the single-quoted PHP string arguments that start at $offset. */
    private static function arguments(string $source, int $offset): array
    {
        $args = [];
        $length = strlen($source);
        for ($i = $offset; $i < $length; $i++) {
            $char = $source[$i];
            if ($char === ')') {
                break;
            }
            if ($char !== "'") {
                continue;
            }
            $value = '';
            for ($i++; $i < $length && $source[$i] !== "'"; $i++) {
                if ($source[$i] === '\\' && in_array($source[$i + 1] ?? '', ["'", '\\'], true)) {
                    $i++;
                }
                $value .= $source[$i];
            }
            $args[] = $value;
        }

        return $args;
    }

    private static function plain(string $text): string
    {
        return trim(str_replace(['*', "\n"], ['', ' '], $text));
    }
}
