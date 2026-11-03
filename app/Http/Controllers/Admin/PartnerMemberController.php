<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPsmMembers;
use App\Support\PartnerDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerMemberController extends Controller
{
    public function psm(Request $request): View
    {
        return view('admin.psm.members', PartnerDirectory::psmMembers($request));
    }

    /** Detail personel: tampilannya mengikuti pilar pemiliknya, sama seperti memberDetail() sumber. */
    public function show(string $id): View
    {
        $psm = DemoPsmMembers::find($id);
        if ($psm) {
            return view('admin.psm.member', [
                'item' => $psm,
                'organization' => DemoOrganizations::find($psm['organizationId']),
            ]);
        }

        $member = DemoMembers::find($id);
        abort_unless($member, 404);
        $organization = DemoOrganizations::find($member['organizationId']);

        return view($organization['pillar'] === 'karang-taruna' ? 'admin.organizations.member-kt' : 'admin.organizations.member-lks', [
            'member' => $member,
            'organization' => $organization,
        ]);
    }
}
