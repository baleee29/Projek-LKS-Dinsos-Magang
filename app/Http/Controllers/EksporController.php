<?php

namespace App\Http\Controllers;

use App\Models\LaporanSemester;
use App\Models\PendaftaranLks;
use App\Support\ClientRecap;
use App\Support\Data\DataKlien;
use App\Support\Data\DataLaporan;
use App\Support\Data\DataLembaga;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataSurat;
use App\Support\Data\Kalender;
use App\Support\LetterListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Data untuk modul ekspor di public/assets/ekspor, dalam bentuk field yang dibaca modul itu.
 * Isinya mengikuti filter halaman. LKS selalu dibatasi lembaganya sendiri, dan Admin tidak menerima draf.
 */
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

    public function klien(Request $request): JsonResponse
    {
        $admin = $this->admin($request);
        $sumber = $admin ? DataKlien::all() : DataKlien::forOrganization(DataLembaga::own()['id']);
        $lembaga = $admin ? DataLembaga::raw() : collect([DataLembaga::own()]);
        $data = ClientRecap::build($request, $sumber, $lembaga, $admin, $admin ? 'Seluruh LKS' : DataLembaga::own()['title']);
        $filter = $data['filters'];

        return response()->json([
            'kind' => 'klien',
            'today' => Kalender::today(),
            'items' => $data['allItems']->map(fn (array $klien) => ['organizationId' => $klien['organizationId'], 'data' => $klien])->values(),
            'organizations' => $lembaga->filter(fn (array $org) => (! $filter['org'] || $org['id'] === $filter['org'])
                && (! $filter['district'] || $org['district'] === $filter['district'])
                && (! $filter['village'] || $org['village'] === $filter['village']))->values(),
        ]);
    }

    public function pengurus(Request $request): JsonResponse
    {
        $lembagaId = $this->admin($request) ? (string) $request->query('org', '') : DataLembaga::own()['id'];
        $org = DataLembaga::find($lembagaId);
        abort_unless($org, 404);
        $kategori = (string) $request->query('category', '');
        $q = (string) $request->query('q', '');

        return response()->json([
            'kind' => 'pengurus',
            'today' => Kalender::today(),
            'org' => $org,
            'members' => DataPengurus::forOrganization($lembagaId)
                ->filter(fn (array $p) => (! $kategori || $p['category'] === $kategori) && $this->lolos($q, [$p['title'], $p['nik'], $p['position'], $p['category']]))->values(),
        ]);
    }

    public function lembaga(Request $request): JsonResponse
    {
        abort_unless($this->admin($request), 403);
        $kecamatan = (string) $request->query('district', '');
        $kelurahan = (string) $request->query('village', '');
        $q = (string) $request->query('q', '');
        $rekening = PendaftaranLks::whereNotNull('lembaga_id')->pluck('rekening_bank', 'lembaga_id');

        return response()->json([
            'kind' => 'lembaga',
            'items' => DataLembaga::all()
                ->filter(fn (array $l) => (! $kecamatan || $l['district'] === $kecamatan) && (! $kelurahan || $l['village'] === $kelurahan) && $this->lolos($q, [$l['title'], $l['chair'], $l['contact']]))
                ->map(fn (array $l) => $l + ['name' => $l['title'], 'rekening' => (string) ($rekening[$l['id']] ?? '')])->values(),
            'members' => DataPengurus::all()->map(fn (array $p) => ['organizationId' => $p['organizationId']])->values(),
        ]);
    }

    /** Modul sumber memakai quarter 1 dan 2, sedangkan view memakai 1 dan 3. */
    private function laporanModul(array $laporan): array
    {
        $lembaga = DataLembaga::find($laporan['organizationId']);

        return [
            'quarter' => (int) $laporan['quarter'] <= 2 ? 1 : 2,
            'period_type' => 'semester',
            'periodLabel' => DataLaporan::periodLabel($laporan),
            'organizationSnapshot' => $lembaga,
        ] + $laporan;
    }

    public function laporan(Request $request): JsonResponse
    {
        $admin = $this->admin($request);
        $kosong = fn (string $nilai) => in_array($nilai, ['all', ''], true);
        $tahun = (string) $request->query('year', 'all');
        $semester = (string) $request->query('quarter', 'all');
        $status = (string) $request->query('status', '');
        $kecamatan = (string) $request->query('district', '');
        $kelurahan = (string) $request->query('village', '');
        $q = (string) $request->query('q', '');
        $baris = DataLaporan::all()->filter(function (array $l) use ($kosong, $tahun, $semester, $status, $kecamatan, $kelurahan, $q) {
            $lembaga = DataLembaga::find($l['organizationId']);

            return ($kosong($tahun) || (string) $l['year'] === $tahun)
                && ($kosong($semester) || ((int) $l['quarter'] <= 2 ? '1' : '2') === $semester)
                && (! $status || $l['status'] === $status)
                && (! $kecamatan || $lembaga['district'] === $kecamatan)
                && (! $kelurahan || $lembaga['village'] === $kelurahan)
                && $this->lolos($q, [$lembaga['title'], $l['id'], $l['title'], $l['summary']]);
        })->map(fn (array $l) => $this->laporanModul($l))->values();

        if ($admin) {
            return response()->json([
                'kind' => 'laporanAdmin',
                'today' => Kalender::today(),
                'reports' => $baris->sortByDesc('submittedAt')->values(),
                // Modul sumber menulis periode hanya bila tahun terisi, dan quarter all berarti setahun penuh.
                'context' => ['year' => $kosong($tahun) ? '' : $tahun, 'quarter' => $kosong($semester) ? ($kosong($tahun) ? '' : 'all') : $semester, 'status' => $status, 'district' => $kecamatan, 'village' => $kelurahan],
            ]);
        }

        return response()->json([
            'kind' => 'laporanLks',
            'today' => Kalender::today(),
            'items' => $baris,
            'organization' => DataLembaga::own(),
            'state' => ['year' => $kosong($tahun) ? '' : $tahun, 'quarter' => $kosong($semester) ? '' : $semester, 'status' => $status],
        ]);
    }

    public function laporanDetail(Request $request, string $id): JsonResponse
    {
        abort_unless($this->admin($request), 403);
        $laporan = DataLaporan::find($id);
        abort_unless($laporan, 404);

        return response()->json([
            'kind' => 'laporanDetail',
            'report' => $this->laporanModul($laporan) + ['history' => LaporanSemester::whereKey($id)->value('riwayat') ?? []],
        ]);
    }

    public function surat(Request $request): JsonResponse
    {
        $admin = $this->admin($request);
        $data = LetterListing::build($request, $admin ? DataSurat::all() : DataSurat::forLks(DataLembaga::own()['id']), $admin);
        $bulanKe = (int) $data['filters']['month'];
        $bulan = $bulanKe >= 1 && $bulanKe <= 12 ? $data['months'][$bulanKe - 1] : '';
        $tahun = $data['filters']['year'] !== 'all' ? $data['filters']['year'] : '';
        [$teks, $slug] = match (true) {
            $bulan && $tahun => ["{$bulan} {$tahun}", "_{$bulan}_{$tahun}"],
            (bool) $bulan => ["Bulan {$bulan}", "_Bulan_{$bulan}"],
            (bool) $tahun => ["Tahun {$tahun}", "_Tahun_{$tahun}"],
            default => ['Semua Periode', ''],
        };
        $keluar = $data['isOutgoing'];

        return response()->json([
            'kind' => 'surat',
            'today' => Kalender::today(),
            'items' => $data['letters'],
            'isOutgoing' => $keluar,
            'adminMode' => $admin,
            'periodText' => $teks,
            'periodSlug' => $slug,
            'prefix' => ($keluar ? 'Surat_Keluar_LKS' : 'Surat_Masuk_LKS').($admin ? '_Admin' : ''),
        ]);
    }
}
