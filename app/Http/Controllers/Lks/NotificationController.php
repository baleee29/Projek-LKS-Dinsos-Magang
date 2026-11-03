<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Support\NotificationListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('shared.notifications.index', NotificationListing::build($request, 'lks'));
    }

    public function show(string $id): View
    {
        return view('shared.notifications.detail', NotificationListing::detail($id, 'lks'));
    }
}
