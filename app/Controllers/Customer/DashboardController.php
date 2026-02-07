<?php

namespace App\Controllers\Customer;

class DashboardController
{
    public static function index()
    {
        if (empty($_SESSION['customer'])) {
            return redirect('/account/login');
        }

        return view('account/dashboard', [
            'customer' => $_SESSION['customer'],
        ]);
    }
}
