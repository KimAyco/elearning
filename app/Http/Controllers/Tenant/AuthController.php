<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\RbacService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly RbacService $rbacService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function showLoginForm(): View
    {
        return view('tenant.login');
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $payload = $request->validate([
            'school_code' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $school = School::query()
            ->where('school_code', $payload['school_code'])
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return back()->withErrors(['school_code' => 'School is not available for tenant login.'])->withInput();
        }

        $user = User::query()
            ->where('email', $payload['email'])
            ->first();

        if ($user === null || ! Hash::check($payload['password'], $user->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active yet. Please wait for registrar activation after finance verification.',
            ])->withInput();
        }

        $roleCodes = $this->rbacService->roleCodesForUserInSchool((int) $user->id, (int) $school->id);
        if ($roleCodes === []) {
            return back()->withErrors(['email' => 'No active role is assigned in this tenant.'])->withInput();
        }

        $user->last_login_at = now();
        $user->save();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();
        $request->session()->put('user_id', (int) $user->id);
        $request->session()->put('active_school_id', (int) $school->id);
        $request->session()->put('role_codes', $roleCodes);
        $request->session()->put('pending_student_activation', false);
        $request->session()->put('permission_codes', $this->rbacService->permissionCodesForUserInSchool((int) $user->id, (int) $school->id));
        $request->session()->forget('super_admin_id');

        $this->auditLogService->log(
            action: 'tenant.login',
            entityType: 'user',
            entityId: (int) $user->id,
            metadata: [
                'school_id' => $school->id,
                'school_code' => $school->school_code,
            ],
            schoolId: (int) $school->id,
            actorUserId: (int) $user->id,
            actorRoleCode: $roleCodes[0] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tenant login successful.',
                'data' => [
                    'user_id' => $user->id,
                    'active_school_id' => $school->id,
                    'role_codes' => $roleCodes,
                    'pending_student_activation' => false,
                ],
            ]);
        }

        return redirect('/tenant/dashboard');
    }

    public function sessionInfo(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            return redirect('/tenant/dashboard');
        }

        return response()->json([
            'data' => [
                'user_id' => $request->session()->get('user_id'),
                'active_school_id' => $request->session()->get('active_school_id'),
                'role_codes' => $request->session()->get('role_codes', []),
            ],
        ]);
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        $userId = $request->session()->get('user_id');
        $schoolId = $request->session()->get('active_school_id');
        $roleCodes = (array) $request->session()->get('role_codes', []);
        $redirectUrl = '/login';

        if (is_int($schoolId)) {
            $schoolCode = School::query()
                ->where('id', $schoolId)
                ->value('school_code');

            if (is_string($schoolCode) && $schoolCode !== '') {
                $redirectUrl = url('/schools/'.$schoolCode.'/login');
            }
        }

        if (is_int($userId)) {
            $this->auditLogService->log(
                action: 'tenant.logout',
                entityType: 'user',
                entityId: $userId,
                schoolId: is_int($schoolId) ? $schoolId : null,
                actorUserId: $userId,
                actorRoleCode: $roleCodes[0] ?? null,
            );
        }

        $request->session()->forget(['user_id', 'active_school_id', 'role_codes', 'permission_codes', 'pending_student_activation']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out.']);
        }

        return redirect($redirectUrl);
    }

}
