<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoLetters;
use App\Support\Demo\DemoOrganizations;
use App\Support\LetterListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('shared.letters', LetterListing::build(
            $request,
            DemoLetters::forLks(DemoOrganizations::OWN_LKS),
            admin: false,
        ));
    }
}
