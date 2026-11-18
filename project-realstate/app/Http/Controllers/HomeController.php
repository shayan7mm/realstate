<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class HomeController extends Controller
{
     public function user()
    {
        return view('index');
    }

     public function dashboard()
    {
        return view('dashboard.dashboard');
    }

        public function logout()
    {
        Auth::logout();
        return redirect(route('user'));
    }
}
