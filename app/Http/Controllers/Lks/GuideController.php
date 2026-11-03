<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\Data\DataLembaga;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function __invoke(): View
    {
        return view('shared.guide', ['eyebrow' => DataLembaga::own()['title']]);
    }
}
