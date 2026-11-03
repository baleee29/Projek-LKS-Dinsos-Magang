<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ClientRecap;
use App\Support\Data\DataKlien;
use App\Support\Data\DataLembaga;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OccupancyController extends Controller
{
    public function index(Request $request): View
    {
        return view('shared.clients.recap', ClientRecap::build(
            $request,
            DataKlien::all(),
            DataLembaga::raw(),
            admin: true,
            title: 'Seluruh LKS',
        ));
    }

    public function show(Request $request, string $id): View
    {
        $client = DataKlien::find($id);
        abort_unless($client, 404);

        return view('shared.clients.detail', ClientRecap::detail($request, $client, admin: true));
    }
}
