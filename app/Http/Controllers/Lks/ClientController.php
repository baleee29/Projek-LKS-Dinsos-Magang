<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\ClientRecap;
use App\Support\Demo\DemoClientSchema;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoOrganizations;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function occupancy(Request $request): View
    {
        $organization = DemoOrganizations::own();

        return view('shared.clients.recap', ClientRecap::build(
            $request,
            DemoClients::forOrganization($organization['id']),
            collect([$organization]),
            admin: false,
            title: $organization['title'],
        ));
    }

    public function show(Request $request, string $id): View
    {
        return view('shared.clients.detail', ClientRecap::detail($request, $this->owned($id), admin: false));
    }

    public function create(): View
    {
        return $this->form(null);
    }

    public function edit(string $id): View
    {
        return $this->form($this->owned($id));
    }

    /** @param  array<string, mixed>|null  $item */
    private function form(?array $item): View
    {
        return view('lks.clients.form', [
            'item' => $item,
            'data' => $item ?? DemoClientSchema::blank(),
            'fields' => DemoClientSchema::fields(),
            'sections' => DemoClientSchema::formSections(),
            'organization' => DemoOrganizations::own(),
            'today' => DemoClock::TODAY,
        ]);
    }

    /** @return array<string, mixed> */
    private function owned(string $id): array
    {
        $client = DemoClients::find($id);
        abort_unless($client && $client['organizationId'] === DemoOrganizations::OWN_LKS, 404);

        return $client;
    }
}
