<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminUser;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function showLoginForm(Request $request): View
    {
        // Prevent stale login forms from posting an expired CSRF token.
        $request->session()->regenerateToken();

        return view('superadmin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = SuperAdminUser::query()
            ->where('email', $credentials['email'])
            ->where('status', 'active')
            ->first();

        if ($admin === null || ! Hash::check($credentials['password'], $admin->password_hash)) {
            return back()->withErrors(['email' => 'Invalid super admin credentials.'])->withInput();
        }

        $admin->last_login_at = now();
        $admin->save();

        if ($request->boolean('remember')) {
            config(['session.lifetime' => 30 * 24 * 60]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate();
        $request->session()->put('super_admin_id', (int) $admin->id);

        $this->auditLogService->log(
            action: 'superadmin.login',
            entityType: 'super_admin_user',
            entityId: (int) $admin->id,
            actorSuperAdminId: (int) $admin->id,
        );

        return redirect()->route('superadmin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $adminId = $request->session()->get('super_admin_id');

        if (is_int($adminId)) {
            $this->auditLogService->log(
                action: 'superadmin.logout',
                entityType: 'super_admin_user',
                entityId: $adminId,
                actorSuperAdminId: $adminId,
            );
        }

        $request->session()->forget('super_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/superadmin/login');
    }
}
