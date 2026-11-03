<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Format;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View
    {
        $organization = DemoOrganizations::own();

        return view('lks.profile', [
            'admin' => false,
            'organization' => $organization,
            'memberCount' => DemoMembers::forOrganization($organization['id'])->count(),
            'counts' => DemoClients::summary(DemoClients::forOrganization($organization['id'])),
            'map' => Format::coordinates($organization['latitude'], $organization['longitude']),
        ]);
    }
}
