<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: partner_registrations dan partner_registration_files (pilar LKS).
 * Status tersimpan: Menunggu Verifikasi, Disetujui (label Diterima), Ditolak.
 * Tidak ada nomor bukti atau kode akses; kolom itu sudah dihapus di sumber.
 */
class DemoRegistrations extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        $make = function (string $id, string $name, string $leader, string $district, string $village, string $status, string $note, int $day) use (&$rows) {
            $created = sprintf('2026-10-%02dT09:00:00+08:00', $day);
            $reviewed = $status === 'Menunggu Verifikasi' ? null : sprintf('2026-10-%02dT14:00:00+08:00', min($day + 2, 30));
            $n = count($rows) + 1;
            $files = [
                    ['id' => $id.'-f1', 'key' => 'akta', 'label' => 'Akta Pendirian Lembaga', 'name' => 'akta-demo.pdf', 'type' => 'application/pdf', 'size' => 412000],
                    ['id' => $id.'-f2', 'key' => 'struktur', 'label' => 'Struktur Organisasi', 'name' => 'struktur-demo.pdf', 'type' => 'application/pdf', 'size' => 183000],
                    ['id' => $id.'-f3', 'key' => 'data-umum', 'label' => 'Data Umum LKS', 'name' => 'data-umum-demo.pdf', 'type' => 'application/pdf', 'size' => 267000],
                ];

            $history = [['status' => 'Menunggu Verifikasi', 'at' => $created, 'actor' => 'Pendaftar', 'note' => 'Pendaftaran dikirim.']];
            if ($reviewed) {
                $history[] = ['status' => $status, 'at' => $reviewed, 'actor' => 'Admin', 'note' => $note];
            }

            $rows[] = [
                'id' => $id,
                'pillar' => 'lks',
                'organization_name' => $name,
                'leader_name' => $leader,
                'whatsapp' => sprintf('0800000050%02d', $n),
                'establishment_date' => sprintf('20%02d-%02d-01', 10 + $n, ($n % 12) + 1),
                'address' => "Jl. Contoh Pendaftaran No. {$n}, Kel. {$village}, Kec. {$district}, Kota Banjarbaru",
                'district' => $district,
                'village' => $village,
                'sk_number' => sprintf('SK-%03d/DEMO/2026', $n),
                'focus_area' => 'Pengasuhan anak dan layanan sosial dasar',
                'status' => $status,
                'notes' => $note,
                'files' => $files,
                'created_at' => $created,
                'updated_at' => $reviewed ?? $created,
                'reviewed_at' => $reviewed,
                'history' => $history,
            ];
        };

        $make('reg-lks-001', 'LKS Harapan Mulia', 'Ratna Sari', 'Cempaka', 'Bangkal', 'Menunggu Verifikasi', '', 26);
        $make('reg-lks-002', 'Panti Sosial Kasih Ibu', 'Teguh Wijaya', 'Banjarbaru Utara', 'Sungai Ulin', 'Menunggu Verifikasi', '', 24);
        $make('reg-lks-003', 'LKSA Insan Cendekia', 'Wulan Asih', 'Liang Anggang', 'Landasan Ulin Selatan', 'Menunggu Verifikasi', '', 21);
        $make('reg-lks-011', 'Panti Asuhan Cahaya Kasih', 'Maryam Lestari', 'Banjarbaru Selatan', 'Loktabat Selatan', 'Disetujui', 'Dokumen lengkap.', 10);
        $make('reg-lks-012', 'LKSA Pelita Harapan', 'Yusuf Pratama', 'Banjarbaru Utara', 'Mentaos', 'Disetujui', 'Dokumen lengkap.', 8);
        $make('reg-lks-013', 'Yayasan Griya Lansia Sejahtera', 'Rohani Wulandari', 'Cempaka', 'Bangkal', 'Disetujui', 'Dokumen lengkap.', 6);
        $make('reg-lks-004', 'Yayasan Bakti Nurani', 'Hasan Basri', 'Landasan Ulin', 'Guntung Manggis', 'Ditolak', 'Dokumen legalitas belum sesuai. Silakan kirim pendaftaran baru.', 5);
        $make('reg-lks-005', 'LKS Cahaya Bangsa', 'Aldi Firmansyah', 'Cempaka', 'Palam', 'Menunggu Verifikasi', '', 25);
        $make('reg-lks-006', 'Panti Lansia Komet Asri', 'Tiara Dewi', 'Banjarbaru Utara', 'Komet', 'Disetujui', 'Dokumen lengkap.', 11);
        $make('reg-lks-007', 'LKS Liang Berdaya', 'Yoga Pamungkas', 'Liang Anggang', 'Landasan Ulin Utara', 'Ditolak', 'Akta pendirian belum ditandatangani. Silakan kirim pendaftaran baru.', 7);

        return $rows;
    }

    public static function pending(): int
    {
        return static::all()->where('status', 'Menunggu Verifikasi')->count();
    }

    /** Sumber menyimpan Disetujui dan menampilkannya sebagai Diterima. */
    public static function statusLabel(string $status): string
    {
        return $status === 'Disetujui' ? 'Diterima' : $status;
    }
}
