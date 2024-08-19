<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function index()
    {
        try {
            $transactions_today = 0;
            $transactions_weekly = 0;
            $transactions_monthly = 0;
            $transactions_yearly = 0;

            $users_today = 0;
            $users_weekly = 0;
            $users_monthly = 0;
            $users_yearly = 0;

            $total_companies = 0;
            $total_users = 0;
            $total_packages = 0;
            $total_transactions = 0;

            return view('user.index', compact(
                'transactions_today',
                'transactions_weekly',
                'transactions_monthly',
                'transactions_yearly',
                'users_today',
                'users_weekly',
                'users_monthly',
                'users_yearly',
                'total_companies',
                'total_users',
                'total_packages',
                'total_transactions'

            ));
        } catch (\Exception $e) {
            return back();
        }
    }
}
