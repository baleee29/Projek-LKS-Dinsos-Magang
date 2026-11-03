<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\KeamananAkun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Ganti sandi hanya untuk Admin. LKS meminta reset ke Admin. */
class PasswordController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->peran === 'admin', 403);

        return view('auth.change-password', ['dashboard' => '/admin/overview']);
    }

    public function update(Request $request): JsonResponse
    {
        $akun = $request->user();
        abort_unless($akun->peran === 'admin', 403);
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', KeamananAkun::aturanSandi()],
        ]);
        if (! Hash::check($data['current_password'], $akun->kata_sandi)) {
            throw ValidationException::withMessages(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }
        $akun->forceFill(['kata_sandi' => $data['password'], 'wajib_ganti_sandi' => false])->save();
        KeamananAkun::akhiriSesi($akun, $request->session()->getId());

        return response()->json(['message' => 'Kata sandi berhasil diganti.', 'redirect' => '/admin/overview']);
    }
}
