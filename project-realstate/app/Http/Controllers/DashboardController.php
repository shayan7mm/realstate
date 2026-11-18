<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isMaster()) {
            // آمار برای مستر
            $totalAccountants = User::where('role', 'accountant')->count();
            $activeAccountants = User::where('role', 'accountant')->where('is_active', true)->count();
            $inactiveAccountants = User::where('role', 'accountant')->where('is_active', false)->count();
            $recentUsers = User::where('role', 'accountant')->latest()->take(5)->get();
            
            return view('dashboard.master', compact(
                'totalAccountants', 
                'activeAccountants', 
                'inactiveAccountants',
                'recentUsers'
            ));
        }
        
        // ===== داشبورد حسابدار =====
        return view('dashboard.accountant', compact('user'));
    }
}