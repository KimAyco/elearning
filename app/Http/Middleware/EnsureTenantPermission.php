<?php

namespace App\Http\Middleware;

use App\Services\RbacService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantPermission
{
    public function __construct(
        private readonly RbacService $rbacService,
    ) {
    }

    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        $userId = $request->attributes->get('actor_user_id') ?? $request->session()->get('user_id');
        $schoolId = $request->attributes->get('active_school_id') ?? $request->session()->get('active_school_id');

        if (! is_int($userId) || ! is_int($schoolId)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tenant context is missing.'], 401);
            }

            return redirect('/login')->withErrors(['auth' => 'Tenant context is missing.']);
        }

        $permissionCodes = $request->attributes->get('permission_codes');
        if (is_array($permissionCodes) && in_array($permissionCode, $permissionCodes, true)) {
            return $next($request);
        }

        $hasPermission = $this->rbacService->hasPermission($userId, $schoolId, $permissionCode);
        if (! $hasPermission) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden: missing permission '.$permissionCode], 403);
            }

            return back()->withErrors(['permission' => 'Forbidden: missing permission '.$permissionCode]);
        }

        return $next($request);
    }
}
