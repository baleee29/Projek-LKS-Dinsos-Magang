<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Services\Pemberitahu;
use App\Services\PenyimpananBerkas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfilLembagaController extends Controller
{
    public function update(Request $request, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $data = $request->validate([
            'version' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:150'],
            'chair' => ['required', 'string', 'max:150'],
            'contact' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:3000'],
            'legal' => ['nullable', 'string', 'max:3000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'removeLogo' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $data, $penyimpanan) {
            $lembaga = Lembaga::lockForUpdate()->findOrFail($request->user()->lembaga_id);
            abort_unless($lembaga->versi === (int) $data['version'], 409, 'Profil berubah. Muat ulang sebelum menyimpan.');
            $logoLama = $lembaga->logo_berkas_id;
            $lembaga->fill([
                'nama' => $data['title'], 'ketua' => $data['chair'], 'kontak' => $data['contact'] ?? null, 'alamat' => $data['address'] ?? null,
                'legalitas' => $data['legal'] ?? null, 'lintang' => $data['latitude'] ?? null, 'bujur' => $data['longitude'] ?? null, 'versi' => $lembaga->versi + 1,
            ]);
            if ($request->hasFile('logo')) {
                $lembaga->logo_berkas_id = $penyimpanan->unggah($request->file('logo'), $lembaga->id, 'lembaga', $lembaga->id)->id;
            } elseif (! empty($data['removeLogo'])) {
                $lembaga->logo_berkas_id = null;
            }
            $lembaga->save();
            if ($logoLama !== $lembaga->logo_berkas_id) {
                $penyimpanan->hapus($logoLama);
            }
            Pemberitahu::keAdmin([
                'title' => 'Data lembaga diperbarui', 'message' => "{$lembaga->nama} memperbarui profil lembaga.", 'organizationTitle' => $lembaga->nama,
                'periodLabel' => 'Pembaruan data', 'url' => '/admin/organization/'.$lembaga->id,
            ]);
        });

        $request->session()->flash('status', 'Profil lembaga tersimpan.');

        return response()->json(['message' => 'Profil lembaga tersimpan.']);
    }
}
