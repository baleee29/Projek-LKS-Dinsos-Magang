<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman ganti kata sandi demo. Formulir divalidasi di browser dan tidak pernah dikirim.
 *
 * TODO(Tahap 2): tambahkan PUT /account/password dari AccountPasswordController sumber.
 */
class ChangePasswordController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $role = $request->session()->get('demo_role');

        if (! $role) {
            return redirect()->route('login');
        }

        return view('auth.change-password', [
            'dashboard' => "/{$role}/overview",
        ]);
    }
}
