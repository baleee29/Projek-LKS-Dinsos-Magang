<?php

namespace App\Http\Controllers;

use App\Models\BerkasTersimpan;
use App\Models\IsiLanding;
use App\Services\PenyimpananBerkas;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BerkasController extends Controller
{
    /** Admin boleh semua berkas; LKS hanya berkas lembaganya. Berkas pendaftaran hanya untuk Admin. */
    public function unduh(Request $request, string $id, PenyimpananBerkas $penyimpanan): StreamedResponse
    {
        $berkas = BerkasTersimpan::findOrFail($id);
        $akun = $request->user();
        abort_unless($akun->peran === 'admin' || ($berkas->lembaga_id && $berkas->lembaga_id === $akun->lembaga_id && $berkas->jenis_sumber !== 'pendaftaran'), 403);

        return $penyimpanan->respons($berkas, $request->boolean('unduh'));
    }

    /** Foto landing page bersifat publik, hanya selama masih dipakai. */
    public function media(string $id, PenyimpananBerkas $penyimpanan): StreamedResponse
    {
        abort_unless(IsiLanding::where('berkas_id', $id)->exists(), 404);

        return $penyimpanan->respons(BerkasTersimpan::findOrFail($id));
    }
}
