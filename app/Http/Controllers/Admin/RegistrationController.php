<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPaginator;
use App\Support\Demo\DemoRegions;
use App\Support\Demo\DemoRegistrations;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'status' => (string) $request->query('status', ''),
            'district' => (string) $request->query('district', ''),
        ];
        $query = mb_strtolower(trim($filters['q']));

        $rows = DemoRegistrations::all()->sortByDesc('created_at')->filter(fn (array $row) => (! $filters['status'] || $row['status'] === $filters['status'])
            && (! $filters['district'] || $row['district'] === $filters['district'])
            && (! $query || collect([$row['organization_name'], $row['leader_name'], $row['whatsapp']])
                ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values();

        return view('admin.registrations.index', [
            'filters' => $filters,
            'statuses' => ['Menunggu Verifikasi', 'Disetujui', 'Ditolak'],
            'districts' => DemoRegions::districts(),
            'rows' => DemoPaginator::make($rows, 10),
        ]);
    }

    public function show(string $id): View
    {
        $row = DemoRegistrations::find($id);
        abort_unless($row, 404);

        return view('admin.registrations.show', [
            'row' => $row,
            'organizationId' => $this->organizationId($row),
            'whatsappUrl' => 'https://wa.me/62'.ltrim($row['whatsapp'], '0'),
        ]);
    }

    /** @param  array<string, mixed>  $row */
    private function organizationId(array $row): ?string
    {
        return DemoOrganizations::raw()->firstWhere('registrationId', $row['id'])['id'] ?? null;
    }
}
