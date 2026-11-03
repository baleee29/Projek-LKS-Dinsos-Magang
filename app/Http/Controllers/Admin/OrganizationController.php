<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoReports;
use App\Support\Format;
use App\Support\PartnerDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request, string $pillar): View
    {
        return view('admin.organizations.index', PartnerDirectory::organizations($request, $pillar));
    }

    public function show(Request $request, string $id): View
    {
        $organization = DemoOrganizations::find($id);
        abort_unless($organization && in_array($organization['pillar'], ['lks', 'karang-taruna'], true), 404);

        $members = DemoMembers::forOrganization($id);
        $map = Format::coordinates($organization['latitude'], $organization['longitude']);

        if ($organization['pillar'] === 'lks') {
            return view('admin.organizations.show-lks', [
                'admin' => true,
                'organization' => $organization,
                'members' => $members,
                'memberCount' => $members->count(),
                'counts' => DemoClients::summary(DemoClients::forOrganization($id)),
                'map' => $map,
                'reports' => DemoReports::forOrganization($id)->where('status', '!=', 'Draf')->values(),
            ]);
        }

        $filters = ['q' => (string) $request->query('q', ''), 'category' => (string) $request->query('category', '')];
        $query = mb_strtolower(trim($filters['q']));

        return view('admin.organizations.show-kt', [
            'organization' => $organization,
            'members' => $members,
            'shown' => $members->filter(fn (array $member) => (! $filters['category'] || $member['category'] === $filters['category'])
                && (! $query || collect([$member['title'], $member['position'], $member['category']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values(),
            'filters' => $filters,
            'categories' => $members->pluck('category')->unique()->values(),
            'counts' => $members->countBy('category'),
            'map' => $map,
            'reports' => DemoReports::forOrganization($id)->values(),
        ]);
    }
}
