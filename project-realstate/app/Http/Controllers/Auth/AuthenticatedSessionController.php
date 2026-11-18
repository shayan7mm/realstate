<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        // اعتبارسنجی
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // تشخیص نوع ورود (ایمیل یا موبایل)
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        // تلاش برای ورود
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // بررسی فعال بودن کاربر
            $user = Auth::user();
            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'حساب کاربری شما غیرفعال شده است.',
                ]);
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors([
            'login' => 'اطلاعات وارد شده صحیح نیست.',
        ])->onlyInput('login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}