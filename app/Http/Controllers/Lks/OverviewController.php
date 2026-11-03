<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoReports;
use App\Support\Format;
use Illuminate\View\View;

class OverviewController extends Controller
{
    public function __invoke(): View
    {
        $organization = DemoOrganizations::own();
        $members = DemoMembers::forOrganization($organization['id']);
        $clients = DemoClients::forOrganization($organization['id']);
        $reports = DemoReports::forOrganization($organization['id']);
        $period = DemoClock::period();

        return view('lks.overview', [
            'organization' => $organization,
            'members' => $members,
            'clients' => $clients,
            'summary' => DemoClients::summary($clients),
            'reportCounts' => [
                'Draf' => $reports->where('status', 'Draf')->count(),
                'Terkirim' => $reports->where('status', 'Terkirim')->count(),
            ],
            'period' => $period,
            'currentReport' => $reports->first(fn (array $report) => (int) $report['year'] === $period['year'] && (int) $report['quarter'] === $period['quarter']),
            'memberCategories' => collect(['Pengurus', 'Staf', 'Tenaga layanan', 'Relawan'])
                ->mapWithKeys(fn (string $category) => [$category => $members->where('category', $category)->count()]),
            'map' => Format::coordinates($organization['latitude'], $organization['longitude']),
            'years' => [$period['year'], $period['year'] - 1, $period['year'] - 2],
        ]);
    }
}
