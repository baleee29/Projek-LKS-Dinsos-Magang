<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use App\Models\PendaftaranLks;
use App\Models\Pengguna;
use App\Services\Pemberitahu;
use App\Support\KeamananAkun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Pembuatan akun selalu terpisah dari penerimaan pendaftaran. Sandi hanya tampil sekali. */
class AkunLksController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:pengguna,nama_pengguna'],
            'password' => ['required', 'string', KeamananAkun::aturanSandi()],
            'registration_id' => ['nullable', 'uuid'],
        ], ['username.unique' => 'Username sudah dipakai.']);
        $username = strtolower($data['username']);

        DB::transaction(function () use ($data, $username) {
            $pendaftaran = ! empty($data['registration_id']) ? PendaftaranLks::lockForUpdate()->findOrFail($data['registration_id']) : null;
            abort_if($pendaftaran && $pendaftaran->status !== PendaftaranLks::DISETUJUI, 422, 'Pilih pendaftaran yang sudah diterima.');
            abort_if($pendaftaran && Pengguna::where('pendaftaran_id', $pendaftaran->id)->exists(), 422, 'Pendaftaran ini sudah memiliki akun.');
            $lembagaId = $pendaftaran?->lembaga_id ?? Lembaga::create(['nama' => $data['name'], 'kontak' => $data['whatsapp']])->id;
            $email = $username.'@sidaya.id';
            if (Pengguna::where('email', $email)->exists()) {
                $email = $username.'.'.random_int(100, 999).'@sidaya.id';
            }
            Pengguna::create([
                'nama' => $data['name'], 'nama_pengguna' => $username, 'email' => $email, 'telepon' => $data['whatsapp'],
                'kata_sandi' => $data['password'], 'peran' => 'lks', 'lembaga_id' => $lembagaId, 'pendaftaran_id' => $pendaftaran?->id,
            ]);
        });

        return response()->json([
            'message' => 'Akun LKS tersimpan.',
            'whatsapp_url' => KeamananAkun::tautanWhatsapp($data['whatsapp'], $this->pesanAkun($data['name'], $username, $data['password'], 'Akun SiDaya untuk lembaga Anda sudah dibuat.')),
        ]);
    }

    public function reset(Request $request, int $id): JsonResponse
    {
        $data = $request->validate(['password' => ['required', 'string', KeamananAkun::aturanSandi()]]);
        $akun = $this->akun($id);

        DB::transaction(function () use ($request, $akun, $data) {
            $akun->forceFill(['kata_sandi' => $data['password']])->save();
            KeamananAkun::akhiriSesi($akun);
            $waktu = now()->format('d-m-Y H:i');
            Pemberitahu::ke($akun, ['title' => 'Kata sandi akun direset', 'message' => "Kata sandi akun Anda direset oleh Admin pada {$waktu}. Masuk dengan kata sandi baru dari petugas.", 'periodLabel' => 'Keamanan akun', 'url' => '/lks/notifications']);
            Pemberitahu::ke($request->user(), ['title' => 'Riwayat reset kata sandi', 'message' => "Anda mereset kata sandi akun {$akun->nama} ({$akun->nama_pengguna}) pada {$waktu} dari alamat IP {$request->ip()}.", 'periodLabel' => 'Keamanan akun', 'url' => '/admin/partner-accounts']);
        });

        return response()->json([
            'message' => 'Kata sandi akun diganti.',
            'whatsapp_url' => $akun->telepon ? KeamananAkun::tautanWhatsapp($akun->telepon, $this->pesanAkun($akun->nama, (string) $akun->nama_pengguna, $data['password'], 'Kata sandi akun SiDaya Anda telah diperbarui oleh Admin.')) : null,
        ]);
    }

    public function cabut(Request $request, int $id): JsonResponse
    {
        $akun = $this->akun($id);
        DB::transaction(function () use ($akun) {
            $akun->forceFill(['aktif' => false, 'kata_sandi' => Str::random(64)])->save();
            KeamananAkun::akhiriSesi($akun);
        });
        $request->session()->flash('status', 'Akses akun dicabut.');

        return response()->json(['message' => 'Akses akun dicabut.']);
    }

    private function akun(int $id): Pengguna
    {
        return Pengguna::where('peran', 'lks')->where('aktif', true)->findOrFail($id);
    }

    private function pesanAkun(string $nama, string $username, string $sandi, string $pembuka): string
    {
        return "Yth. Pengurus *{$nama}*,\n\n{$pembuka}\n\nHalaman masuk: ".url('/login')."\nUsername: *{$username}*\nKata sandi: *{$sandi}*\n\nMohon simpan kata sandi ini dengan baik.\n\nSalam hormat,\n*Dinas Sosial Kota Banjarbaru*";
    }
}
