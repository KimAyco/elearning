<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $superAdminId = $request->session()->get('super_admin_id');
        if (! is_int($superAdminId)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated super admin.'], 401);
            }

            return redirect('/superadmin/login');
        }

        $request->attributes->set('actor_super_admin_id', $superAdminId);

        return $next($request);
    }
}

