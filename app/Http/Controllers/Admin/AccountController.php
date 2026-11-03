<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoAdminAccount;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.account', ['account' => DemoAdminAccount::get()]);
    }
}
