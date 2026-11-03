<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Models\SuratLks;
use App\Services\Pemberitahu;
use App\Services\PenyimpananBerkas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratController extends Controller
{
    private const DINAS = 'Dinas Sosial Kota Banjarbaru';

    public function store(Request $request, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $akun = $request->user();
        $admin = $akun->peran === 'admin';
        $data = $request->validate([
            'organization_id' => [$admin ? 'required' : 'nullable', 'uuid', 'exists:lembaga,id'],
            'letter_number' => ['nullable', 'string', 'max:120'],
            'letter_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ]);
        // LKS selalu memakai lembaganya sendiri dari sesi.
        $lembagaId = $admin ? $data['organization_id'] : $akun->lembaga_id;

        DB::transaction(function () use ($request, $akun, $admin, $data, $lembagaId, $penyimpanan) {
            $nama = Lembaga::whereKey($lembagaId)->value('nama');
            $surat = SuratLks::create([
                'lembaga_id' => $lembagaId,
                'pengguna_id' => $akun->id,
                'arah' => $admin ? SuratLks::DINAS_KE_LKS : SuratLks::LKS_KE_DINAS,
                'nomor_surat' => $data['letter_number'] ?? null,
                'pengirim' => $admin ? self::DINAS : $nama,
                'tujuan' => $admin ? $nama : self::DINAS,
                'perihal' => $data['subject'],
                'tanggal_surat' => $data['letter_date'],
                'keterangan' => $data['description'] ?? null,
            ]);
            if ($request->hasFile('file')) {
                $surat->update(['berkas_id' => $penyimpanan->unggah($request->file('file'), $lembagaId, 'surat', $surat->id)->id]);
            }
            $isi = ['organizationTitle' => $nama, 'periodLabel' => 'Persuratan', 'fields' => ['subject' => $surat->perihal]];
            $admin
                ? Pemberitahu::keLembaga($lembagaId, $isi + ['title' => 'Surat masuk dari Dinas Sosial', 'message' => 'Dinas Sosial mengirim surat kepada lembaga Anda.', 'url' => '/lks/letters'])
                : Pemberitahu::keAdmin($isi + ['title' => 'Surat masuk dari LKS', 'message' => "{$nama} mengirim surat ke Dinas Sosial.", 'url' => '/admin/letters/lks']);
        });
        $request->session()->flash('status', 'Surat tersimpan.');

        return response()->json(['message' => 'Surat tersimpan.']);
    }

    /** Hanya penerima yang menandai surat terbaca. */
    public function read(Request $request, string $id): JsonResponse
    {
        $akun = $request->user();
        $query = SuratLks::query();
        $akun->peran === 'admin'
            ? $query->where('arah', SuratLks::LKS_KE_DINAS)
            : $query->where('arah', SuratLks::DINAS_KE_LKS)->where('lembaga_id', $akun->lembaga_id);
        $surat = $query->findOrFail($id);
        $surat->dibaca_pada ??= now();
        $surat->save();
        $request->session()->flash('status', 'Surat ditandai dibaca.');

        return response()->json(['message' => 'Surat ditandai dibaca.']);
    }

    /** Admin boleh menghapus surat apa pun; LKS hanya surat keluar miliknya. */
    public function destroy(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $akun = $request->user();
        DB::transaction(function () use ($akun, $id, $penyimpanan) {
            $query = SuratLks::query();
            if ($akun->peran !== 'admin') {
                $query->where('arah', SuratLks::LKS_KE_DINAS)->where('lembaga_id', $akun->lembaga_id);
            }
            $surat = $query->findOrFail($id);
            $berkas = $surat->berkas_id;
            $surat->delete();
            $penyimpanan->hapus($berkas);
        });
        $request->session()->flash('status', 'Surat dihapus.');

        return response()->json(['message' => 'Surat dihapus.']);
    }
}
