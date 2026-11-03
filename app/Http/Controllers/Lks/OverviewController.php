<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Data\DataKlien;
use App\Support\Data\Kalender;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataLembaga;
use App\Support\Data\DataLaporan;
use App\Support\Format;
use Illuminate\View\View;

class OverviewController extends Controller
{
    public function __invoke(): View
    {
        $organization = DataLembaga::own();
        $members = DataPengurus::forOrganization($organization['id']);
        $clients = DataKlien::forOrganization($organization['id']);
        $reports = DataLaporan::forOrganization($organization['id']);
        $period = Kalender::period();

        return view('lks.overview', [
            'organization' => $organization,
            'members' => $members,
            'clients' => $clients,
            'summary' => DataKlien::summary($clients),
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
