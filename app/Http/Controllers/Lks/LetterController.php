<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Data\DataSurat;
use App\Support\Data\DataLembaga;
use App\Support\LetterListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('shared.letters', LetterListing::build(
            $request,
            DataSurat::forLks(DataLembaga::ownId()),
            admin: false,
        ));
    }
}
