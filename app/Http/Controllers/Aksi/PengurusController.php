<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\PengurusLks;
use App\Services\PenyimpananBerkas;
use App\Support\Data\DataPengurus;
use App\Support\Data\Kalender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Pengurus dan SDM disimpan langsung oleh lembaga pemilik, tanpa persetujuan Admin. */
class PengurusController extends Controller
{
    private function aturan(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'digits:16'],
            'birthPlace' => ['required', 'string', 'max:150'],
            'birthDate' => ['required', 'date', 'before_or_equal:'.Kalender::today()],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'contact' => ['required', 'string', 'max:40'],
            'ktpAddress' => ['required', 'string', 'max:1000'],
            'npwp' => ['nullable', 'string', 'max:150'],
            'category' => ['required', Rule::in(['Pengurus', 'Staf', 'Tenaga layanan', 'Relawan'])],
            'position' => ['nullable', 'string', 'max:150'],
            'joined' => ['nullable', 'date', 'before_or_equal:'.Kalender::today()],
            'notes' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'removePhoto' => ['nullable', 'boolean'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'removeAttachment' => ['nullable', 'boolean'],
        ];
    }

    private function simpan(Request $request, PengurusLks $pengurus, array $data, PenyimpananBerkas $penyimpanan): void
    {
        foreach (DataPengurus::KOLOM as $isian => $kolom) {
            if (array_key_exists($isian, $data)) {
                $pengurus->{$kolom} = $data[$isian] === '' ? null : $data[$isian];
            }
        }
        $pengurus->save();
        $lembagaId = $pengurus->lembaga_id;
        $fotoLama = $pengurus->foto_berkas_id;
        if ($request->hasFile('photo')) {
            $pengurus->foto_berkas_id = $penyimpanan->unggah($request->file('photo'), $lembagaId, 'pengurus-foto', $pengurus->id)->id;
        } elseif (! empty($data['removePhoto'])) {
            $pengurus->foto_berkas_id = null;
        }
        $pengurus->save();
        if ($fotoLama !== $pengurus->foto_berkas_id) {
            $penyimpanan->hapus($fotoLama);
        }
        if (! empty($data['removeAttachment'])) {
            $penyimpanan->hapusSumber('pengurus', $pengurus->id);
        }
        foreach ($request->file('attachments', []) as $lampiran) {
            $penyimpanan->unggah($lampiran, $lembagaId, 'pengurus', $pengurus->id);
        }
    }

    public function store(Request $request, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $data = $request->validate($this->aturan());
        DB::transaction(fn () => $this->simpan($request, new PengurusLks(['lembaga_id' => $request->user()->lembaga_id]), $data, $penyimpanan));
        $request->session()->flash('status', 'Personel tersimpan.');

        return response()->json(['message' => 'Personel tersimpan.', 'redirect' => '/lks/members']);
    }

    public function update(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $data = $request->validate($this->aturan() + ['version' => ['required', 'integer']]);
        DB::transaction(function () use ($request, $id, $data, $penyimpanan) {
            $pengurus = PengurusLks::where('lembaga_id', $request->user()->lembaga_id)->lockForUpdate()->findOrFail($id);
            abort_unless($pengurus->versi === (int) $data['version'], 409, 'Data personel berubah. Muat ulang sebelum menyimpan.');
            $pengurus->versi++;
            $this->simpan($request, $pengurus, $data, $penyimpanan);
        });
        $request->session()->flash('status', 'Personel tersimpan.');

        return response()->json(['message' => 'Personel tersimpan.', 'redirect' => '/lks/member/'.$id]);
    }

    public function destroy(Request $request, string $id, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        DB::transaction(function () use ($request, $id, $penyimpanan) {
            $pengurus = PengurusLks::where('lembaga_id', $request->user()->lembaga_id)->findOrFail($id);
            $foto = $pengurus->foto_berkas_id;
            $pengurus->delete();
            $penyimpanan->hapus($foto);
            $penyimpanan->hapusSumber('pengurus', $id);
        });
        $request->session()->flash('status', 'Personel dihapus.');

        return response()->json(['message' => 'Personel dihapus.', 'redirect' => '/lks/members']);
    }
}
