<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\BerkasTersimpan;
use App\Models\LaporanSemester;
use App\Models\Lembaga;
use App\Services\Pemberitahu;
use App\Services\PenyimpananBerkas;
use App\Support\Data\Kalender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Status laporan hanya Draf dan Terkirim. Perubahan setelah terkirim tetap privat sampai dikirim ulang. */
class LaporanController extends Controller
{
    public function bukaPeriode(Request $request): JsonResponse
    {
        $data = $request->validate(['year' => ['required', 'integer', 'between:2000,2100'], 'semester' => ['required', 'integer', 'in:1,2']]);
        $laporan = LaporanSemester::firstOrCreate(
            ['lembaga_id' => $request->user()->lembaga_id, 'tahun' => $data['year'], 'semester' => $data['semester']],
            ['status' => LaporanSemester::DRAF],
        );

        return response()->json(['message' => 'Laporan dibuka.', 'redirect' => '/lks/edit/'.$laporan->id]);
    }

    public function simpan(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $data = $request->validate([
            'version' => ['required', 'integer'],
            'action' => ['required', 'in:draft,send'],
            'title' => ['nullable', 'string', 'max:255'],
            'kept' => ['nullable', 'array'],
            'kept.*' => ['uuid'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,txt', 'max:5120'],
        ]);
        $kirim = $data['action'] === 'send';

        $pesan = DB::transaction(function () use ($request, $id, $data, $kirim, $penyimpanan) {
            $laporan = LaporanSemester::where('lembaga_id', $request->user()->lembaga_id)->lockForUpdate()->findOrFail($id);
            abort_unless($laporan->versi === (int) $data['version'], 409, 'Laporan berubah. Muat ulang sebelum menyimpan.');

            $dipertahankan = BerkasTersimpan::where('jenis_sumber', 'laporan')->where('id_sumber', $laporan->id)->whereIn('id', $data['kept'] ?? [])->pluck('id')->all();
            $lampiran = $dipertahankan;
            foreach ($request->file('files', []) as $berkas) {
                $lampiran[] = $penyimpanan->unggah($berkas, $laporan->lembaga_id, 'laporan', $laporan->id)->id;
            }
            if (count($lampiran) > 10) {
                throw ValidationException::withMessages(['files' => 'Maksimal 10 berkas untuk satu laporan.']);
            }
            $judul = trim((string) ($data['title'] ?? ''));

            if ($kirim) {
                if ($judul === '' || ! $lampiran) {
                    throw ValidationException::withMessages(['title' => 'Judul dan minimal satu berkas wajib ada sebelum laporan dikirim.']);
                }
                $periode = Kalender::period($laporan->tahun, $laporan->semester === 1 ? 1 : 3);
                if ($periode['interim']) {
                    throw ValidationException::withMessages(['title' => 'Pengiriman tersedia setelah periode berakhir.']);
                }
                $ulang = $laporan->status === LaporanSemester::TERKIRIM;
                $laporan->fill([
                    'judul' => $judul, 'lampiran' => $lampiran, 'draf_data' => null, 'status' => LaporanSemester::TERKIRIM,
                    'dikirim_oleh' => $request->user()->id, 'dikirim_pada' => now(),
                    'riwayat' => [...($laporan->riwayat ?? []), ['status' => 'Terkirim', 'at' => now()->toIso8601String(), 'actor' => $request->user()->nama, 'version' => $laporan->versi + 1]],
                ]);
                $nama = Lembaga::whereKey($laporan->lembaga_id)->value('nama');
                Pemberitahu::keAdmin([
                    'title' => $ulang ? 'Laporan dikirim ulang' : 'Laporan semester baru masuk',
                    'message' => $ulang ? "{$nama} memperbarui laporan semester." : "{$nama} mengirim laporan semester.",
                    'organizationTitle' => $nama, 'periodLabel' => $periode['label'], 'url' => '/admin/report/'.$laporan->id,
                    'fields' => ['title' => $judul, 'year' => $laporan->tahun, 'quarter' => $periode['label']],
                ]);
            } elseif ($laporan->status === LaporanSemester::TERKIRIM) {
                $laporan->draf_data = ['judul' => $judul, 'lampiran' => $lampiran];
            } else {
                $laporan->fill(['judul' => $judul, 'lampiran' => $lampiran]);
            }
            $laporan->versi++;
            $laporan->save();

            // Berkas yang tidak lagi dirujuk kiriman maupun draf dibuang.
            $terpakai = [...($laporan->lampiran ?? []), ...($laporan->draf_data['lampiran'] ?? [])];
            BerkasTersimpan::where('jenis_sumber', 'laporan')->where('id_sumber', $laporan->id)->whereNotIn('id', $terpakai)->delete();

            return $kirim ? 'Laporan terkirim ke Admin.' : 'Draf tersimpan.';
        });
        $request->session()->flash('status', $pesan);

        return response()->json(['message' => $pesan, 'redirect' => $kirim ? '/lks/report/'.$id : '/lks/edit/'.$id]);
    }

    public function destroy(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        DB::transaction(function () use ($request, $id, $penyimpanan) {
            $laporan = LaporanSemester::where('lembaga_id', $request->user()->lembaga_id)->lockForUpdate()->findOrFail($id);
            abort_unless($laporan->status === LaporanSemester::DRAF, 409, 'Laporan terkirim tidak dapat dihapus.');
            $laporan->delete();
            $penyimpanan->hapusSumber('laporan', $id);
        });
        $request->session()->flash('status', 'Draf dihapus.');

        return response()->json(['message' => 'Draf dihapus.', 'redirect' => '/lks/reports']);
    }
}
