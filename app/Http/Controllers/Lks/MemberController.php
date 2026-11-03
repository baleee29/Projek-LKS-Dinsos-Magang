<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $organization = DemoOrganizations::own();
        $members = DemoMembers::forOrganization($organization['id']);
        $filters = ['q' => (string) $request->query('q', ''), 'category' => (string) $request->query('category', '')];
        $query = mb_strtolower(trim($filters['q']));

        $shown = $members->filter(fn (array $member) => (! $filters['category'] || $member['category'] === $filters['category'])
            && (! $query || collect([$member['title'], $member['nik'], $member['position'], $member['category']])
                ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values();

        $serviceStaff = $members->filter(fn (array $member) => in_array(mb_strtolower($member['category']), ['staf', 'tenaga layanan', 'relawan'], true))->count();
        $residents = DemoClients::summary(DemoClients::forOrganization($organization['id']))['residents'];

        return view('lks.members.index', [
            'organization' => $organization,
            'members' => $members,
            'shown' => $shown,
            'filters' => $filters,
            'categories' => ['Pengurus', 'Staf', 'Tenaga layanan', 'Relawan'],
            'ratio' => $serviceStaff ? '1:'.rtrim(rtrim(number_format($residents / $serviceStaff, 1, ',', '.'), '0'), ',') : '—',
        ]);
    }

    public function show(string $id): View
    {
        $member = $this->owned($id);

        return view('lks.members.show', [
            'member' => $member,
            'organization' => DemoOrganizations::own(),
        ]);
    }

    public function create(): View
    {
        return $this->form(null);
    }

    public function edit(string $id): View
    {
        return $this->form($this->owned($id));
    }

    /** @param  array<string, mixed>|null  $member */
    private function form(?array $member): View
    {
        return view('lks.members.form', [
            'member' => $member,
            'values' => $member ?? ['joined' => DemoClock::TODAY, 'category' => 'Tenaga layanan', 'position' => 'Anggota'],
            'today' => DemoClock::TODAY,
        ]);
    }

    /** @return array<string, mixed> */
    private function owned(string $id): array
    {
        $member = DemoMembers::find($id);
        abort_unless($member && $member['organizationId'] === DemoOrganizations::OWN_LKS, 404);

        return $member;
    }
}
