<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AkunAdminController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $akun = $request->user();
        $data = $request->validate([
            'version' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254', Rule::unique('pengguna', 'email')->ignore($akun->id)],
            'position' => ['required', 'string', 'max:100'],
            'showUnreadBadge' => ['nullable', 'boolean'],
        ]);
        abort_unless($akun->versi === (int) $data['version'], 409, 'Akun berubah. Muat ulang sebelum menyimpan.');
        $akun->fill([
            'nama' => $data['name'], 'email' => strtolower($data['email']), 'jabatan' => $data['position'],
            'preferensi_notifikasi' => ['showUnreadBadge' => $request->boolean('showUnreadBadge')], 'versi' => $akun->versi + 1,
        ])->save();
        $request->session()->flash('status', 'Pengaturan akun tersimpan.');

        return response()->json(['message' => 'Pengaturan akun tersimpan.']);
    }
}
