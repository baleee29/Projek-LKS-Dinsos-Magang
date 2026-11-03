<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: notifications (notifikasi database Laravel per pengguna).
 */
class DemoNotifications extends DemoRepository
{
    protected static function seed(): array
    {
        $own = DemoOrganizations::own();
        $rows = [];
        $make = function (string $audience, string $pillar, string $title, string $message, string $organization, string $period, string $url, bool $read, array $fields = []) use (&$rows) {
            $n = count($rows) + 1;
            $rows[] = [
                'id' => sprintf('notif-%03d', $n),
                'audience' => $audience,
                'pillar' => $pillar,
                'title' => $title,
                'message' => $message,
                'organizationTitle' => $organization,
                'periodLabel' => $period,
                'at' => sprintf('2026-%02d-%02dT%02d:15:00+08:00', 10 - ($n % 3), 28 - ($n % 20), 8 + ($n % 9)),
                'read' => $read,
                'url' => $url,
                'note' => '',
                'exists' => $fields !== [],
                'fields' => $fields,
            ];
        };

        $admin = [
            ['Laporan semester baru masuk', 'LKSA Pelita Harapan mengirim laporan semester.', 'LKSA Pelita Harapan', 'Semester 1 2026', '/admin/report/RPT-LKS002-2026-S1', false, ['title' => 'Laporan Kegiatan dan Layanan Semester 1 2026', 'year' => 2026, 'quarter' => 'Semester 1']],
            ['Laporan dikirim ulang', 'Yayasan Griya Lansia Sejahtera memperbarui laporan semester.', 'Yayasan Griya Lansia Sejahtera', 'Semester 1 2026', '/admin/report/RPT-LKS003-2026-S1', false, ['title' => 'Laporan Kegiatan dan Layanan Semester 1 2026', 'year' => 2026, 'quarter' => 'Semester 1']],
            ['Pendaftaran LKS baru', 'Ada pendaftaran LKS baru yang menunggu keputusan Admin.', 'LKS Harapan Mulia', 'Pendaftaran LKS', '/admin/registration/reg-lks-001', false, ['organization_name' => 'LKS Harapan Mulia', 'leader_name' => 'Ratna Sari', 'status' => 'Menunggu Verifikasi', 'district' => 'Cempaka', 'village' => 'Bangkal']],
            ['Pendaftaran LKS baru', 'Ada pendaftaran LKS baru yang menunggu keputusan Admin.', 'Panti Sosial Kasih Ibu', 'Pendaftaran LKS', '/admin/registration/reg-lks-002', false, ['organization_name' => 'Panti Sosial Kasih Ibu', 'leader_name' => 'Teguh Wijaya', 'status' => 'Menunggu Verifikasi']],
            ['Surat masuk dari LKS', 'Panti Asuhan Cahaya Kasih mengirim surat ke Dinas Sosial.', 'Panti Asuhan Cahaya Kasih', 'Persuratan', '/admin/letters/lks', false, ['subject' => 'Permohonan Rekomendasi Bantuan Permakanan']],
            ['Surat masuk dari LKS', 'LKSA Tunas Bangsa mengirim surat ke Dinas Sosial.', 'LKSA Tunas Bangsa', 'Persuratan', '/admin/letters/lks', true, ['subject' => 'Laporan Perubahan Susunan Pengurus']],
            ['Laporan semester baru masuk', 'LKSA Tunas Bangsa mengirim laporan semester.', 'LKSA Tunas Bangsa', 'Semester 1 2026', '/admin/report/RPT-LKS006-2026-S1', true, ['title' => 'Laporan Kegiatan dan Layanan Semester 1 2026', 'year' => 2026, 'quarter' => 'Semester 1']],
            ['Data lembaga diperbarui', 'LKS Rumah Singgah Mentari memperbarui profil lembaga.', 'LKS Rumah Singgah Mentari', 'Pembaruan data', '/admin/organization/lks-008', true, []],
            ['Klien baru tercatat', 'Yayasan Peduli Sesama Banua menambahkan biodata klien.', 'Yayasan Peduli Sesama Banua', 'Pembaruan data', '/admin/occupancy?org=lks-007&residence=', true, []],
            ['Laporan semester baru masuk', 'Yayasan Peduli Sesama Banua mengirim laporan semester.', 'Yayasan Peduli Sesama Banua', 'Semester 1 2026', '/admin/report/RPT-LKS007-2026-S1', true, ['title' => 'Laporan Kegiatan dan Layanan Semester 1 2026', 'year' => 2026, 'quarter' => 'Semester 1']],
        ];
        foreach ($admin as $row) {
            $make('admin', 'lks', ...$row);
        }

        $lks = [
            ['Surat baru dari Dinas Sosial', 'Dinas Sosial mengirim surat untuk lembaga Anda.', 'Semester 2 2026', '/lks/letters?tab=incoming', false, ['subject' => 'Undangan Sosialisasi Standar Layanan LKS', 'letter_number' => '460/010/DINSOS-DEMO/2026']],
            ['Laporan semester terkirim', 'Laporan Semester 1 2026 sudah masuk ke Admin.', 'Semester 1 2026', '/lks/report/RPT-LKS001-2026-S1', false, ['title' => 'Laporan Kegiatan dan Layanan Semester 1 2026', 'year' => 2026, 'quarter' => 'Semester 1']],
            ['Pengingat draf laporan', 'Draf Laporan Semester 2 2026 belum dikirim.', 'Semester 2 2026', '/lks/edit/RPT-LKS001-2026-S2', false, ['title' => 'Laporan Semester 2 2026 (berjalan)', 'year' => 2026, 'quarter' => 'Semester 2']],
            ['Personel baru tercatat', 'Personel baru tercatat dalam daftar Pengurus & SDM.', 'Pembaruan data', '/lks/members', true, []],
            ['Biodata klien diperbarui', 'Biodata klien diperbarui oleh petugas lembaga.', 'Pembaruan data', '/lks/occupancy', true, []],
            ['Laporan semester terkirim', 'Laporan Semester 2 2025 sudah masuk ke Admin.', 'Semester 2 2025', '/lks/report/RPT-LKS001-2025-S2', true, ['title' => 'Laporan Kegiatan dan Layanan Semester 2 2025', 'year' => 2025, 'quarter' => 'Semester 2']],
            ['Surat baru dari Dinas Sosial', 'Dinas Sosial mengirim pemberitahuan monitoring.', 'Semester 1 2026', '/lks/letters?tab=incoming', true, ['subject' => 'Pemberitahuan Jadwal Monitoring Semester']],
            ['Laporan semester terkirim', 'Laporan Semester 1 2025 sudah masuk ke Admin.', 'Semester 1 2025', '/lks/report/RPT-LKS001-2025-S1', true, []],
        ];
        foreach ($lks as [$title, $message, $period, $url, $read, $fields]) {
            $make('lks', 'lks', $title, $message, $own['title'], $period, $url, $read, $fields);
        }

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forRole(string $role): Collection
    {
        return static::all()->where('audience', $role)->sortByDesc('at')->values();
    }
}
