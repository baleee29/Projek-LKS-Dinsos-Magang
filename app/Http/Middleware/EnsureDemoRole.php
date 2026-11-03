<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Penjaga rute dashboard Tahap 1: peran dibaca dari session demo.
 *
 * TODO(Tahap 2): ganti dengan middleware auth, EnsureActiveAccount, EnsurePasswordChanged,
 * dan EnsureRole dari sumber.
 */
class EnsureDemoRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $current = $request->session()->get('demo_role');

        if (! $current) {
            return redirect()->route('login');
        }

        abort_unless($current === $role, 403);

        return $next($request);
    }
}
