<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoAccounts;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoLetters;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoRegistrations;
use App\Support\Demo\DemoReports;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OverviewController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Admin memilih tahun dan semester. Bawaan sumber: periode terakhir yang sudah berakhir.
        $year = (int) $request->query('year', DemoClock::YEAR);
        $semester = (int) $request->query('quarter', 1) === 2 ? 2 : 1;
        $period = DemoClock::period($year, $semester === 1 ? 1 : 3);

        $organizations = DemoOrganizations::all();
        $clients = DemoClients::all();
        $allSubmitted = DemoReports::submitted();
        $reports = $allSubmitted->filter(fn (array $item) => (int) $item['year'] === $year && ((int) $item['quarter'] <= 2) === ($semester === 1))->values();
        $submittedOrgs = $reports->pluck('organizationId')->all();
        $pendingRegistrations = DemoRegistrations::pending();
        $unreadLetters = DemoLetters::unreadForAdmin();

        $activities = collect()
            ->merge(DemoRegistrations::all()->map(fn (array $row) => [
                'at' => $row['updated_at'], 'status' => DemoRegistrations::statusLabel($row['status']),
                'title' => 'Pendaftaran LKS', 'organizationTitle' => $row['organization_name'],
                'note' => $row['notes'], 'actor' => $row['reviewed_at'] ? 'Admin' : 'Pendaftar', 'url' => '/admin/registration/'.$row['id'],
            ]))
            ->merge($allSubmitted->map(fn (array $row) => [
                'at' => $row['submittedAt'], 'status' => $row['status'],
                'title' => 'Laporan '.DemoReports::periodLabel($row), 'organizationTitle' => DemoOrganizations::find($row['organizationId'])['title'],
                'note' => '', 'actor' => '', 'url' => '/admin/report/'.$row['id'],
            ]))
            ->sortByDesc('at')
            ->values();

        return view('admin.overview', [
            'year' => $year,
            'semester' => $semester,
            'years' => [DemoClock::YEAR, DemoClock::YEAR - 1, DemoClock::YEAR - 2],
            'period' => $period,
            'organizationCount' => $organizations->count(),
            'memberCount' => DemoMembers::all()->count(),
            'clientCount' => $clients->count(),
            'clientSummary' => DemoClients::summary($clients),
            'reportCount' => $reports->count(),
            'pendingRegistrations' => $pendingRegistrations,
            'unreadLetters' => $unreadLetters,
            'pendingWork' => $pendingRegistrations + $unreadLetters,
            'accountCount' => DemoAccounts::all()->count(),
            'reports' => $reports,
            'totalFiles' => $reports->sum(fn (array $item) => count($item['attachments'])),
            'previews' => $reports->sortByDesc('submittedAt')->take(3)->values(),
            'unsubmitted' => $organizations->reject(fn (array $org) => in_array($org['id'], $submittedOrgs, true))->values(),
            'activities' => $activities,
            'organizationOf' => fn (string $id) => DemoOrganizations::find($id),
            'reportUrl' => '/admin/reports/lks?year='.$year.'&quarter='.$semester,
        ]);
    }
}
