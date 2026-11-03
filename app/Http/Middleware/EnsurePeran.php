<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Penjaga dashboard: harus masuk, akun aktif, sandi sementara Admin sudah diganti, dan perannya cocok. */
class EnsurePeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        $akun = $request->user();
        if (! $akun) {
            return $request->expectsJson() ? response()->json(['message' => 'Sesi berakhir. Silakan masuk lagi.'], 401) : redirect()->route('login');
        }
        if (! $akun->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akses akun ini sudah dicabut. Hubungi Admin Dinas Sosial.']);
        }
        if ($akun->wajib_ganti_sandi && ! $request->is('account/password')) {
            return redirect()->route('password.edit');
        }
        abort_unless($peran === [] || in_array($akun->peran, $peran, true), 403);

        return $next($request);
    }
}
