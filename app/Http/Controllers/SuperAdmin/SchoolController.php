<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolRegistration;
use App\Services\AuditLogService;
use App\Services\SchoolRegistrationProvisioningService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly SchoolRegistrationProvisioningService $provisioningService,
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $schools = School::query()
            ->orderBy('name')
            ->get([
                'id',
                'school_code',
                'name',
                'short_description',
                'status',
                'subscription_state',
                'suspended_at',
                'created_at',
                'updated_at',
            ]);
        $pendingRegistrations = SchoolRegistration::query()
            ->whereIn('status', ['pending', 'paid'])
            ->orderByDesc('id')
            ->get([
                'id',
                'name',
                'email',
                'plan_months',
                'status',
                'created_at',
                'updated_at',
            ]);
        $paymentPlans = DB::table('payment_plans')
            ->orderBy('months')
            ->get([
                'id',
                'name',
                'months',
                'price_per_month',
                'total_price',
            ]);
        $platformSettings = DB::table('platform_settings')
            ->select(['id', 'price_per_month', 'auto_approve_after_payment'])
            ->first();

        if (! $request->expectsJson()) {
            return view('superadmin.schools', [
                'schools' => $schools,
                'pendingRegistrations' => $pendingRegistrations,
                'paymentPlans' => $paymentPlans,
                'platformSettings' => $platformSettings,
            ]);
        }

        return response()->json([
            'data' => [
                'schools' => $schools,
                'pending_registrations' => $pendingRegistrations,
                'payment_plans' => $paymentPlans,
                'platform_settings' => $platformSettings,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            return redirect('/superadmin/schools')->withErrors([
                'school' => 'Manual school creation is disabled. Schools must register, subscribe, and pay before approval.',
            ]);
        }

        return response()->json([
            'message' => 'Manual school creation is disabled. Use the school registration flow.',
        ], 403);
    }

    public function updateStatus(Request $request, School $school): JsonResponse|RedirectResponse
    {
        $payload = $request->validate([
            'status' => ['required', 'in:active,suspended'],
        ]);

        $oldValues = [
            'status' => $school->status,
            'suspended_at' => $school->suspended_at,
        ];

        $school->status = $payload['status'];
        $school->suspended_at = $payload['status'] === 'suspended' ? now() : null;
        $school->save();

        $this->auditLogService->log(
            action: $payload['status'] === 'suspended' ? 'school.suspended' : 'school.resumed',
            entityType: 'school',
            entityId: (int) $school->id,
            oldValues: $oldValues,
            newValues: [
                'status' => $school->status,
                'suspended_at' => $school->suspended_at,
            ],
            actorSuperAdminId: $request->session()->get('super_admin_id'),
        );

        if (! $request->expectsJson()) {
            return redirect('/superadmin/schools')->with('status', 'School status updated.');
        }

        return response()->json([
            'message' => 'School status updated.',
            'data' => $school,
        ]);
    }

    public function subscription(School $school): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $school->id,
                'school_code' => $school->school_code,
                'name' => $school->name,
                'subscription_state' => $school->subscription_state,
                'status' => $school->status,
            ],
        ]);
    }

    public function approveRegistration(Request $request, SchoolRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'paid') {
            return redirect('/superadmin/schools')->withErrors(['registration' => 'Only paid registrations can be approved.']);
        }

        $schoolCode = $this->provisioningService->provision($registration);
        if ($schoolCode === null) {
            return redirect('/superadmin/schools')->withErrors(['registration' => 'Unable to provision school/admin account from registration data.']);
        }

        $registration->status = 'approved';
        $registration->auto_approved = false;
        $registration->save();

        $this->auditLogService->log(
            action: 'school_registration.approved',
            entityType: 'school_registration',
            entityId: (int) $registration->id,
            metadata: [
                'school_code' => $schoolCode,
                'email' => $registration->email,
            ],
            actorSuperAdminId: $request->session()->get('super_admin_id'),
        );

        return redirect('/superadmin/schools')->with('status', 'Registration approved. School code: ' . $schoolCode);
    }

    public function updatePlatformPricing(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'price_per_month' => ['required', 'numeric', 'min:0'],
        ]);

        $settings = DB::table('platform_settings')->first();
        if ($settings === null) {
            DB::table('platform_settings')->insert([
                'price_per_month' => $payload['price_per_month'],
                'auto_approve_after_payment' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('platform_settings')
                ->where('id', $settings->id)
                ->update([
                    'price_per_month' => $payload['price_per_month'],
                    'updated_at' => now(),
                ]);
        }

        return redirect('/superadmin/schools')->with('status', 'Default monthly price updated.');
    }

    public function upsertPaymentPlan(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'plan_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:120'],
            'months' => ['required', 'integer', 'min:1', 'max:120'],
            'price_per_month' => ['required', 'numeric', 'min:0'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $planId = isset($payload['plan_id']) ? (int) $payload['plan_id'] : null;
        $computedTotal = $payload['total_price'] ?? ((float) $payload['price_per_month'] * (int) $payload['months']);

        if ($planId !== null && $planId > 0) {
            DB::table('payment_plans')
                ->where('id', $planId)
                ->update([
                    'name' => $payload['name'],
                    'months' => (int) $payload['months'],
                    'price_per_month' => $payload['price_per_month'],
                    'total_price' => $computedTotal,
                    'updated_at' => now(),
                ]);

            return redirect('/superadmin/schools')->with('status', 'Payment plan updated.');
        }

        $duplicateMonths = DB::table('payment_plans')
            ->where('months', (int) $payload['months'])
            ->exists();
        if ($duplicateMonths) {
            return redirect('/superadmin/schools')->withErrors([
                'plan' => 'A plan for ' . (int) $payload['months'] . ' month(s) already exists. Edit the existing plan instead.',
            ]);
        }

        DB::table('payment_plans')->insert([
            'name' => $payload['name'],
            'months' => (int) $payload['months'],
            'price_per_month' => $payload['price_per_month'],
            'total_price' => $computedTotal,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/superadmin/schools')->with('status', 'Payment plan added.');
    }
}
