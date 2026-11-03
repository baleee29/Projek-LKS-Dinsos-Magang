<?php

namespace App\View\Composers;

use App\Support\Demo\DemoShell;
use App\Support\Demo\DemoState;
use App\Support\Navigation;
use Illuminate\View\View;

/**
 * Data kerangka dashboard (identitas, lencana, menu aktif) untuk layout dan halaman error.
 */
class DashboardComposer
{
    public function compose(View $view): void
    {
        $role = session('demo_role');

        if (! $role) {
            return;
        }

        $path = request()->path();
        $workspace = str_starts_with($path, $role.'/') ? substr($path, strlen($role) + 1) : $path;

        $view->with([
            'role' => $role,
            'identity' => DemoShell::identity($role),
            'counts' => DemoShell::counts($role),
            'navigation' => new Navigation($role, $workspace, request()->query()),
            'demoError' => DemoState::isError(),
        ]);
    }
}
