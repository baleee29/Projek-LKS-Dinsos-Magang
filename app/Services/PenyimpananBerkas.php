<?php

namespace App\Services;

use App\Models\BerkasTersimpan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Berkas disimpan di database per potongan 48 KiB, dalam transaksi yang sama dengan data pemiliknya. */
class PenyimpananBerkas
{
    private const POTONGAN = 48 * 1024;

    /** Jenis yang aman dibuka langsung di browser; selain ini selalu diunduh. */
    private const BOLEH_TAMPIL = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];

    public function unggah(UploadedFile $berkas, ?string $lembagaId, string $jenis, string $idSumber): BerkasTersimpan
    {
        return $this->simpan((string) file_get_contents($berkas->getRealPath()), $berkas->getClientOriginalName(), $berkas->getMimeType() ?: 'application/octet-stream', $lembagaId, $jenis, $idSumber);
    }

    public function simpan(string $isi, string $nama, string $mime, ?string $lembagaId, string $jenis, string $idSumber): BerkasTersimpan
    {
        $berkas = BerkasTersimpan::create([
            'lembaga_id' => $lembagaId,
            'jenis_sumber' => $jenis,
            'id_sumber' => $idSumber,
            'nama' => mb_substr(basename(str_replace('\\', '/', $nama)), 0, 255),
            'jenis_mime' => mb_substr($mime, 0, 100),
            'ukuran' => strlen($isi),
            'sha256' => hash('sha256', $isi),
        ]);
        foreach (str_split($isi, self::POTONGAN) as $urutan => $potongan) {
            DB::table('potongan_berkas')->insert(['berkas_id' => $berkas->id, 'urutan' => $urutan, 'isi' => $potongan]);
        }

        return $berkas;
    }

    public function hapus(?string $id): void
    {
        if ($id) {
            BerkasTersimpan::whereKey($id)->delete();
        }
    }

    public function hapusSumber(string $jenis, string $idSumber): void
    {
        BerkasTersimpan::where('jenis_sumber', $jenis)->where('id_sumber', $idSumber)->delete();
    }

    public function respons(BerkasTersimpan $berkas, bool $unduh = false): StreamedResponse
    {
        $cara = $unduh || ! in_array($berkas->jenis_mime, self::BOLEH_TAMPIL, true) ? 'attachment' : 'inline';
        $cadangan = preg_replace('/[^A-Za-z0-9._-]+/', '_', $berkas->nama) ?: 'berkas';

        return response()->stream(function () use ($berkas) {
            DB::table('potongan_berkas')->where('berkas_id', $berkas->id)->orderBy('urutan')->lazyById(20, 'urutan')->each(function ($potongan) {
                echo $potongan->isi;
            });
        }, 200, [
            'Content-Type' => $berkas->jenis_mime,
            'Content-Length' => (string) $berkas->ukuran,
            'Content-Disposition' => HeaderUtils::makeDisposition($cara, $berkas->nama, $cadangan),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0',
        ]);
    }
}
