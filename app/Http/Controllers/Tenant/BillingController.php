<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\BillingRule;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\BillingWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingController extends Controller
{
    public function __construct(
        private readonly BillingWorkflowService $billingWorkflowService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function createRule(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'charge_type' => ['required', 'in:tuition,misc_fee,lab_fee,penalty,other'],
            'scope_type' => ['required', 'in:program,department,section,student,all'],
            'scope_id' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'is_required' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $semesterBelongsToSchool = DB::table('semesters')
            ->where('school_id', $schoolId)
            ->where('id', (int) $payload['semester_id'])
            ->exists();
        if (! $semesterBelongsToSchool) {
            throw ValidationException::withMessages([
                'semester_id' => 'Selected semester does not belong to the active school.',
            ]);
        }

        $scopeColumns = $this->resolveScopeColumns(
            schoolId: $schoolId,
            scopeType: (string) $payload['scope_type'],
            scopeId: isset($payload['scope_id']) ? (int) $payload['scope_id'] : null,
        );

        $rule = BillingRule::query()->create([
            'school_id' => $schoolId,
            'semester_id' => (int) $payload['semester_id'],
            'charge_type' => $payload['charge_type'],
            ...$scopeColumns,
            'description' => $payload['description'],
            'amount' => $payload['amount'],
            'is_required' => $payload['is_required'] ?? true,
            'status' => $payload['status'] ?? 'active',
            'created_by_finance_user_id' => (int) $request->attributes->get('actor_user_id'),
        ]);

        $this->auditLogService->log(
            action: 'billing_rule.created',
            entityType: 'billing_rule',
            entityId: (int) $rule->id,
            newValues: $rule->toArray(),
            schoolId: (int) $rule->school_id,
            actorUserId: (int) $request->attributes->get('actor_user_id'),
            actorRoleCode: 'finance_staff',
        );

        return response()->json([
            'message' => 'Billing rule created.',
            'data' => $rule,
        ], 201);
    }

    public function generateForEnrollment(Request $request, Enrollment $enrollment): JsonResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $enrollment->school_id !== $schoolId) {
            return response()->json(['message' => 'Enrollment does not belong to active tenant.'], 404);
        }

        $generated = $this->billingWorkflowService->generateForEnrollment(
            enrollment: $enrollment,
            generatedByFinanceUserId: (int) $request->attributes->get('actor_user_id'),
        );

        return response()->json([
            'message' => 'Billing generation completed.',
            'data' => $generated,
        ]);
    }

    public function submitPayment(Request $request, Billing $billing): JsonResponse
    {
        $payload = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_no' => ['nullable', 'string', 'max:80'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');

        if ((int) $billing->school_id !== $schoolId || (int) $billing->student_user_id !== $studentUserId) {
            return response()->json(['message' => 'Billing record does not belong to the current student tenant context.'], 404);
        }

        $payment = $this->billingWorkflowService->submitPayment(
            billing: $billing,
            studentUserId: $studentUserId,
            amount: (float) $payload['amount'],
            referenceNo: $payload['reference_no'] ?? null,
        );

        return response()->json([
            'message' => 'Payment submitted for verification.',
            'data' => $payment,
        ], 201);
    }

    public function verifyPayment(Request $request, Payment $payment): JsonResponse
    {
        $payload = $request->validate([
            'approved' => ['required', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        if ((int) $payment->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Payment does not belong to active tenant.'], 404);
        }

        $updated = $this->billingWorkflowService->verifyPayment(
            payment: $payment,
            financeUserId: (int) $request->attributes->get('actor_user_id'),
            approved: (bool) $payload['approved'],
            remarks: $payload['remarks'] ?? null,
        );

        return response()->json([
            'message' => 'Payment verification processed.',
            'data' => $updated,
        ]);
    }

    public function issueClearance(Request $request, Billing $billing): JsonResponse
    {
        if ((int) $billing->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Billing does not belong to active tenant.'], 404);
        }

        try {
            $updated = $this->billingWorkflowService->issueClearance(
                billing: $billing,
                financeUserId: (int) $request->attributes->get('actor_user_id'),
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Financial clearance issued.',
            'data' => $updated,
        ]);
    }

    public function myBilling(Request $request): JsonResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');

        $rows = Billing::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $rows,
        ]);
    }

    /**
     * @return array{program_id:?int,department_id:?int,section_id:?int,scope_student_user_id:?int}
     */
    private function resolveScopeColumns(int $schoolId, string $scopeType, ?int $scopeId): array
    {
        $columns = [
            'program_id' => null,
            'department_id' => null,
            'section_id' => null,
            'scope_student_user_id' => null,
        ];

        if ($scopeType === 'all') {
            return $columns;
        }

        if ($scopeId === null || $scopeId <= 0) {
            throw ValidationException::withMessages([
                'scope_id' => 'Scope ID is required for the selected scope type.',
            ]);
        }

        $scopeExists = match ($scopeType) {
            'program' => DB::table('programs')
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'department' => DB::table('departments')
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'section' => DB::table('sections')
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'student' => DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.school_id', $schoolId)
                ->where('user_roles.user_id', $scopeId)
                ->where('user_roles.is_active', true)
                ->where('roles.code', 'student')
                ->exists(),
            default => false,
        };

        if (! $scopeExists) {
            throw ValidationException::withMessages([
                'scope_id' => 'Scope record does not belong to the active school.',
            ]);
        }

        return match ($scopeType) {
            'program' => [...$columns, 'program_id' => $scopeId],
            'department' => [...$columns, 'department_id' => $scopeId],
            'section' => [...$columns, 'section_id' => $scopeId],
            'student' => [...$columns, 'scope_student_user_id' => $scopeId],
            default => $columns,
        };
    }
}
