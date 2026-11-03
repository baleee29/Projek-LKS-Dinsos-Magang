<?php

namespace App\Http\Controllers\Aksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function bacaSemua(Request $request): JsonResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        $request->session()->flash('status', 'Semua notifikasi ditandai dibaca.');

        return response()->json(['message' => 'Semua notifikasi ditandai dibaca.']);
    }

    public function belumDibaca(Request $request, string $id): JsonResponse
    {
        $request->user()->notifications()->findOrFail($id)->update(['read_at' => null]);
        $request->session()->flash('status', 'Notifikasi ditandai belum dibaca.');

        return response()->json(['message' => 'Notifikasi ditandai belum dibaca.', 'redirect' => '/'.$request->user()->peran.'/notifications']);
    }
}
