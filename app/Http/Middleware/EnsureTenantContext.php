<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Models\User;
use App\Services\RbacService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function __construct(
        private readonly RbacService $rbacService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('user_id');
        $activeSchoolId = $request->session()->get('active_school_id');

        if (! is_int($userId) || ! is_int($activeSchoolId)) {
            return $this->unauthorized($request, 'Tenant session is missing.');
        }

        $school = School::query()
            ->where('id', $activeSchoolId)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return $this->unauthorized($request, 'School is not active.');
        }

        $user = User::query()
            ->where('id', $userId)
            ->first();

        if ($user === null) {
            return $this->unauthorized($request, 'User is not available.');
        }

        if ($user->status !== 'active') {
            return $this->unauthorized($request, 'User is not active.');
        }

        $roleCodes = $this->rbacService->roleCodesForUserInSchool($userId, $activeSchoolId);
        if ($roleCodes === []) {
            return $this->unauthorized($request, 'No active tenant role is assigned.');
        }
        $permissionCodes = $this->rbacService->permissionCodesForUserInSchool($userId, $activeSchoolId);

        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $request->attributes->set('actor_user_id', $userId);
        $request->attributes->set('actor_user_name', (string) ($user->full_name ?? ''));
        $request->attributes->set('active_school_id', $activeSchoolId);
        $request->attributes->set('role_codes', $roleCodes);
        $request->attributes->set('permission_codes', $permissionCodes);
        $request->attributes->set('pending_student_activation', false);

        // Bind session-authenticated tenant user into auth() for blade topbar identity display.
        Auth::setUser($user);

        $request->session()->put('actor_user_name', (string) ($user->full_name ?? ''));
        $request->session()->put('role_codes', $roleCodes);
        $request->session()->put('permission_codes', $permissionCodes);
        $request->session()->put('pending_student_activation', false);

        return $next($request);
    }

    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect('/login')->withErrors(['email' => $message]);
    }
}
