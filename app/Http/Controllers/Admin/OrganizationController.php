<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\DataKlien;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
use App\Support\Data\Wilayah;
use App\Support\Data\DataLaporan;
use App\Support\Format;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'district' => (string) $request->query('district', ''),
            'village' => (string) $request->query('village', ''),
        ];
        $query = mb_strtolower(trim($filters['q']));
        $rows = DataLembaga::all()
            ->filter(fn (array $org) => (! $filters['district'] || $org['district'] === $filters['district'])
                && (! $filters['village'] || $org['village'] === $filters['village'])
                && (! $query || collect([$org['title'], $org['chair'], $org['contact']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->values();

        return view('admin.organizations.index', [
            'filters' => $filters,
            'districts' => Wilayah::districts(),
            'villages' => Wilayah::villages($filters['district'] ?: null),
            'organizations' => Halaman::make($rows, 10),
            'rosterOf' => fn (string $id) => DataPengurus::raw()->where('organizationId', $id)->values(),
        ]);
    }

    public function show(string $id): View
    {
        $organization = DataLembaga::find($id);
        abort_unless($organization, 404);

        $members = DataPengurus::forOrganization($id);

        return view('admin.organizations.show', [
            'admin' => true,
            'organization' => $organization,
            'members' => $members,
            'memberCount' => $members->count(),
            'counts' => DataKlien::summary(DataKlien::forOrganization($id)),
            'map' => Format::coordinates($organization['latitude'], $organization['longitude']),
            'reports' => DataLaporan::forOrganization($id)->where('status', '!=', 'Draf')->values(),
        ]);
    }

    public function member(string $id): View
    {
        $member = DataPengurus::find($id);
        abort_unless($member, 404);

        return view('admin.organizations.member', [
            'member' => $member,
            'organization' => DataLembaga::find($member['organizationId']),
        ]);
    }
}
