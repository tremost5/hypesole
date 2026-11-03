<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'customerCount' => Customer::count(),
            'staffCount' => User::count(),
            'activeStaffCount' => User::where('is_active', true)->count(),
            'roleCount' => Role::count(),
        ]);
    }
}
