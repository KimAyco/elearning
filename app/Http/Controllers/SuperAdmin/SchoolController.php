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

    /**
     * @return array{schools: \Illuminate\Support\Collection, pendingRegistrations: \Illuminate\Support\Collection, paymentPlans: \Illuminate\Support\Collection, platformSettings: object|null}
     */
    private function superAdminPayload(): array
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

        return [
            'schools' => $schools,
            'pendingRegistrations' => $pendingRegistrations,
            'paymentPlans' => $paymentPlans,
            'platformSettings' => $platformSettings,
        ];
    }

    private function jsonIndexResponse(): JsonResponse
    {
        $p = $this->superAdminPayload();

        return response()->json([
            'data' => [
                'schools' => $p['schools'],
                'pending_registrations' => $p['pendingRegistrations'],
                'payment_plans' => $p['paymentPlans'],
                'platform_settings' => $p['platformSettings'],
            ],
        ]);
    }

    public function dashboard(Request $request): View
    {
        return view('superadmin.dashboard', array_merge(
            $this->superAdminPayload(),
            $this->dashboardAnalytics(),
            ['activeNav' => 'dashboard']
        ));
    }

    /**
     * @return array{chartSubscriptionLabels: list<string>, chartSubscriptionData: list<int>, chartMonthlyLabels: list<string>, chartMonthlyData: list<int>}
     */
    private function dashboardAnalytics(): array
    {
        $schools = School::query()->get(['subscription_state', 'created_at']);

        $stateLabels = [
            'trial' => 'Trial',
            'active' => 'Active billing',
            'past_due' => 'Past due',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];
        $stateOrder = ['trial', 'active', 'past_due', 'expired', 'cancelled'];

        $subLabels = [];
        $subData = [];
        foreach ($stateOrder as $state) {
            $n = $schools->where('subscription_state', $state)->count();
            if ($n > 0) {
                $subLabels[] = $stateLabels[$state] ?? $state;
                $subData[] = $n;
            }
        }
        if ($subLabels === []) {
            $subLabels = ['No tenants yet'];
            $subData = [0];
        }

        $byMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i)->startOfMonth();
            $byMonth[$d->format('Y-m')] = ['label' => $d->format('M'), 'count' => 0];
        }
        foreach ($schools as $s) {
            $key = $s->created_at->format('Y-m');
            if (isset($byMonth[$key])) {
                $byMonth[$key]['count']++;
            }
        }

        return [
            'chartSubscriptionLabels' => $subLabels,
            'chartSubscriptionData' => $subData,
            'chartMonthlyLabels' => array_column($byMonth, 'label'),
            'chartMonthlyData' => array_column($byMonth, 'count'),
        ];
    }

    public function schools(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return $this->jsonIndexResponse();
        }

        return view('superadmin.schools', array_merge(
            $this->superAdminPayload(),
            ['activeNav' => 'schools']
        ));
    }

    public function pricing(Request $request): View
    {
        return view('superadmin.pricing', array_merge(
            $this->superAdminPayload(),
            ['activeNav' => 'pricing']
        ));
    }

    public function approvals(Request $request): View
    {
        return view('superadmin.approvals', array_merge(
            $this->superAdminPayload(),
            ['activeNav' => 'approvals']
        ));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            return redirect()->route('superadmin.schools')->withErrors([
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
            return redirect()->route('superadmin.schools')->with('status', 'School status updated.');
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
            return redirect()->route('superadmin.approvals')->withErrors(['registration' => 'Only paid registrations can be approved.']);
        }

        $schoolCode = $this->provisioningService->provision($registration);
        if ($schoolCode === null) {
            return redirect()->route('superadmin.approvals')->withErrors(['registration' => 'Unable to provision school/admin account from registration data.']);
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

        return redirect()->route('superadmin.approvals')->with('status', 'Registration approved. School code: '.$schoolCode);
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

        return redirect()->route('superadmin.pricing')->with('status', 'Default monthly price updated.');
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

            return redirect()->route('superadmin.pricing')->with('status', 'Payment plan updated.');
        }

        $duplicateMonths = DB::table('payment_plans')
            ->where('months', (int) $payload['months'])
            ->exists();
        if ($duplicateMonths) {
            return redirect()->route('superadmin.pricing')->withErrors([
                'plan' => 'A plan for '.(int) $payload['months'].' month(s) already exists. Edit the existing plan instead.',
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

        return redirect()->route('superadmin.pricing')->with('status', 'Payment plan added.');
    }
}
