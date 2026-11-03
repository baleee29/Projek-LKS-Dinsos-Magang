<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Data\DataKlien;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataLembaga;
use App\Support\Format;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View
    {
        $organization = DataLembaga::own();

        return view('lks.profile', [
            'admin' => false,
            'organization' => $organization,
            'memberCount' => DataPengurus::forOrganization($organization['id'])->count(),
            'counts' => DataKlien::summary(DataKlien::forOrganization($organization['id'])),
            'map' => Format::coordinates($organization['latitude'], $organization['longitude']),
        ]);
    }
}
