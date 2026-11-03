<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: lks_letters. type = incoming: Dinas ke LKS; outgoing: LKS ke Dinas.
 * read_at milik penerima, jadi Admin membaca outgoing dan LKS membaca incoming.
 */
class DemoLetters extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        $subjects = [
            'Undangan Sosialisasi Standar Layanan LKS',
            'Pemberitahuan Jadwal Monitoring Semester',
            'Permohonan Rekomendasi Bantuan Permakanan',
            'Laporan Perubahan Susunan Pengurus',
            'Surat Edaran Pembaruan Data Klien',
            'Permohonan Audiensi Program Pendampingan',
            'Pemberitahuan Pelatihan Pekerja Sosial',
            'Pengantar Dokumen Legalitas Lembaga',
        ];
        for ($n = 1; $n <= 18; $n++) {
            $incoming = $n % 2 === 0;
            $org = $n % 3 === 0 ? sprintf('lks-%03d', ($n % 8) + 1) : DemoOrganizations::OWN_LKS;
            $month = ($n % 10) + 1;
            $hasFile = $n % 4 !== 0;
            $rows[] = [
                'id' => sprintf('letter-%03d', $n),
                'organization_id' => $org,
                'type' => $incoming ? 'incoming' : 'outgoing',
                'letter_number' => $n % 7 === 0 ? '' : sprintf($incoming ? '460/%03d/DINSOS-DEMO/2026' : '%03d/LKS-DEMO/%d/2026', $n, $month),
                'letter_date' => sprintf('2026-%02d-%02d', $month, ($n % 27) + 1),
                'subject' => $subjects[$n % count($subjects)],
                'description' => $n % 3 === 0 ? '' : 'Ringkasan isi surat untuk keperluan arsip lembaga.',
                'sender' => $incoming ? 'Dinas Sosial Kota Banjarbaru' : DemoOrganizations::find($org)['title'],
                'destination' => $incoming ? DemoOrganizations::find($org)['title'] : 'Dinas Sosial Kota Banjarbaru',
                'is_read' => $n % 5 !== 0 && $n % 6 !== 0,
                'has_file' => $hasFile,
                'file_name' => $hasFile ? "surat-demo-{$n}.pdf" : '',
                'file_size' => $hasFile ? 120000 + ($n * 15000) : 0,
            ];
        }

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forLks(string $organizationId): Collection
    {
        return static::all()->where('organization_id', $organizationId)->values();
    }

    public static function unreadForLks(): int
    {
        return static::forLks(DemoOrganizations::OWN_LKS)->where('type', 'incoming')->where('is_read', false)->count();
    }

    public static function unreadForAdmin(): int
    {
        return static::all()->where('type', 'outgoing')->where('is_read', false)->count();
    }

    public static function fileSize(int $bytes): string
    {
        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : number_format($bytes / 1024, 0, ',', '.').' KB';
    }
}
