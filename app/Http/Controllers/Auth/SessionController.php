<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController extends Controller
{
    private const PERAN = ['admin', 'lks'];

    public function create(Request $request): View|RedirectResponse
    {
        if ($akun = $request->user()) {
            return redirect("/{$akun->peran}/overview");
        }

        return view('auth.login', [
            // Masuk cepat hanya untuk pengembangan lokal, memakai akun dari seeder data contoh.
            'demoAccounts' => app()->environment('local') ? [
                ['role' => 'admin', 'label' => 'Admin', 'hint' => 'Dinas Sosial Kota Banjarbaru'],
                ['role' => 'lks', 'label' => 'LKS', 'hint' => 'Satu lembaga contoh'],
            ] : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'string', 'max:255'], 'password' => ['required', 'string', 'max:255']], [
            'email.required' => 'Isi email atau username akun.',
            'password.required' => 'Isi kata sandi.',
        ]);
        $identitas = strtolower(trim($data['email']));
        $kolom = str_contains($identitas, '@') ? 'email' : 'nama_pengguna';

        if (! Auth::attempt([$kolom => $identitas, 'password' => $data['password'], 'aktif' => true])) {
            throw ValidationException::withMessages(['email' => 'Email/username atau kata sandi tidak cocok, atau akses akun sudah dicabut.']);
        }

        return $this->masuk($request);
    }

    public function demo(Request $request, string $role): RedirectResponse
    {
        abort_unless(app()->environment('local') && in_array($role, self::PERAN, true), 404);
        $akun = Pengguna::where('peran', $role)->where('aktif', true)->orderBy('id')->first();
        abort_unless($akun, 404, 'Akun contoh belum ada. Jalankan php artisan db:seed.');
        Auth::login($akun);

        return $this->masuk($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function masuk(Request $request): RedirectResponse
    {
        $request->session()->regenerate();
        $akun = $request->user();

        return $akun->wajib_ganti_sandi ? redirect()->route('password.edit') : redirect("/{$akun->peran}/overview");
    }
}
