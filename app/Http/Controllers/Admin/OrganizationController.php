<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPaginator;
use App\Support\Demo\DemoRegions;
use App\Support\Demo\DemoReports;
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
        $rows = DemoOrganizations::all()
            ->filter(fn (array $org) => (! $filters['district'] || $org['district'] === $filters['district'])
                && (! $filters['village'] || $org['village'] === $filters['village'])
                && (! $query || collect([$org['title'], $org['chair'], $org['contact']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->values();

        return view('admin.organizations.index', [
            'filters' => $filters,
            'districts' => DemoRegions::districts(),
            'villages' => DemoRegions::villages($filters['district'] ?: null),
            'organizations' => DemoPaginator::make($rows, 10),
            'rosterOf' => fn (string $id) => DemoMembers::raw()->where('organizationId', $id)->values(),
        ]);
    }

    public function show(string $id): View
    {
        $organization = DemoOrganizations::find($id);
        abort_unless($organization, 404);

        $members = DemoMembers::forOrganization($id);

        return view('admin.organizations.show', [
            'admin' => true,
            'organization' => $organization,
            'members' => $members,
            'memberCount' => $members->count(),
            'counts' => DemoClients::summary(DemoClients::forOrganization($id)),
            'map' => Format::coordinates($organization['latitude'], $organization['longitude']),
            'reports' => DemoReports::forOrganization($id)->where('status', '!=', 'Draf')->values(),
        ]);
    }

    public function member(string $id): View
    {
        $member = DemoMembers::find($id);
        abort_unless($member, 404);

        return view('admin.organizations.member', [
            'member' => $member,
            'organization' => DemoOrganizations::find($member['organizationId']),
        ]);
    }
}
