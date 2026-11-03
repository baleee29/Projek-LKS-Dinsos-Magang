<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ClientRecap;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoOrganizations;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OccupancyController extends Controller
{
    public function index(Request $request): View
    {
        return view('shared.clients.recap', ClientRecap::build(
            $request,
            DemoClients::all(),
            DemoOrganizations::raw()->where('pillar', 'lks')->values(),
            admin: true,
            title: 'Seluruh LKS',
        ));
    }

    public function show(Request $request, string $id): View
    {
        $client = DemoClients::find($id);
        abort_unless($client, 404);

        return view('shared.clients.detail', ClientRecap::detail($request, $client, admin: true));
    }
}
