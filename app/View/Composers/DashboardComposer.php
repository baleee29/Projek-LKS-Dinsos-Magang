<?php

namespace App\View\Composers;

use App\Support\Data\DataRangka;
use App\Support\Navigation;
use Illuminate\View\View;

/**
 * Data kerangka dashboard (identitas, lencana, menu aktif) untuk layout dan halaman error.
 */
class DashboardComposer
{
    public function compose(View $view): void
    {
        $akun = auth()->user();
        if (! $akun) {
            return;
        }
        $role = $akun->peran;
        $path = request()->path();
        $workspace = str_starts_with($path, $role.'/') ? substr($path, strlen($role) + 1) : $path;

        $view->with([
            'role' => $role,
            'identity' => DataRangka::identity($akun),
            'counts' => DataRangka::counts($akun),
            'navigation' => new Navigation($role, $workspace, request()->query()),
        ]);
    }
}
