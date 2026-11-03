<?php

namespace App\Support\Demo;

/**
 * Definisi isian klien, disalin dari shared/data-schemas.js (residentSections, residentSupplementalFields)
 * dan lks/client-form-sections.js. Aturan wajib bersyarat ada di resources/js/pages/client-form.js.
 *
 * TODO(Tahap 2): pindahkan ke FormRequest dengan aturan dari shared/data-validation.js.
 */
class DemoClientSchema
{
    /** @return array<string, array<string, mixed>> */
    public static function fields(): array
    {
        $doc = ['Tersedia', 'Belum lengkap', 'Belum tersedia'];

        return [
            'code' => ['label' => 'Kode klien', 'required' => true, 'maxLength' => 40, 'hint' => 'Kode unik dalam lembaga, misalnya WB-001.'],
            'name' => ['label' => 'Nama lengkap', 'required' => true, 'maxLength' => 150],
            'category' => ['label' => 'Kategori penerima layanan', 'type' => 'select', 'required' => true, 'options' => ['Anak', 'Lansia', 'Penyandang Disabilitas', 'Dewasa', 'Lainnya']],
            'gender' => ['label' => 'Jenis kelamin', 'type' => 'select', 'required' => true, 'options' => ['Laki-laki', 'Perempuan', 'Belum diketahui']],
            'birthPlace' => ['label' => 'Tempat lahir', 'maxLength' => 100],
            'birthDate' => ['label' => 'Tanggal lahir', 'type' => 'date', 'hint' => 'Isi sesuai dokumen yang tersedia. Jika belum diketahui, pilih perkiraan usia di bawah.'],
            'birthDateUnknown' => ['label' => 'Tanggal lahir belum diketahui', 'type' => 'checkbox'],
            'approximateAge' => ['label' => 'Perkiraan usia saat ini (tahun)', 'type' => 'number', 'min' => 0, 'max' => 125, 'conditional' => 'birthDateUnknown', 'hint' => 'Wajib jika tanggal lahir belum diketahui. Angka 0 untuk bayi berusia kurang dari satu tahun.'],
            'nik' => ['label' => 'NIK (jika ada)', 'maxLength' => 16, 'hint' => '16 digit jika sudah tersedia. Jangan membuat NIK pengganti.'],
            'originAddress' => ['label' => 'Alamat asal', 'type' => 'textarea', 'maxLength' => 500, 'hint' => 'Isi wilayah atau alamat asal yang diketahui.'],
            'admissionDate' => ['label' => 'Tanggal mulai menerima layanan', 'type' => 'date', 'required' => true],
            'serviceType' => ['label' => 'Bentuk layanan', 'type' => 'select', 'required' => true, 'options' => ['Tinggal di panti', 'Layanan nonresidensial']],
            'residenceStatus' => ['label' => 'Status penerimaan layanan', 'type' => 'select', 'required' => true, 'options' => ['Masih menerima layanan', 'Sudah keluar']],
            'exitDate' => ['label' => 'Tanggal keluar / selesai layanan', 'type' => 'date', 'conditional' => 'residenceStatus=Sudah keluar'],
            'exitReason' => ['label' => 'Alasan keluar / selesai layanan', 'type' => 'textarea', 'maxLength' => 1000, 'conditional' => 'residenceStatus=Sudah keluar'],
            'referralSource' => ['label' => 'Asal rujukan / pihak yang mengantar', 'required' => true, 'maxLength' => 200, 'hint' => 'Misalnya keluarga, Dinas Sosial, atau lembaga perujuk.'],
            'intakeReason' => ['label' => 'Alasan penerimaan layanan', 'type' => 'textarea', 'required' => true, 'maxLength' => 1500, 'hint' => 'Ringkas kebutuhan sosial. Hindari rincian kejadian kekerasan atau diagnosis kesehatan.'],
            'guardianStatus' => ['label' => 'Ketersediaan keluarga / wali', 'type' => 'select', 'required' => true, 'options' => ['Keluarga/wali diketahui', 'Belum diketahui', 'Tidak ada kontak yang dapat dihubungi']],
            'guardianName' => ['label' => 'Nama keluarga / wali', 'maxLength' => 150, 'conditional' => 'guardianStatus=Keluarga/wali diketahui'],
            'guardianRelationship' => ['label' => 'Hubungan dengan klien', 'maxLength' => 100, 'conditional' => 'guardianStatus=Keluarga/wali diketahui'],
            'guardianContact' => ['label' => 'Nomor kontak keluarga / wali', 'maxLength' => 40, 'conditional' => 'guardianStatus=Keluarga/wali diketahui', 'hint' => 'Isi nomor telepon jika tersedia.'],
            'guardianAddress' => ['label' => 'Alamat keluarga / wali', 'type' => 'textarea', 'maxLength' => 500, 'conditional' => 'guardianStatus=Keluarga/wali diketahui'],
            'educationStatus' => ['label' => 'Status pendidikan', 'type' => 'select', 'options' => ['Belum sekolah', 'Sedang sekolah', 'Tidak sekolah', 'Sudah lulus', 'Tidak diketahui', 'Tidak berlaku'], 'hint' => 'Wajib untuk kategori Anak atau klien berusia kurang dari 18 tahun.'],
            'schoolName' => ['label' => 'Nama sekolah', 'maxLength' => 150, 'conditional' => 'educationStatus=Sedang sekolah'],
            'educationLevel' => ['label' => 'Jenjang / kelas', 'maxLength' => 100, 'conditional' => 'educationStatus=Sedang sekolah'],
            'careNeeds' => ['label' => 'Kebutuhan layanan / pendampingan', 'type' => 'textarea', 'required' => true, 'maxLength' => 1500, 'hint' => 'Misalnya pendampingan pendidikan, akses dokumen, atau dukungan kegiatan sehari-hari.'],
            'carePlan' => ['label' => 'Rencana layanan / pengasuhan', 'type' => 'textarea', 'required' => true, 'maxLength' => 2000],
            'caseWorker' => ['label' => 'Petugas / pendamping penanggung jawab', 'required' => true, 'maxLength' => 150],
            'identityDocumentStatus' => ['label' => 'Dokumen identitas', 'type' => 'select', 'required' => true, 'options' => $doc, 'hint' => 'Misalnya akta kelahiran, kartu keluarga, atau identitas lain yang tersedia.'],
            'placementDocumentStatus' => ['label' => 'Dokumen penerimaan / penempatan', 'type' => 'select', 'required' => true, 'options' => $doc, 'hint' => 'Misalnya surat rujukan atau persetujuan keluarga / wali yang relevan.'],
            'documentNotes' => ['label' => 'Keterangan dokumen / rencana melengkapi', 'type' => 'textarea', 'maxLength' => 1500],
            'notes' => ['label' => 'Catatan layanan tambahan', 'type' => 'textarea', 'maxLength' => 1500, 'hint' => 'Gunakan keterangan seperlunya; jangan mencantumkan diagnosis atau rincian sensitif.'],
            'religion' => ['label' => 'Agama / kepercayaan', 'type' => 'select', 'options' => ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Kepercayaan lainnya', 'Belum diketahui'], 'hint' => 'Opsional; isi jika sudah diketahui.'],
            'familyCardNumber' => ['label' => 'Nomor Kartu Keluarga (KK)', 'maxLength' => 16, 'hint' => '16 digit jika tersedia. Kosongkan jika belum diketahui.'],
            'orphanFather' => ['label' => 'Yatim', 'type' => 'checkbox'],
            'orphanMother' => ['label' => 'Piatu', 'type' => 'checkbox'],
            'economicallyVulnerable' => ['label' => 'Dhuafa / rentan ekonomi', 'type' => 'checkbox'],
            'healthCoverageStatus' => ['label' => 'Status kepesertaan BPJS / KIS', 'type' => 'select', 'options' => ['Ada', 'Tidak ada', 'Belum diketahui'], 'hint' => 'Catat ketersediaan jaminan kesehatan yang diketahui.'],
            'healthCoverageNumber' => ['label' => 'Nomor kartu BPJS / KIS', 'maxLength' => 13, 'hint' => '13 digit jika tersedia; dapat dilengkapi nanti.', 'conditional' => 'healthCoverageStatus=Ada'],
            'leaderName' => ['label' => 'Pimpinan / kepala lembaga', 'maxLength' => 150, 'hint' => 'Nama pimpinan lembaga, jika diperlukan untuk pencatatan.'],
        ];
    }

    /** Bagian A–D pada form. @return array<int, array<string, mixed>> */
    public static function formSections(): array
    {
        return [
            ['key' => 'a', 'letter' => 'A', 'title' => 'Identitas Klien / Anak Asuh', 'short' => 'Identitas klien', 'description' => 'Identitas dan kelompok penerima layanan lembaga.', 'tag' => 'Identitas', 'fields' => ['name', 'code', 'category', 'gender', 'religion', 'nik', 'familyCardNumber', 'birthPlace', 'birthDate', 'birthDateUnknown', 'approximateAge']],
            ['key' => 'b', 'letter' => 'B', 'title' => 'Data Orang Tua / Wali dan Alamat', 'short' => 'Keluarga, wali & alamat', 'description' => 'Keluarga atau wali yang dapat dihubungi untuk koordinasi.', 'tag' => 'Kontak keluarga', 'fields' => ['guardianStatus', 'guardianName', 'guardianRelationship', 'guardianContact', 'originAddress', 'guardianAddress']],
            ['key' => 'c', 'letter' => 'C', 'title' => 'Status Sosial, Bantuan & Pendidikan', 'short' => 'Sosial, layanan & pendidikan', 'description' => 'Kebutuhan layanan, pendidikan, jaminan kesehatan, dan dokumen.', 'tag' => 'Kebutuhan layanan', 'fields' => ['orphanFather', 'orphanMother', 'economicallyVulnerable', 'educationStatus', 'schoolName', 'educationLevel', 'serviceType', 'residenceStatus', 'healthCoverageStatus', 'healthCoverageNumber', 'careNeeds', 'carePlan', 'identityDocumentStatus', 'placementDocumentStatus', 'documentNotes']],
            ['key' => 'd', 'letter' => 'D', 'title' => 'Informasi Masuk & Penanggung Jawab', 'short' => 'Penerimaan & pendampingan', 'description' => 'Riwayat penerimaan, sumber rujukan, dan rencana tindak lanjut.', 'tag' => 'Administrasi lembaga', 'fields' => ['admissionDate', 'exitDate', 'exitReason', 'referralSource', 'intakeReason', 'caseWorker', 'leaderName', 'notes']],
        ];
    }

    /**
     * Bagian detail biodata, dari residentSections dan extraFields di lks/client-detail.js.
     *
     * @return array<int, array{key: string, title: string, tag: string, icon: string, fields: array<int, string>}>
     */
    public static function detailSections(): array
    {
        return [
            ['key' => 'identity', 'title' => 'Identitas klien', 'tag' => 'Data pokok', 'icon' => 'users', 'fields' => ['code', 'name', 'category', 'gender', 'birthPlace', 'birthDate', 'birthDateUnknown', 'approximateAge', 'nik', 'originAddress', 'religion', 'familyCardNumber']],
            ['key' => 'intake', 'title' => 'Penerimaan dan layanan', 'tag' => 'Layanan', 'icon' => 'home', 'fields' => ['admissionDate', 'serviceType', 'residenceStatus', 'exitDate', 'exitReason', 'referralSource', 'intakeReason']],
            ['key' => 'guardian', 'title' => 'Keluarga atau wali', 'tag' => 'Relasi', 'icon' => 'heart', 'fields' => ['guardianStatus', 'guardianName', 'guardianRelationship', 'guardianContact', 'guardianAddress']],
            ['key' => 'education', 'title' => 'Pendidikan', 'tag' => 'Formal', 'icon' => 'file', 'fields' => ['educationStatus', 'schoolName', 'educationLevel']],
            ['key' => 'care', 'title' => 'Kebutuhan dan rencana pendampingan', 'tag' => 'Pendampingan', 'icon' => 'shield', 'fields' => ['careNeeds', 'carePlan', 'caseWorker', 'orphanFather', 'orphanMother', 'economicallyVulnerable', 'healthCoverageStatus', 'healthCoverageNumber']],
            ['key' => 'documents', 'title' => 'Administrasi dan catatan', 'tag' => 'Berkas', 'icon' => 'file', 'fields' => ['identityDocumentStatus', 'placementDocumentStatus', 'documentNotes', 'notes', 'leaderName']],
        ];
    }

    /** @param  array<string, mixed>  $data */
    public static function applicable(string $key, array $data): bool
    {
        return match (true) {
            $key === 'birthDateUnknown' => false,
            $key === 'approximateAge' => ! empty($data['birthDateUnknown']),
            in_array($key, ['exitDate', 'exitReason'], true) => $data['residenceStatus'] === 'Sudah keluar',
            in_array($key, ['guardianName', 'guardianRelationship', 'guardianContact', 'guardianAddress'], true) => $data['guardianStatus'] === 'Keluarga/wali diketahui',
            in_array($key, ['schoolName', 'educationLevel'], true) => $data['educationStatus'] === 'Sedang sekolah',
            $key === 'healthCoverageNumber' => $data['healthCoverageStatus'] === 'Ada',
            default => true,
        };
    }

    /** @return array<string, mixed> Nilai awal form Tambah Klien. */
    public static function blank(): array
    {
        $blank = [];
        foreach (self::fields() as $key => $field) {
            $blank[$key] = ($field['type'] ?? 'text') === 'checkbox' ? false : '';
        }

        return array_merge($blank, [
            'photo' => '',
            'category' => 'Anak',
            'serviceType' => 'Tinggal di panti',
            'residenceStatus' => 'Masih menerima layanan',
            'admissionDate' => DemoClock::TODAY,
        ]);
    }
}
