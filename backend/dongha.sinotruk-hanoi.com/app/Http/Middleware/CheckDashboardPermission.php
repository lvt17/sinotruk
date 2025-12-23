<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckDashboardPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập tính năng này!');
    }
}
