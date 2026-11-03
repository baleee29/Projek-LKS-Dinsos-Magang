<?php

namespace Database\Seeders;

use App\Models\BerkasPendaftaran;
use App\Models\KlienLks;
use App\Models\LaporanSemester;
use App\Models\Lembaga;
use App\Models\Notifikasi;
use App\Models\PendaftaranLks;
use App\Models\Pengguna;
use App\Models\PengurusLks;
use App\Models\SuratLks;
use App\Notifications\NotifikasiDashboard;
use App\Services\PenyimpananBerkas;
use App\Support\Data\DataKlien;
use App\Support\Data\DataPengurus;
use Carbon\Carbon;
use Database\Seeders\Contoh\DemoAccounts;
use Database\Seeders\Contoh\DemoClients;
use Database\Seeders\Contoh\DemoLetters;
use Database\Seeders\Contoh\DemoMembers;
use Database\Seeders\Contoh\DemoNotifications;
use Database\Seeders\Contoh\DemoOrganizations;
use Database\Seeders\Contoh\DemoRegistrations;
use Database\Seeders\Contoh\DemoReports;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/** Data fiktif untuk pengembangan dan pengujian. Tidak dijalankan di produksi. */
class DataContohSeeder extends Seeder
{
    public const SANDI_ADMIN = 'SidayaAdmin2026';

    public const SANDI_LKS = 'SidayaLks2026';

    /** @var array<string, string> ID data contoh => ID database */
    private array $peta = [];

    private PenyimpananBerkas $berkas;

    public function run(PenyimpananBerkas $berkas): void
    {
        if (Lembaga::exists()) {
            return;
        }
        $this->berkas = $berkas;
        $this->lembaga();
        $this->pendaftaran();
        $this->akun();
        $this->pengurus();
        $this->klien();
        $this->laporan();
        $this->surat();
        $this->notifikasi();
    }

    /** PDF satu halaman yang sah, supaya tombol unduh pada data contoh benar-benar menghasilkan berkas. */
    private function isiContoh(string $judul): string
    {
        $teks = preg_replace('/[^A-Za-z0-9 ._-]/', '', $judul);
        $isi = "BT /F1 14 Tf 60 760 Td (Berkas contoh SiDaya: {$teks}) Tj ET";
        $objek = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($isi)." >>\nstream\n{$isi}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];
        $pdf = "%PDF-1.4\n";
        $letak = [];
        foreach ($objek as $i => $badan) {
            $letak[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n{$badan}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 6\n0000000000 65535 f \n";
        foreach ($letak as $posisi) {
            $pdf .= sprintf("%010d 00000 n \n", $posisi);
        }

        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
    }

    private function simpanBerkas(string $nama, ?string $lembagaId, string $jenis, string $idSumber): string
    {
        $namaPdf = preg_replace('/\.[A-Za-z0-9]+$/', '', $nama).'.pdf';

        return $this->berkas->simpan($this->isiContoh($namaPdf), $namaPdf, 'application/pdf', $lembagaId, $jenis, $idSumber)->id;
    }

    private function waktu(?string $nilai): ?Carbon
    {
        return $nilai ? Carbon::parse($nilai) : null;
    }

    private function lembaga(): void
    {
        foreach (DemoOrganizations::raw() as $baris) {
            $lembaga = Lembaga::create([
                'nama' => $baris['title'], 'ketua' => $baris['chair'], 'alamat' => $baris['address'], 'kontak' => $baris['contact'],
                'kecamatan' => $baris['district'], 'kelurahan' => $baris['village'], 'legalitas' => $baris['legal'], 'masa_bakti' => $baris['term'] ?: null,
                'terdaftar_sejak' => $baris['registeredFrom'], 'lintang' => $baris['latitude'], 'bujur' => $baris['longitude'],
            ]);
            $this->peta[$baris['id']] = $lembaga->id;
        }
    }

    private function pendaftaran(): void
    {
        $lembagaDari = DemoOrganizations::raw()->filter(fn ($org) => $org['registrationId'])->pluck('id', 'registrationId');
        $admin = Pengguna::where('peran', 'admin')->value('id');
        foreach (DemoRegistrations::raw() as $baris) {
            $pendaftaran = new PendaftaranLks([
                'nama_lembaga' => $baris['organization_name'], 'nama_ketua' => $baris['leader_name'], 'whatsapp' => $baris['whatsapp'],
                'nomor_izin' => $baris['sk_number'], 'bidang_fokus' => $baris['focus_area'],
                'prioritas_pelayanan' => ['Keterlantaran'], 'jenis_pelayanan' => ['Rehabilitasi Sosial'], 'sasaran_pelayanan' => ['Perseorangan'],
                'jenis_kegiatan' => ['Unit pelayanan sosial untuk anak'], 'sifat_pelayanan' => ['Dalam LKS'],
                'status' => $baris['status'], 'catatan' => $baris['notes'] ?: null,
                'ditinjau_oleh' => $baris['reviewed_at'] ? $admin : null, 'ditinjau_pada' => $this->waktu($baris['reviewed_at']),
                'lembaga_id' => isset($lembagaDari[$baris['id']]) ? $this->peta[$lembagaDari[$baris['id']]] : null,
            ]);
            $pendaftaran->dibuat_pada = $this->waktu($baris['created_at']);
            $pendaftaran->diperbarui_pada = $this->waktu($baris['updated_at']);
            $pendaftaran->save();
            $this->peta[$baris['id']] = $pendaftaran->id;
            foreach ($baris['files'] as $file) {
                BerkasPendaftaran::create([
                    'pendaftaran_id' => $pendaftaran->id, 'kunci_berkas' => $file['key'], 'label_berkas' => $file['label'],
                    'berkas_id' => $this->simpanBerkas($file['name'], null, 'pendaftaran', $pendaftaran->id),
                ]);
            }
        }
    }

    private function akun(): void
    {
        foreach (DemoAccounts::raw() as $baris) {
            $asal = substr($baris['id'], strlen('acc-'));
            $lembaga = DemoOrganizations::find($asal);
            Pengguna::create([
                'nama' => $baris['name'], 'nama_pengguna' => $baris['username'], 'email' => $baris['email'], 'telepon' => $baris['phone'] ?: null,
                'kata_sandi' => self::SANDI_LKS, 'peran' => 'lks', 'lembaga_id' => $this->peta[$asal],
                'pendaftaran_id' => $lembaga['registrationId'] ? $this->peta[$lembaga['registrationId']] : null,
            ]);
        }
    }

    private function pengurus(): void
    {
        foreach (DemoMembers::raw() as $baris) {
            $pengurus = new PengurusLks(['lembaga_id' => $this->peta[$baris['organizationId']], 'ketua' => $baris['isChair'], 'versi' => $baris['version']]);
            foreach (DataPengurus::KOLOM as $isian => $kolom) {
                $pengurus->{$kolom} = ($baris[$isian] ?? '') === '' ? null : $baris[$isian];
            }
            $pengurus->save();
            $this->peta[$baris['id']] = $pengurus->id;
            foreach ($baris['attachments'] as $lampiran) {
                $this->simpanBerkas($lampiran['name'], $pengurus->lembaga_id, 'pengurus', $pengurus->id);
            }
        }
    }

    private function klien(): void
    {
        foreach (DemoClients::raw() as $baris) {
            $klien = new KlienLks(['lembaga_id' => $this->peta[$baris['organizationId']], 'status' => $baris['status'], 'versi' => $baris['version'], 'riwayat' => $baris['history']]);
            foreach (DataKlien::KOLOM as $isian => $kolom) {
                $nilai = $baris[$isian] ?? '';
                $klien->{$kolom} = $nilai === '' ? null : $nilai;
            }
            $klien->diperbarui_pada = $this->waktu($baris['updated']);
            $klien->save();
            $this->peta[$baris['id']] = $klien->id;
        }
    }

    private function laporan(): void
    {
        foreach (DemoReports::raw() as $baris) {
            $lembagaId = $this->peta[$baris['organizationId']];
            $laporan = new LaporanSemester([
                'lembaga_id' => $lembagaId, 'tahun' => $baris['year'], 'semester' => (int) $baris['quarter'] <= 2 ? 1 : 2, 'judul' => $baris['title'],
                'status' => $baris['status'], 'data' => ['ringkasan' => $baris['summary']], 'dikirim_pada' => $this->waktu($baris['submittedAt']), 'versi' => $baris['version'],
            ]);
            $laporan->diperbarui_pada = $this->waktu($baris['updated']);
            $laporan->save();
            $this->peta[$baris['id']] = $laporan->id;
            $lampiran = [];
            foreach ($baris['attachments'] as $file) {
                $lampiran[] = $this->simpanBerkas($file['name'], $lembagaId, 'laporan', $laporan->id);
            }
            $laporan->lampiran = $lampiran;
            if ($baris['hasDraftChanges']) {
                $laporan->draf_data = ['judul' => $baris['title'].' (perubahan)', 'lampiran' => $lampiran];
            }
            $laporan->timestamps = false;
            $laporan->save();
        }
    }

    private function surat(): void
    {
        foreach (DemoLetters::raw() as $baris) {
            $lembagaId = $this->peta[$baris['organization_id']];
            $surat = SuratLks::create([
                'lembaga_id' => $lembagaId, 'arah' => $baris['type'] === 'incoming' ? SuratLks::DINAS_KE_LKS : SuratLks::LKS_KE_DINAS,
                'nomor_surat' => $baris['letter_number'] ?: null, 'pengirim' => $baris['sender'], 'tujuan' => $baris['destination'], 'perihal' => $baris['subject'],
                'tanggal_surat' => $baris['letter_date'], 'keterangan' => $baris['description'] ?: null, 'dibaca_pada' => $baris['is_read'] ? now() : null,
            ]);
            $this->peta[$baris['id']] = $surat->id;
            if ($baris['has_file']) {
                $surat->update(['berkas_id' => $this->simpanBerkas($baris['file_name'], $lembagaId, 'surat', $surat->id)]);
            }
        }
    }

    private function notifikasi(): void
    {
        $admin = Pengguna::where('peran', 'admin')->first();
        $lks = Pengguna::where('peran', 'lks')->where('lembaga_id', $this->peta[DemoOrganizations::OWN_LKS])->first();
        $ganti = $this->peta;
        uksort($ganti, fn ($a, $b) => strlen($b) <=> strlen($a));
        foreach (DemoNotifications::raw() as $baris) {
            $penerima = $baris['audience'] === 'admin' ? $admin : $lks;
            $isi = [
                'title' => $baris['title'], 'message' => $baris['message'], 'organizationTitle' => $baris['organizationTitle'], 'periodLabel' => $baris['periodLabel'],
                'url' => strtr($baris['url'], $ganti), 'note' => $baris['note'], 'fields' => $baris['fields'],
            ];
            Notification::sendNow($penerima, new NotifikasiDashboard($isi));
            $terakhir = $penerima->notifications()->reorder('created_at', 'desc')->orderBy('id')->first();
            Notifikasi::whereKey($terakhir->id)->update(['created_at' => $this->waktu($baris['at']), 'read_at' => $baris['read'] ? now() : null]);
        }
    }
}
