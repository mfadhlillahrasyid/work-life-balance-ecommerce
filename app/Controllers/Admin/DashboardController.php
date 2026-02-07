<?php

namespace App\Controllers\Admin;

class DashboardController
{
    public static function index()
    {
        admin_auth();

        return view('admin/dashboard');
    }
}
