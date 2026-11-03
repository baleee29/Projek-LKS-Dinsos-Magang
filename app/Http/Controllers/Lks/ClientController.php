<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\ClientRecap;
use App\Support\Data\SkemaKlien;
use App\Support\Data\DataKlien;
use App\Support\Data\Kalender;
use App\Support\Data\DataLembaga;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function occupancy(Request $request): View
    {
        $organization = DataLembaga::own();

        return view('shared.clients.recap', ClientRecap::build(
            $request,
            DataKlien::forOrganization($organization['id']),
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
            'data' => $item ?? SkemaKlien::blank(),
            'fields' => SkemaKlien::fields(),
            'sections' => SkemaKlien::formSections(),
            'organization' => DataLembaga::own(),
            'today' => Kalender::today(),
        ]);
    }

    /** @return array<string, mixed> */
    private function owned(string $id): array
    {
        $client = DataKlien::find($id);
        abort_unless($client && $client['organizationId'] === DataLembaga::ownId(), 404);

        return $client;
    }
}
