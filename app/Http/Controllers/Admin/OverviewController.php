<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\DataAkun;
use App\Support\Data\DataKlien;
use App\Support\Data\Kalender;
use App\Support\Data\DataSurat;
use App\Support\Data\DataPengurus;
use App\Support\Data\DataLembaga;
use App\Support\Data\DataPendaftaran;
use App\Support\Data\DataLaporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OverviewController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Admin memilih tahun dan semester. Bawaan sumber: periode terakhir yang sudah berakhir.
        $year = (int) $request->query('year', Kalender::year());
        $semester = (int) $request->query('quarter', 1) === 2 ? 2 : 1;
        $period = Kalender::period($year, $semester === 1 ? 1 : 3);

        $organizations = DataLembaga::all();
        $clients = DataKlien::all();
        $allSubmitted = DataLaporan::submitted();
        $reports = $allSubmitted->filter(fn (array $item) => (int) $item['year'] === $year && ((int) $item['quarter'] <= 2) === ($semester === 1))->values();
        $submittedOrgs = $reports->pluck('organizationId')->all();
        $pendingRegistrations = DataPendaftaran::pending();
        $unreadLetters = DataSurat::unreadForAdmin();

        $activities = collect()
            ->merge(DataPendaftaran::all()->map(fn (array $row) => [
                'at' => $row['updated_at'], 'status' => DataPendaftaran::statusLabel($row['status']),
                'title' => 'Pendaftaran LKS', 'organizationTitle' => $row['organization_name'],
                'note' => $row['notes'], 'actor' => $row['reviewed_at'] ? 'Admin' : 'Pendaftar', 'url' => '/admin/registration/'.$row['id'],
            ]))
            ->merge($allSubmitted->map(fn (array $row) => [
                'at' => $row['submittedAt'], 'status' => $row['status'],
                'title' => 'Laporan '.DataLaporan::periodLabel($row), 'organizationTitle' => DataLembaga::find($row['organizationId'])['title'],
                'note' => '', 'actor' => '', 'url' => '/admin/report/'.$row['id'],
            ]))
            ->sortByDesc('at')
            ->values();

        return view('admin.overview', [
            'year' => $year,
            'semester' => $semester,
            'years' => [Kalender::year(), Kalender::year() - 1, Kalender::year() - 2],
            'period' => $period,
            'organizationCount' => $organizations->count(),
            'memberCount' => DataPengurus::all()->count(),
            'clientCount' => $clients->count(),
            'clientSummary' => DataKlien::summary($clients),
            'reportCount' => $reports->count(),
            'pendingRegistrations' => $pendingRegistrations,
            'unreadLetters' => $unreadLetters,
            'pendingWork' => $pendingRegistrations + $unreadLetters,
            'accountCount' => DataAkun::all()->count(),
            'reports' => $reports,
            'totalFiles' => $reports->sum(fn (array $item) => count($item['attachments'])),
            'previews' => $reports->sortByDesc('submittedAt')->take(3)->values(),
            'unsubmitted' => $organizations->reject(fn (array $org) => in_array($org['id'], $submittedOrgs, true))->values(),
            'activities' => $activities,
            'organizationOf' => fn (string $id) => DataLembaga::find($id),
            'reportUrl' => '/admin/reports/lks?year='.$year.'&quarter='.$semester,
        ]);
    }
}
