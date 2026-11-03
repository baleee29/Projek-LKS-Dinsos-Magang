<?php

namespace App\Support\Demo;

/**
 * Isi Kelola Landing Page. Sumber: config/landing.php dan App\Support\LandingRegistry.
 * Di sumber slot dibaca dari Blade publik; halaman publik di luar cakupan Tahap 1, jadi slot dummy ditulis di sini.
 *
 * TODO(Tahap 2): pindahkan LandingRegistry dan view publik, lalu hapus class ini.
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
        $text = fn (string $key, string $label, string $default, string $tag = 'p', string $value = null) => [
            'key' => $key, 'label' => $label, 'type' => 'text', 'default' => $default, 'value' => $value ?? $default,
            'changed' => $value !== null, 'max' => 500, 'tag' => $tag,
        ];
        $image = fn (string $key, string $label, string $alt, string $url, bool $changed = false) => [
            'key' => $key, 'label' => $label, 'type' => 'image', 'alt' => $alt, 'value' => $alt,
            'image_url' => $url, 'changed' => $changed, 'max' => 300,
        ];
        $photo = '/assets/images/landing/foto-dinsos-584.webp';
        $placeholder = '/assets/images/landing/gallery-placeholder.svg';
        $page = fn (string $key, string $label, array $groups) => ['key' => $key, 'label' => $label, 'groups' => $groups];

        return [
            $page('home', 'Beranda', [
                ['key' => 'hero', 'label' => 'Terhubung untuk Berdaya', 'slots' => [
                    $text('home.hero.01', 'Judul utama', 'Terhubung untuk Berdaya', 'h1'),
                    $text('home.hero.02', 'Paragraf', 'Informasi pemberdayaan sosial untuk masyarakat Kota Banjarbaru.', 'p', 'Informasi layanan pemberdayaan sosial bagi warga Kota Banjarbaru.'),
                    $image('home.hero.03', 'Foto utama', 'Kegiatan layanan sosial', $photo, true),
                ]],
                ['key' => 'galeri', 'label' => 'Galeri kegiatan', 'slots' => [
                    $text('home.galeri.01', 'Judul bagian', 'Galeri kegiatan', 'h2'),
                    $image('home.galeri.02', 'Foto galeri 1', 'Dokumentasi kegiatan', $placeholder),
                    $image('home.galeri.03', 'Foto galeri 2', 'Dokumentasi kegiatan', $placeholder),
                ]],
            ]),
            $page('profile', 'Profil', [
                ['key' => 'utama', 'label' => 'Kenali SiDaya', 'slots' => [
                    $text('profile.utama.01', 'Judul utama', 'Kenali SiDaya', 'h1'),
                    $text('profile.utama.02', 'Paragraf', 'SiDaya menghubungkan pilar sosial dengan Dinas Sosial Kota Banjarbaru.'),
                ]],
            ]),
            $page('contact', 'Kontak', [
                ['key' => 'utama', 'label' => 'Hubungi Dinas Sosial', 'slots' => [
                    $text('contact.utama.01', 'Judul utama', 'Hubungi Dinas Sosial', 'h1'),
                    $text('contact.utama.02', 'Alamat', 'Alamat contoh Dinas Sosial, Kota Banjarbaru.'),
                ]],
            ]),
            $page('pillars', 'Pilar Sosial', [
                ['key' => 'utama', 'label' => 'Pilar Sosial', 'slots' => [
                    $text('pillars.utama.01', 'Judul utama', 'Pilar Sosial Kota Banjarbaru', 'h1'),
                ]],
            ]),
            $page('lks', 'LKS', [
                ['key' => 'utama', 'label' => 'Lembaga Kesejahteraan Sosial', 'slots' => [
                    $text('lks.utama.01', 'Judul utama', 'Lembaga Kesejahteraan Sosial', 'h1'),
                    $image('lks.utama.02', 'Foto utama', 'Kegiatan LKS', $placeholder),
                ]],
            ]),
            $page('psm', 'PSM', [
                ['key' => 'utama', 'label' => 'Pekerja Sosial Masyarakat', 'slots' => [
                    $text('psm.utama.01', 'Judul utama', 'Pekerja Sosial Masyarakat', 'h1'),
                ]],
            ]),
            $page('karang-taruna', 'Karang Taruna', [
                ['key' => 'utama', 'label' => 'Karang Taruna', 'slots' => [
                    $text('karang-taruna.utama.01', 'Judul utama', 'Karang Taruna', 'h1'),
                ]],
            ]),
            $page('pub', 'PUB', [
                ['key' => 'utama', 'label' => 'Pengumpulan Uang atau Barang', 'slots' => [
                    $text('pub.utama.01', 'Judul utama', 'Pengumpulan Uang atau Barang', 'h1'),
                ]],
            ]),
            $page('footer', 'Footer', [
                ['key' => 'utama', 'label' => 'Footer', 'slots' => [
                    $text('footer.utama.01', 'Deskripsi', 'Informasi pemberdayaan sosial untuk masyarakat Kota Banjarbaru.'),
                ]],
            ]),
        ];
    }
}
