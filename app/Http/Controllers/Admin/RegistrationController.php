<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
use App\Support\KeamananAkun;
use App\Support\Data\DataPendaftaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'status' => (string) $request->query('status', ''),
        ];
        $query = mb_strtolower(trim($filters['q']));

        $rows = DataPendaftaran::all()->sortByDesc('created_at')->filter(fn (array $row) => (! $filters['status'] || $row['status'] === $filters['status'])
            && (! $query || collect([$row['organization_name'], $row['leader_name'], $row['whatsapp']])
                ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values();

        return view('admin.registrations.index', [
            'filters' => $filters,
            'statuses' => ['Menunggu Verifikasi', 'Disetujui', 'Ditolak'],
            'rows' => Halaman::make($rows, 10),
        ]);
    }

    public function show(string $id): View
    {
        $row = DataPendaftaran::find($id);
        abort_unless($row, 404);

        return view('admin.registrations.show', [
            'row' => $row,
            'organizationId' => $row['organization_id'],
            'whatsappUrl' => KeamananAkun::tautanWhatsapp($row['whatsapp'], $row['status'] === 'Ditolak' ? "Yth. {$row['leader_name']},

Pendaftaran {$row['organization_name']} belum dapat kami terima.
Alasan: {$row['notes']}

Silakan kirim pendaftaran baru setelah diperbaiki.

Dinas Sosial Kota Banjarbaru" : ''),
        ]);
    }
}
