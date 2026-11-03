<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoActivities;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPsmMembers;
use App\Support\Demo\DemoRegistrations;
use App\Support\Demo\DemoReplies;
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

        $lksOrgs = DemoOrganizations::byPillar('lks');
        $ktOrgs = DemoOrganizations::byPillar('karang-taruna');
        $psmOrgs = DemoOrganizations::byPillar('psm');
        $members = DemoMembers::all();
        $inLks = fn (array $member) => str_starts_with($member['organizationId'], 'lks-');
        $lksMembers = $members->filter($inLks);
        $ktMembers = $members->reject($inLks);
        $psmMembers = DemoPsmMembers::all();
        $clients = DemoClients::all();
        $summary = DemoClients::summary($clients);

        $allSubmitted = DemoReports::submittedLks();
        $reports = $allSubmitted->filter(fn (array $item) => (int) $item['year'] === $year && ((int) $item['quarter'] <= 2) === ($semester === 1))->values();
        $submittedOrgs = $reports->pluck('organizationId')->all();

        $queues = [
            'registrationsLKS' => DemoRegistrations::pending('lks'),
            'registrationsKT' => DemoRegistrations::pending('karang-taruna'),
            'incomingLetters' => DemoActivities::pending()->count(),
            'replyDrafts' => DemoReplies::all()->where('status', 'Draf')->count(),
        ];

        $activities = collect()
            ->merge(DemoRegistrations::all()->map(fn (array $row) => [
                'at' => $row['updated_at'], 'status' => DemoRegistrations::statusLabel($row['status']), 'pillar' => $row['pillar'],
                'title' => 'Pendaftaran '.($row['pillar'] === 'lks' ? 'LKS' : 'Karang Taruna'), 'organizationTitle' => $row['organization_name'],
                'note' => $row['notes'], 'actor' => $row['reviewed_at'] ? 'Admin' : 'Pendaftar', 'url' => '/admin/registration/'.$row['id'],
            ]))
            ->merge($allSubmitted->map(fn (array $row) => [
                'at' => $row['submittedAt'], 'status' => $row['status'], 'pillar' => 'lks',
                'title' => 'Laporan '.DemoReports::periodLabel($row), 'organizationTitle' => DemoOrganizations::find($row['organizationId'])['title'],
                'note' => '', 'actor' => '', 'url' => '/admin/report/'.$row['id'],
            ]))
            ->merge(DemoActivities::all()->map(fn (array $row) => [
                'at' => $row['updated'], 'status' => $row['status'], 'pillar' => 'karang-taruna',
                'title' => $row['title'], 'organizationTitle' => DemoOrganizations::find($row['organizationId'])['title'],
                'note' => $row['reviewNote'], 'actor' => '', 'url' => '/admin/incoming-letter/'.$row['id'],
            ]))
            ->sortByDesc('at')
            ->values();

        return view('admin.overview', [
            'year' => $year,
            'semester' => $semester,
            'years' => [DemoClock::YEAR, DemoClock::YEAR - 1, DemoClock::YEAR - 2],
            'period' => $period,
            'organizationCount' => $lksOrgs->count() + $ktOrgs->count() + $psmOrgs->count(),
            'pillarCounts' => ['psm' => $psmOrgs->count(), 'lks' => $lksOrgs->count(), 'karang-taruna' => $ktOrgs->count()],
            'memberCount' => $members->count() + $psmMembers->count(),
            'clientCount' => $clients->count(),
            'inhabitants' => $summary['residents'],
            'reportCount' => $reports->count(),
            'queues' => $queues,
            'pendingWork' => array_sum($queues),
            'lksOrgCount' => $lksOrgs->count(),
            'lksMemberCount' => $lksMembers->count(),
            'reportStatusCounts' => ['Terkirim' => $reports->count()],
            'psmOrgCount' => $psmOrgs->count(),
            'psmMemberCount' => $psmMembers->count(),
            'ktOrgCount' => $ktOrgs->count(),
            'ktOfficers' => $ktMembers->where('category', 'Pengurus')->count(),
            'ktParticipating' => $ktMembers->where('category', 'Anggota berkegiatan')->count(),
            'ktMemberCount' => $ktMembers->count(),
            'reports' => $reports,
            'totalFiles' => $reports->sum(fn (array $item) => count($item['attachments'])),
            'previews' => $reports->sortByDesc('submittedAt')->take(3)->values(),
            'unsubmitted' => $lksOrgs->reject(fn (array $org) => in_array($org['id'], $submittedOrgs, true))->values(),
            'activities' => $activities,
            'organizationOf' => fn (string $id) => DemoOrganizations::find($id),
            'reportUrl' => '/admin/reports/lks?year='.$year.'&quarter='.$semester,
        ]);
    }
}
