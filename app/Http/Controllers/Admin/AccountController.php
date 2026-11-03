<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Data\DataAkun;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.account', ['account' => DataAkun::admin()]);
    }
}
