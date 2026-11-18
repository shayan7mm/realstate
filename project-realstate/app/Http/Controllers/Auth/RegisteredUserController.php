<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // فقط مستر می‌تواند به صفحه ثبت‌نام دسترسی داشته باشد
        if (!Auth::check() || !Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }
        
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // فقط مستر می‌تواند ثبت‌نام کند
        if (!Auth::check() || !Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'accountant', // همیشه حسابدار ثبت می‌شود
            'is_active' => true, // به طور پیش‌فرض فعال است
        ]);

        event(new Registered($user));

        // بعد از ثبت‌نام، به صفحه مدیریت کاربران برمی‌گردیم
        return redirect()->route('master.users')->with('success', 'حسابدار با موفقیت ایجاد شد.');
    }
}