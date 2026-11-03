<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoComplaints;
use App\Support\Demo\DemoPaginator;
use App\Support\Demo\DemoRegions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function __invoke(Request $request): View
    {
        // ?complaint={id} membuka detail di halaman yang sama, seperti openDetail() di admin/psm-complaints.js.
        if ($request->filled('complaint')) {
            $row = DemoComplaints::find((string) $request->query('complaint'));
            abort_unless($row, 404);

            return view('admin.psm.complaint', [
                'row' => $row,
                'backUrl' => $request->fullUrlWithQuery(['complaint' => null]),
            ]);
        }

        $filters = [
            'q' => (string) $request->query('q', ''),
            'district' => (string) $request->query('district', ''),
            'village' => (string) $request->query('village', ''),
        ];
        $query = mb_strtolower(trim($filters['q']));
        $all = DemoComplaints::all();
        $rows = $all->filter(fn (array $row) => (! $filters['district'] || $row['district'] === $filters['district'])
            && (! $filters['village'] || $row['village'] === $filters['village'])
            && (! $query || collect([$row['complaint_number'], $row['member_name'], $row['client_name'], $row['subject']])
                ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values();

        return view('admin.psm.complaints', [
            'filters' => $filters,
            'districts' => DemoRegions::districts(),
            'villages' => $filters['district'] ? DemoRegions::villages($filters['district']) : [],
            'complaints' => DemoPaginator::make($rows, 10),
            'metrics' => [
                ['Total Pengaduan', 'Seluruh pengaduan masuk', 'file', 'amber', $all->count()],
                ['Pengaduan Bulan Ini', 'Periode berjalan · WITA', 'calendar', 'teal', $all->filter(fn (array $row) => str_starts_with($row['created_at'], substr(DemoClock::TODAY, 0, 7)))->count()],
                ['Anggota Mengadu', 'Anggota yang melapor', 'users', 'indigo', $all->pluck('member_id')->unique()->count()],
                ['Klien / PPKS Terdata', 'Pengaduan dengan data klien', 'heart', 'amber', $all->where('client_name', '!=', '')->count()],
            ],
        ]);
    }
}
