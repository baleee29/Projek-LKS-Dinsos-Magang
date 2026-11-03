<?php

namespace App\Http\Controllers;

use App\Support\ClientRecap;
use App\Support\Data\DataKlien;
use App\Support\Data\DataLaporan;
use App\Support\Data\DataLembaga;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataSurat;
use App\Support\Data\Kalender;
use App\Support\Format;
use App\Support\LetterListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/** Data ekspor sesuai filter halaman. LKS selalu dibatasi lembaganya sendiri. */
class EksporController extends Controller
{
    private function admin(Request $request): bool
    {
        return $request->user()->peran === 'admin';
    }

    private function lolos(string $q, array $nilai): bool
    {
        $q = mb_strtolower(trim($q));

        return ! $q || collect($nilai)->contains(fn ($isi) => str_contains(mb_strtolower((string) $isi), $q));
    }

    /** @param  array<int, string>  $kolom @param Collection<int, array<int, mixed>> $baris */
    private function hasil(string $judul, string $berkas, array $kolom, Collection $baris, string $sub = ''): JsonResponse
    {
        return response()->json([
            'title' => $judul,
            'subtitle' => trim($sub.' Diunduh '.Format::date(Kalender::today()).'.'),
            'filename' => $berkas.'-'.Kalender::today(),
            'columns' => $kolom,
            'rows' => $baris->map(fn (array $row, int $i) => [$i + 1, ...$row])->values(),
        ]);
    }

    public function klien(Request $request): JsonResponse
    {
        $admin = $this->admin($request);
        $sumber = $admin ? DataKlien::all() : DataKlien::forOrganization(DataLembaga::own()['id']);
        $data = ClientRecap::build($request, $sumber, $admin ? DataLembaga::raw() : collect([DataLembaga::own()]), $admin, $admin ? 'Seluruh LKS' : DataLembaga::own()['title']);
        $baris = $data['allItems']->map(fn (array $k) => [
            $k['code'], $k['name'], DataLembaga::find($k['organizationId'])['title'] ?? '', $k['category'], $k['gender'], $k['nik'], $k['birthPlace'], $k['birthDate'] ?: 'Tidak diketahui',
            $k['admissionDate'], $k['serviceType'], $k['residenceStatus'], $k['guardianName'], $k['caseWorker'],
        ])->values();

        return $this->hasil('Rekap Klien LKS', 'rekap-klien', ['No', 'Kode', 'Nama', 'Lembaga', 'Kategori', 'Jenis kelamin', 'NIK', 'Tempat lahir', 'Tanggal lahir', 'Tanggal masuk', 'Bentuk layanan', 'Status', 'Wali', 'Pendamping'], $baris, $data['caption'].'.');
    }

    public function pengurus(Request $request): JsonResponse
    {
        $lembagaId = $this->admin($request) ? (string) $request->query('org', '') : DataLembaga::own()['id'];
        $sumber = $lembagaId ? DataPengurus::forOrganization($lembagaId) : DataPengurus::all();
        $kategori = (string) $request->query('category', '');
        $q = (string) $request->query('q', '');
        $baris = $sumber->filter(fn (array $p) => (! $kategori || $p['category'] === $kategori) && $this->lolos($q, [$p['title'], $p['nik'], $p['position'], $p['category']]))
            ->map(fn (array $p) => [$p['title'], DataLembaga::find($p['organizationId'])['title'] ?? '', $p['nik'], $p['gender'], $p['contact'], $p['category'], $p['position'], $p['joined']])->values();

        return $this->hasil('Pengurus dan SDM LKS', 'pengurus-sdm', ['No', 'Nama', 'Lembaga', 'NIK', 'Jenis kelamin', 'Kontak', 'Kategori', 'Jabatan', 'Tanggal bergabung'], $baris, DataLembaga::find($lembagaId)['title'] ?? 'Seluruh LKS.');
    }

    public function lembaga(Request $request): JsonResponse
    {
        abort_unless($this->admin($request), 403);
        $kecamatan = (string) $request->query('district', '');
        $kelurahan = (string) $request->query('village', '');
        $q = (string) $request->query('q', '');
        $baris = DataLembaga::all()->filter(fn (array $l) => (! $kecamatan || $l['district'] === $kecamatan) && (! $kelurahan || $l['village'] === $kelurahan) && $this->lolos($q, [$l['title'], $l['chair'], $l['contact']]))
            ->map(fn (array $l) => [$l['title'], $l['chair'], $l['contact'], $l['district'], $l['village'], $l['address'], $l['legal'], $l['registeredFrom']])->values();

        return $this->hasil('Data LKS', 'data-lks', ['No', 'Nama lembaga', 'Penanggung jawab', 'Kontak', 'Kecamatan', 'Kelurahan', 'Alamat', 'Legalitas', 'Terdaftar sejak'], $baris);
    }

    public function laporan(Request $request): JsonResponse
    {
        $tahun = (string) $request->query('year', 'all');
        $semester = (string) $request->query('quarter', 'all');
        $status = (string) $request->query('status', '');
        $kecamatan = (string) $request->query('district', '');
        $kelurahan = (string) $request->query('village', '');
        $q = (string) $request->query('q', '');
        $baris = DataLaporan::all()->filter(function (array $l) use ($tahun, $semester, $status, $kecamatan, $kelurahan, $q) {
            $lembaga = DataLembaga::find($l['organizationId']);

            return (in_array($tahun, ['all', ''], true) || (string) $l['year'] === $tahun)
                && (in_array($semester, ['all', ''], true) || ((int) $l['quarter'] <= 2 ? '1' : '2') === $semester)
                && (! $status || $l['status'] === $status)
                && (! $kecamatan || $lembaga['district'] === $kecamatan)
                && (! $kelurahan || $lembaga['village'] === $kelurahan)
                && $this->lolos($q, [$lembaga['title'], $l['title']]);
        })->map(fn (array $l) => [DataLembaga::find($l['organizationId'])['title'], DataLaporan::periodLabel($l), $l['title'], $l['status'], count($l['attachments']), Format::date($l['submittedAt'], '-')])->values();

        return $this->hasil('Laporan Semester LKS', 'laporan-semester', ['No', 'Lembaga', 'Periode', 'Judul', 'Status', 'Jumlah berkas', 'Dikirim'], $baris);
    }

    public function laporanDetail(Request $request, string $id): JsonResponse
    {
        $laporan = DataLaporan::find($id);
        abort_unless($laporan, 404);
        $lembaga = DataLembaga::find($laporan['organizationId']);
        $periode = DataLaporan::periodLabel($laporan);
        $baris = collect([
            ['Lembaga', $lembaga['title']], ['Penanggung jawab', $lembaga['chair']], ['Wilayah', trim($lembaga['village'].', '.$lembaga['district'], ', ')],
            ['Kontak', $lembaga['contact']], ['Alamat', $lembaga['address']], ['Legalitas', $lembaga['legal']], ['Periode', $periode], ['Judul', $laporan['title']],
            ['Status', $laporan['status']], ['Dikirim', Format::date($laporan['submittedAt'], '-')],
            ...collect($laporan['attachments'])->map(fn (array $berkas, int $i) => ['Lampiran '.($i + 1), $berkas['name']])->all(),
        ]);

        return $this->hasil('Laporan '.$periode, 'laporan-'.str_replace(' ', '-', strtolower($periode)), ['No', 'Keterangan', 'Isi'], $baris, $lembaga['title'].'.');
    }

    public function surat(Request $request): JsonResponse
    {
        $admin = $this->admin($request);
        $data = LetterListing::build($request, $admin ? DataSurat::all() : DataSurat::forLks(DataLembaga::own()['id']), $admin);
        $baris = $data['letters']->map(fn (array $s) => [$s['letter_number'], Format::date($s['letter_date']), $s['subject'], $s['sender'], $s['destination'], $s['description'], $s['file_name'], $s['is_read'] ? 'Sudah' : 'Belum'])->values();

        return $this->hasil($data['isOutgoing'] ? 'Daftar Surat Keluar' : 'Daftar Surat Masuk', 'daftar-surat', ['No', 'Nomor surat', 'Tanggal', 'Perihal', 'Pengirim', 'Tujuan', 'Keterangan', 'Berkas', 'Dibaca'], $baris);
    }
}
