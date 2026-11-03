<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Data\Kalender;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
use App\Support\Data\DataLaporan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const PER_PAGE = 5;

    public function index(Request $request): View
    {
        return $this->listing($request, '');
    }

    public function drafts(Request $request): View
    {
        return $this->listing($request, 'Draf');
    }

    public function show(string $id): View
    {
        $report = $this->owned($id);

        return view('shared.reports.detail', [
            'admin' => false,
            'report' => $report,
            'organization' => DataLembaga::own(),
            'period' => Kalender::period((int) $report['year'], (int) $report['quarter']),
        ]);
    }

    public function edit(string $id): View
    {
        $report = $this->owned($id);

        return view('lks.reports.editor', [
            'report' => $report,
            'organization' => DataLembaga::own(),
            'period' => Kalender::period((int) $report['year'], (int) $report['quarter']),
        ]);
    }

    private function listing(Request $request, string $forcedStatus): View
    {
        $organization = DataLembaga::own();
        $all = DataLaporan::forOrganization($organization['id']);
        $filters = [
            'q' => (string) $request->query('q', ''),
            'year' => (string) $request->query('year', ''),
            'quarter' => (string) $request->query('quarter', ''),
            'status' => (string) $request->query('status', ''),
        ];

        $yearScope = $all->filter(fn (array $item) => ! $filters['year'] || (string) $item['year'] === $filters['year']);
        $query = mb_strtolower(trim($filters['q']));
        $status = $forcedStatus ?: $filters['status'];
        // Sumber menyimpan semester 2 sebagai quarter 3 atau 4; filter 2 berarti Juli–Desember.
        $semester = fn (array $item) => (int) $item['quarter'] <= 2 ? '1' : '2';

        $filtered = $all
            ->filter(fn (array $item) => (! $filters['year'] || (string) $item['year'] === $filters['year'])
                && (! $filters['quarter'] || $semester($item) === $filters['quarter'])
                && (! $status || $item['status'] === $status)
                && (! $query || collect([$item['title'], $item['id'], $item['summary']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->sort(fn (array $a, array $b) => [$b['year'], $b['quarter']] <=> [$a['year'], $a['quarter']])
            ->values();

        $calendarYear = (int) $filters['year'] ?: Kalender::year();

        return view('lks.reports.index', [
            'organization' => $organization,
            'forcedStatus' => $forcedStatus,
            'pageTitle' => $forcedStatus ? 'Draf Laporan Semester' : 'Daftar Laporan Semester',
            'description' => $forcedStatus
                ? 'Lanjutkan draf, periksa kelengkapan, lalu kirim laporan semester ke Admin.'
                : 'Kelola dan kirim laporan semester langsung ke Admin tanpa proses persetujuan.',
            'filters' => $filters,
            'years' => $all->pluck('year')->push(Kalender::year())->unique()->sortDesc()->values(),
            'statuses' => DataLaporan::ACTIVE_STATUSES,
            'metrics' => [
                ['Total Laporan', $yearScope->count(), 'Riwayat periode yang tercatat', 'file', ''],
                ['Terkirim', $yearScope->where('status', 'Terkirim')->count(), 'Laporan sudah masuk ke Admin', 'shield', 'approved'],
                ['Perubahan Belum Dikirim', $yearScope->where('hasDraftChanges', true)->count(), 'Draf pembaruan milik lembaga', 'info', 'revision'],
                ['Draf Berjalan', $yearScope->where('status', 'Draf')->count(), 'Isian belum dikirim', 'edit', 'draft'],
            ],
            'reports' => Halaman::make($filtered, self::PER_PAGE),
            'calendarYear' => $calendarYear,
            'semesters' => collect([1, 3])->map(fn (int $quarter) => [
                'number' => $quarter === 1 ? 1 : 2,
                'period' => Kalender::period($calendarYear, $quarter),
                'existing' => $all->first(fn (array $item) => (int) $item['year'] === $calendarYear && ((int) $item['quarter'] <= 2) === ($quarter === 1)),
            ]),
            'today' => Kalender::today(),
            'dialogYears' => [Kalender::year(), Kalender::year() - 1, Kalender::year() - 2],
        ]);
    }

    /** @return array<string, mixed> */
    private function owned(string $id): array
    {
        $report = DataLaporan::find($id);
        abort_unless($report && $report['organizationId'] === DataLembaga::ownId(), 404);

        return $report;
    }
}
