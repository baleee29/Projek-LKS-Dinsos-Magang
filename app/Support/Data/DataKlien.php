<?php

namespace App\Support\Data;

use App\Models\KlienLks;
use App\Support\Format;
use Illuminate\Support\Collection;

/** Klien LKS dalam bentuk yang dibaca view dan form. */
class DataKlien
{
    /** Nama isian di form dan view => kolom. */
    public const KOLOM = [
        'code' => 'kode', 'name' => 'nama', 'category' => 'kategori', 'gender' => 'jenis_kelamin', 'birthPlace' => 'tempat_lahir',
        'birthDate' => 'tanggal_lahir', 'birthDateUnknown' => 'tanggal_lahir_tidak_diketahui', 'approximateAge' => 'perkiraan_usia',
        'nik' => 'nik', 'familyCardNumber' => 'nomor_kk', 'religion' => 'agama', 'originAddress' => 'alamat_asal',
        'admissionDate' => 'tanggal_masuk', 'serviceType' => 'bentuk_layanan', 'residenceStatus' => 'status_penghuni',
        'exitDate' => 'tanggal_keluar', 'exitReason' => 'alasan_keluar', 'referralSource' => 'asal_rujukan', 'intakeReason' => 'alasan_penerimaan',
        'guardianStatus' => 'status_wali', 'guardianName' => 'nama_wali', 'guardianRelationship' => 'hubungan_wali', 'guardianContact' => 'kontak_wali',
        'guardianAddress' => 'alamat_wali', 'educationStatus' => 'status_pendidikan', 'schoolName' => 'nama_sekolah', 'educationLevel' => 'jenjang_pendidikan',
        'careNeeds' => 'kebutuhan_layanan', 'carePlan' => 'rencana_layanan', 'caseWorker' => 'petugas_pendamping',
        'identityDocumentStatus' => 'status_dokumen_identitas', 'placementDocumentStatus' => 'status_dokumen_penempatan', 'documentNotes' => 'catatan_dokumen',
        'notes' => 'catatan', 'orphanFather' => 'yatim', 'orphanMother' => 'piatu', 'economicallyVulnerable' => 'rentan_ekonomi',
        'healthCoverageStatus' => 'status_jaminan_kesehatan', 'healthCoverageNumber' => 'nomor_jaminan_kesehatan', 'leaderName' => 'nama_pimpinan',
    ];

    public const BOOLEAN = ['birthDateUnknown', 'orphanFather', 'orphanMother', 'economicallyVulnerable'];

    /** @return array<string, mixed> */
    public static function tampil(KlienLks $klien): array
    {
        $baris = [
            'id' => $klien->id,
            'organizationId' => $klien->lembaga_id,
            'status' => $klien->status,
            'version' => $klien->versi,
            'updated' => $klien->diperbarui_pada?->toIso8601String() ?? '',
            'photo' => $klien->foto_berkas_id ? '/berkas/'.$klien->foto_berkas_id : '',
            'history' => $klien->riwayat ?? [],
        ];
        foreach (self::KOLOM as $isian => $kolom) {
            $nilai = $klien->{$kolom};
            $baris[$isian] = match (true) {
                in_array($isian, self::BOOLEAN, true) => (bool) $nilai,
                $nilai instanceof \DateTimeInterface => $nilai->format('Y-m-d'),
                default => $nilai ?? '',
            };
        }

        return $baris;
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function raw(): Collection
    {
        return Memo::ingat('klien', fn () => KlienLks::orderBy('nama')->get()->map(fn (KlienLks $klien) => self::tampil($klien))->values());
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function all(): Collection
    {
        return self::raw();
    }

    /** @return array<string, mixed>|null */
    public static function find(string $id): ?array
    {
        return self::raw()->firstWhere('id', $id);
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function forOrganization(string $organizationId): Collection
    {
        return self::raw()->where('organizationId', $organizationId)->values();
    }

    /**
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

    /** @return array<string, array<int, string>> */
    public static function formOptions(): array
    {
        return [
            'category' => ['Anak', 'Lansia', 'Penyandang Disabilitas', 'Dewasa', 'Lainnya'],
            'gender' => ['Laki-laki', 'Perempuan', 'Belum diketahui'],
        ];
    }
}
