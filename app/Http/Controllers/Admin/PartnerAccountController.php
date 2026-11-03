<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoAccounts;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoRegistrations;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerAccountController extends Controller
{
    public function __invoke(Request $request): View
    {
        $registrations = DemoRegistrations::all()->where('status', 'Disetujui')->values();

        return view('admin.partner-accounts', [
            'accounts' => DemoAccounts::all(),
            'registrations' => $registrations,
            'preselected' => $registrations->firstWhere('id', $request->query('from_reg')),
            'psmOrganizations' => DemoOrganizations::byPillar('psm'),
            'roleBadges' => DemoAccounts::ROLE_BADGES,
        ]);
    }
}
