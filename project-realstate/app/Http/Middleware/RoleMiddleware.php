<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // اگر کاربر لاگین نکرده باشد
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ===== دیباگ =====
        // اگر کاربر مستر باشد، به همه چیز دسترسی دارد
        if ($user->role === 'master') {
            return $next($request);
        }

        // اگر نقش کاربر در لیست نقش‌های مجاز نباشد
        if (!in_array($user->role, $roles)) {
            abort(403, 'شما دسترسی به این بخش را ندارید. نقش شما: ' . $user->role);
        }

        return $next($request);
    }
}