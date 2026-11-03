<?php

namespace App\Support\Demo;

use Illuminate\Support\Collection;

/**
 * Tabel sumber: partner_registrations dan partner_registration_files.
 * Status tersimpan: Menunggu Verifikasi, Disetujui (label Diterima), Ditolak.
 * Tidak ada nomor bukti atau kode akses; kolom itu sudah dihapus di sumber.
 */
class DemoRegistrations extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        $make = function (string $id, string $pillar, string $name, string $leader, string $district, string $village, string $status, string $note, int $day) use (&$rows) {
            $created = sprintf('2026-10-%02dT09:00:00+08:00', $day);
            $reviewed = $status === 'Menunggu Verifikasi' ? null : sprintf('2026-10-%02dT14:00:00+08:00', min($day + 2, 30));
            $n = count($rows) + 1;
            $files = $pillar === 'lks'
                ? [
                    ['id' => $id.'-f1', 'key' => 'akta', 'label' => 'Akta Pendirian Lembaga', 'name' => 'akta-demo.pdf', 'type' => 'application/pdf', 'size' => 412000],
                    ['id' => $id.'-f2', 'key' => 'struktur', 'label' => 'Struktur Organisasi', 'name' => 'struktur-demo.pdf', 'type' => 'application/pdf', 'size' => 183000],
                    ['id' => $id.'-f3', 'key' => 'data-umum', 'label' => 'Data Umum LKS', 'name' => 'data-umum-demo.pdf', 'type' => 'application/pdf', 'size' => 267000],
                ]
                : [
                    ['id' => $id.'-f1', 'key' => 'sk', 'label' => 'SK Pengukuhan Karang Taruna', 'name' => 'sk-demo.pdf', 'type' => 'application/pdf', 'size' => 215000],
                ];

            $history = [['status' => 'Menunggu Verifikasi', 'at' => $created, 'actor' => 'Pendaftar', 'note' => 'Pendaftaran dikirim.']];
            if ($reviewed) {
                $history[] = ['status' => $status, 'at' => $reviewed, 'actor' => 'Admin', 'note' => $note];
            }

            $rows[] = [
                'id' => $id,
                'pillar' => $pillar,
                'organization_name' => $name,
                'leader_name' => $leader,
                'whatsapp' => sprintf('0800000050%02d', $n),
                'establishment_date' => sprintf('20%02d-%02d-01', 10 + $n, ($n % 12) + 1),
                'address' => "Jl. Contoh Pendaftaran No. {$n}, Kel. {$village}, Kec. {$district}, Kota Banjarbaru",
                'district' => $district,
                'village' => $village,
                'sk_number' => sprintf('SK-%03d/DEMO/2026', $n),
                'focus_area' => $pillar === 'lks' ? 'Pengasuhan anak dan layanan sosial dasar' : 'Kepemudaan dan bakti sosial',
                'status' => $status,
                'notes' => $note,
                'files' => $files,
                'created_at' => $created,
                'updated_at' => $reviewed ?? $created,
                'reviewed_at' => $reviewed,
                'history' => $history,
            ];
        };

        $make('reg-lks-001', 'lks', 'LKS Harapan Mulia', 'Ratna Sari', 'Cempaka', 'Bangkal', 'Menunggu Verifikasi', '', 26);
        $make('reg-lks-002', 'lks', 'Panti Sosial Kasih Ibu', 'Teguh Wijaya', 'Banjarbaru Utara', 'Sungai Ulin', 'Menunggu Verifikasi', '', 24);
        $make('reg-lks-003', 'lks', 'LKSA Insan Cendekia', 'Wulan Asih', 'Liang Anggang', 'Landasan Ulin Selatan', 'Menunggu Verifikasi', '', 21);
        $make('reg-lks-011', 'lks', 'Panti Asuhan Cahaya Kasih', 'Maryam Lestari', 'Banjarbaru Selatan', 'Loktabat Selatan', 'Disetujui', 'Dokumen lengkap.', 10);
        $make('reg-lks-012', 'lks', 'LKSA Pelita Harapan', 'Yusuf Pratama', 'Banjarbaru Utara', 'Mentaos', 'Disetujui', 'Dokumen lengkap.', 8);
        $make('reg-lks-013', 'lks', 'Yayasan Griya Lansia Sejahtera', 'Rohani Wulandari', 'Cempaka', 'Bangkal', 'Disetujui', 'Dokumen lengkap.', 6);
        $make('reg-lks-004', 'lks', 'Yayasan Bakti Nurani', 'Hasan Basri', 'Landasan Ulin', 'Guntung Manggis', 'Ditolak', 'Dokumen legalitas belum sesuai. Silakan kirim pendaftaran baru.', 5);
        $make('reg-kt-001', 'karang-taruna', 'Karang Taruna Palam Muda', 'Aldi Firmansyah', 'Cempaka', 'Palam', 'Menunggu Verifikasi', '', 25);
        $make('reg-kt-002', 'karang-taruna', 'Karang Taruna Komet Cerdas', 'Tiara Dewi', 'Banjarbaru Utara', 'Komet', 'Menunggu Verifikasi', '', 22);
        $make('reg-kt-011', 'karang-taruna', 'Karang Taruna Bina Remaja', 'Rizky Maulana', 'Banjarbaru Selatan', 'Kemuning', 'Disetujui', 'SK sesuai.', 12);
        $make('reg-kt-012', 'karang-taruna', 'Karang Taruna Muda Berkarya', 'Salsabila Putri', 'Banjarbaru Utara', 'Sungai Ulin', 'Disetujui', 'SK sesuai.', 11);
        $make('reg-kt-003', 'karang-taruna', 'Karang Taruna Liang Berdaya', 'Yoga Pamungkas', 'Liang Anggang', 'Landasan Ulin Utara', 'Ditolak', 'SK belum ditandatangani. Silakan kirim pendaftaran baru.', 7);

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function byPillar(string $pillar): Collection
    {
        return static::all()->where('pillar', $pillar)->sortByDesc('created_at')->values();
    }

    public static function pending(string $pillar): int
    {
        return static::all()->where('pillar', $pillar)->where('status', 'Menunggu Verifikasi')->count();
    }

    /** Sumber menyimpan Disetujui dan menampilkannya sebagai Diterima. */
    public static function statusLabel(string $status): string
    {
        return $status === 'Disetujui' ? 'Diterima' : $status;
    }
}
