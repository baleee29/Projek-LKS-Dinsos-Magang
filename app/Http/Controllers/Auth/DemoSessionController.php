<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Login demo. Kata sandi tidak diperiksa dan tidak ada kredensial yang disimpan.
 *
 * TODO(Tahap 2): ganti dengan SessionController sumber (Auth::attempt, throttle, pemeriksaan akun aktif).
 */
class DemoSessionController extends Controller
{
    private const ROLES = ['admin', 'lks'];

    public function create(Request $request): View|RedirectResponse
    {
        if ($role = $request->session()->get('demo_role')) {
            return redirect("/{$role}/overview");
        }

        return view('auth.login', [
            'demoAccounts' => app()->environment('local') ? [
                ['role' => 'admin', 'label' => 'Admin', 'hint' => 'Dinas Sosial Kota Banjarbaru'],
                ['role' => 'lks', 'label' => 'LKS', 'hint' => 'Satu lembaga contoh'],
            ] : [],
        ]);
    }

    /** Form login: peran dibaca dari isian, karena akun sungguhan baru ada di Tahap 2. */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        $identifier = strtolower(trim((string) $request->input('email')));
        $errors = [];

        if ($identifier === '') {
            $errors['email'] = 'Isi email atau username akun.';
        }
        if ((string) $request->input('password') === '') {
            $errors['password'] = 'Isi kata sandi.';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $role = collect(self::ROLES)->first(fn (string $role) => str_contains($identifier, $role));

        if (! $role) {
            throw ValidationException::withMessages([
                'email' => 'Mode demo: gunakan isian yang memuat "admin" atau "lks", atau pilih tombol masuk cepat.',
            ]);
        }

        return $this->enter($request, $role);
    }

    public function demo(Request $request, string $role): RedirectResponse
    {
        abort_unless(app()->environment('local') && in_array($role, self::ROLES, true), 404);

        return $this->enter($request, $role);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function enter(Request $request, string $role): RedirectResponse
    {
        $request->session()->regenerate();
        $request->session()->put('demo_role', $role);

        return redirect("/{$role}/overview");
    }
}
