<?php

namespace Database\Seeders\Contoh;

use App\Support\Format;
use Illuminate\Support\Collection;

/**
 * Tabel sumber: lks_clients (kolom raw_data dan history). Seluruh identitas klien fiktif.
 */
class DemoClients extends DemoRepository
{
    protected static function seed(): array
    {
        $first = ['Adit', 'Bella', 'Candra', 'Dinda', 'Erlangga', 'Fitri', 'Galih', 'Hana', 'Iqra', 'Jihan', 'Kevin', 'Laila', 'Miftah', 'Nadia', 'Omar', 'Putri', 'Qori', 'Rafi', 'Syifa', 'Taufik', 'Umi', 'Vino', 'Wardah', 'Yoga', 'Zahra'];
        $last = ['Ramadhan', 'Azzahra', 'Kusuma', 'Maharani', 'Saputra', 'Handayani', 'Permana', 'Safitri', 'Nugraha', 'Amalia'];
        $orgs = ['lks-001', 'lks-001', 'lks-002', 'lks-001', 'lks-003', 'lks-001', 'lks-004', 'lks-001', 'lks-005', 'lks-006', 'lks-007', 'lks-008'];
        $categories = ['Anak', 'Anak', 'Anak', 'Lansia', 'Penyandang Disabilitas', 'Dewasa', 'Lainnya'];

        $rows = [];
        for ($n = 1; $n <= 44; $n++) {
            $org = $orgs[$n % count($orgs)];
            $category = $categories[$n % count($categories)];
            $gender = $n % 11 === 0 ? 'Belum diketahui' : ($n % 2 ? 'Laki-laki' : 'Perempuan');
            $exited = $n % 9 === 0;
            $nonResidential = ! $exited && $n % 5 === 0;
            $unknownBirth = $n % 13 === 0;
            $isChild = $category === 'Anak';
            $year = $isChild ? 2008 + ($n % 10) : ($category === 'Lansia' ? 1945 + ($n % 15) : 1985 + ($n % 15));
            $guardianKnown = $n % 7 !== 0;
            $school = $isChild && $n % 4 !== 0;
            $admission = sprintf('20%02d-%02d-%02d', 20 + ($n % 6), ($n % 12) + 1, ($n % 27) + 1);
            $updated = sprintf('2026-%02d-%02dT09:%02d:00+08:00', 6 + ($n % 5), ($n % 27) + 1, $n);
            $name = $first[$n % count($first)].' '.$last[$n % count($last)];

            $rows[] = [
                'id' => sprintf('client-%03d', $n),
                'organizationId' => $org,
                'status' => 'Terdaftar',
                'version' => 1,
                'updated' => $updated,
                'photo' => '',
                'code' => sprintf('WB-%03d', $n),
                'name' => $name,
                'category' => $category,
                'gender' => $gender,
                'religion' => ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Belum diketahui'][$n % 6],
                'nik' => $n % 6 === 0 ? '' : str_pad((string) (1000 + $n), 16, '0', STR_PAD_LEFT),
                'familyCardNumber' => $n % 4 === 0 ? '' : str_pad((string) (2000 + $n), 16, '0', STR_PAD_LEFT),
                'birthPlace' => $unknownBirth ? '' : ['Banjarbaru', 'Martapura', 'Banjarmasin', 'Pelaihari'][$n % 4],
                'birthDate' => $unknownBirth ? '' : sprintf('%d-%02d-%02d', $year, ($n % 12) + 1, ($n % 27) + 1),
                'birthDateUnknown' => $unknownBirth,
                'approximateAge' => $unknownBirth ? 8 + ($n % 60) : '',
                'originAddress' => "Jl. Contoh Asal No. {$n}, Kota Banjarbaru",
                'guardianStatus' => $guardianKnown ? 'Keluarga/wali diketahui' : ($n % 2 ? 'Belum diketahui' : 'Tidak ada kontak yang dapat dihubungi'),
                'guardianName' => $guardianKnown ? 'Wali Contoh '.$n : '',
                'guardianRelationship' => $guardianKnown ? ['Paman', 'Bibi', 'Kakek', 'Nenek', 'Kakak'][$n % 5] : '',
                'guardianContact' => $guardianKnown ? sprintf('0800000040%02d', $n) : '',
                'guardianAddress' => $guardianKnown ? "Jl. Contoh Wali No. {$n}, Kota Banjarbaru" : '',
                'orphanFather' => $n % 3 === 0,
                'orphanMother' => $n % 8 === 0,
                'economicallyVulnerable' => $n % 2 === 0,
                'educationStatus' => $isChild ? ($school ? 'Sedang sekolah' : 'Belum sekolah') : 'Tidak berlaku',
                'schoolName' => $school ? 'Sekolah Contoh '.(($n % 4) + 1) : '',
                'educationLevel' => $school ? 'Kelas '.(($n % 6) + 1) : '',
                'serviceType' => $nonResidential ? 'Layanan nonresidensial' : 'Tinggal di panti',
                'residenceStatus' => $exited ? 'Sudah keluar' : 'Masih menerima layanan',
                'healthCoverageStatus' => ['Ada', 'Ada', 'Tidak ada', 'Belum diketahui'][$n % 4],
                'healthCoverageNumber' => $n % 4 < 2 ? str_pad((string) (3000 + $n), 13, '0', STR_PAD_LEFT) : '',
                'careNeeds' => 'Pendampingan pendidikan dan kegiatan sehari-hari.',
                'carePlan' => 'Evaluasi perkembangan setiap semester bersama pendamping.',
                'identityDocumentStatus' => ['Tersedia', 'Belum lengkap', 'Belum tersedia'][$n % 3],
                'placementDocumentStatus' => ['Tersedia', 'Tersedia', 'Belum lengkap'][$n % 3],
                'documentNotes' => $n % 3 === 0 ? 'Dokumen dilengkapi pada kunjungan berikutnya.' : '',
                'admissionDate' => $admission,
                'exitDate' => $exited ? sprintf('2026-%02d-15', ($n % 9) + 1) : '',
                'exitReason' => $exited ? 'Kembali kepada keluarga setelah asesmen.' : '',
                'referralSource' => ['Keluarga', 'Dinas Sosial', 'Lembaga perujuk'][$n % 3],
                'intakeReason' => 'Membutuhkan dukungan pengasuhan dan layanan sosial dasar.',
                'caseWorker' => 'Pendamping Contoh '.(($n % 3) + 1),
                'leaderName' => DemoOrganizations::find($org)['chair'],
                'notes' => $n % 5 === 0 ? 'Mengikuti kegiatan keterampilan mingguan.' : '',
                'history' => [
                    ['status' => 'Terdaftar', 'at' => $updated, 'actor' => 'Petugas Lembaga', 'note' => 'Biodata disimpan.'],
                ],
            ];
        }

        return $rows;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return static::all()->where('organizationId', $organizationId)->values();
    }

    /**
     * Ringkasan dari summary() di lks/ui.js.
     *
     * @param  Collection<int, array<string, mixed>>  $clients
     * @return array{active: Collection, residents: int, nonresidential: int, exited: int, clients: int}
     */
    public static function summary(Collection $clients): array
    {
        $active = $clients->filter(fn (array $client) => Format::living($client))->values();

        return [
            'active' => $active,
            'residents' => $active->count(),
            'nonresidential' => $clients->filter(fn (array $client) => $client['serviceType'] === 'Layanan nonresidensial' && $client['residenceStatus'] === 'Masih menerima layanan')->count(),
            'exited' => $clients->where('residenceStatus', 'Sudah keluar')->count(),
            'clients' => $clients->count(),
        ];
    }

    /** @return array<string, array<int, string>> Pilihan filter rekap, dari shared/data-schemas.js. */
    public static function formOptions(): array
    {
        return [
            'category' => ['Anak', 'Lansia', 'Penyandang Disabilitas', 'Dewasa', 'Lainnya'],
            'gender' => ['Laki-laki', 'Perempuan', 'Belum diketahui'],
        ];
    }
}
