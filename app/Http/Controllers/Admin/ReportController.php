<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\Kalender;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
use App\Support\Data\Wilayah;
use App\Support\Data\DataLaporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'district' => (string) $request->query('district', ''),
            'village' => (string) $request->query('village', ''),
            'status' => (string) $request->query('status', ''),
            'year' => (string) $request->query('year', 'all'),
            'quarter' => (string) $request->query('quarter', 'all'),
        ];
        $query = mb_strtolower(trim($filters['q']));
        $period = $filters['year'] !== 'all' && $filters['quarter'] !== 'all'
            ? Kalender::period((int) $filters['year'], $filters['quarter'] === '1' ? 1 : 3)
            : null;

        $reports = DataLaporan::submitted()->filter(function (array $item) use ($filters, $query) {
            $org = DataLembaga::find($item['organizationId']);

            return ($filters['year'] === 'all' || (string) $item['year'] === $filters['year'])
                && ($filters['quarter'] === 'all' || ((int) $item['quarter'] <= 2 ? '1' : '2') === $filters['quarter'])
                && (! $filters['district'] || $org['district'] === $filters['district'])
                && (! $filters['village'] || $org['village'] === $filters['village'])
                && (! $filters['status'] || $item['status'] === $filters['status'])
                && (! $query || collect([$org['title'], $item['id'], $item['title']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query)));
        })->sortByDesc('submittedAt')->values();

        return view('admin.reports.index', [
            'filters' => $filters,
            'period' => $period,
            'years' => [Kalender::year(), Kalender::year() - 1, Kalender::year() - 2],
            'districts' => Wilayah::districts(),
            'villages' => Wilayah::villages($filters['district'] ?: null),
            'reports' => Halaman::make($reports, 10),
            'organizationOf' => fn (string $id) => DataLembaga::find($id),
        ]);
    }

    public function show(string $id): View
    {
        // Admin tidak boleh membaca draf, sama seperti detail() di shared/reports.js.
        $report = DataLaporan::find($id);
        abort_unless($report && $report['status'] !== 'Draf', 404);

        return view('shared.reports.detail', [
            'admin' => true,
            'report' => $report,
            'organization' => DataLembaga::find($report['organizationId']),
            'period' => Kalender::period((int) $report['year'], (int) $report['quarter']),
        ]);
    }
}
