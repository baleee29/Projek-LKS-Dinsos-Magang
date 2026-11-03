<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoLetters;
use App\Support\LetterListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('shared.letters', LetterListing::build($request, DemoLetters::all(), admin: true));
    }
}
