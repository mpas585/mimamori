<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * @param string|null $role  'master', 'operator', or null (any role)
     */
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        // role制限がある場合
        if ($role !== null) {
            $admin = Auth::guard('admin')->user();

            if ($admin->role !== $role) {
                // 権限違い → 自分の画面にリダイレクト
                if ($admin->role === 'master') {
                    return redirect('/admin');
                }
                return redirect('/admin/org');
            }
        }

        return $next($request);
    }
}
