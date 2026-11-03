<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\IsiLanding;
use App\Services\PenyimpananBerkas;
use App\Support\Data\DataLanding;
use App\Support\LandingRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsiLandingController extends Controller
{
    public function simpan(Request $request, string $kunci, LandingRegistry $registry): JsonResponse
    {
        $slot = $registry->find($kunci);
        abort_unless($slot, 404);
        $batas = $slot['type'] === 'image' ? 300 : ($slot['max'] ?? 3000);
        $data = $request->validate(['value' => ['nullable', 'string', 'max:'.$batas]]);
        IsiLanding::updateOrCreate(['kunci' => $kunci], ['nilai_teks' => $data['value'] ?? '', 'diperbarui_oleh' => $request->user()->id]);
        $request->session()->flash('status', 'Isian tersimpan.');

        return response()->json(['message' => 'Isian tersimpan.']);
    }

    public function foto(Request $request, string $kunci, LandingRegistry $registry, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        $slot = $registry->find($kunci);
        abort_unless($slot && $slot['type'] === 'image', 404);
        $batas = DataLanding::imageLimits();
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$batas['max_kb'], 'dimensions:min_width='.$batas['min_width']]]);

        DB::transaction(function () use ($request, $kunci, $penyimpanan) {
            $baris = IsiLanding::firstOrNew(['kunci' => $kunci]);
            $lama = $baris->berkas_id;
            $baris->fill([
                'berkas_id' => $penyimpanan->unggah($request->file('image'), null, 'landing', $kunci)->id,
                'diperbarui_oleh' => $request->user()->id,
            ])->save();
            $penyimpanan->hapus($lama);
        });
        $request->session()->flash('status', 'Foto tersimpan.');

        return response()->json(['message' => 'Foto tersimpan.']);
    }

    public function kembalikan(Request $request, string $kunci, PenyimpananBerkas $penyimpanan): JsonResponse
    {
        DB::transaction(function () use ($kunci, $penyimpanan) {
            $baris = IsiLanding::where('kunci', $kunci)->first();
            if ($baris) {
                $berkas = $baris->berkas_id;
                $baris->delete();
                $penyimpanan->hapus($berkas);
            }
        });
        $request->session()->flash('status', 'Isian dikembalikan ke bawaan.');

        return response()->json(['message' => 'Isian dikembalikan ke bawaan.']);
    }
}
