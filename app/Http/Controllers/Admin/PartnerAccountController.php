<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\DataAkun;
use App\Support\Data\DataPendaftaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerAccountController extends Controller
{
    public function __invoke(Request $request): View
    {
        $registrations = DataPendaftaran::all()->where('status', 'Disetujui')->values();

        return view('admin.partner-accounts', [
            'accounts' => DataAkun::all(),
            'registrations' => $registrations,
            'preselected' => $registrations->firstWhere('id', $request->query('from_reg')),
        ]);
    }
}
