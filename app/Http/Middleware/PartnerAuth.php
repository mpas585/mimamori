<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerAuth
{
    /**
     * @param string|null $role  'master', 'operator', or null (any role)
     */
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        if (!Auth::guard('partner')->check()) {
            return redirect('/partner/login');
        }

        // role制限がある場合
        if ($role !== null) {
            $admin = Auth::guard('partner')->user();

            if ($admin->role !== $role) {
                // 権限違い → 自分の画面にリダイレクト
                if ($admin->role === 'master') {
                    return redirect('/partner');
                }
                return redirect('/partner/org');
            }
        }

        return $next($request);
    }
}


