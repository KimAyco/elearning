<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingRule;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\FinanceSetting;
use App\Models\Payment;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BillingWorkflowService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly RbacService $rbacService,
    ) {
    }

    /**
     * @return Collection<int, Billing>
     */
    public function generateForEnrollment(Enrollment $enrollment, ?int $generatedByFinanceUserId = null): Collection
    {
        $generatedByFinanceUserId ??= $this->rbacService->firstFinanceUserId((int) $enrollment->school_id);
        if ($generatedByFinanceUserId === null) {
            return new Collection();
        }

        $profile = StudentProfile::query()
            ->where('school_id', $enrollment->school_id)
            ->where('user_id', $enrollment->student_user_id)
            ->first();

        if ($profile === null) {
            return new Collection();
        }

        $rules = BillingRule::query()
            ->where('school_id', $enrollment->school_id)
            ->where('semester_id', $enrollment->semester_id)
            ->where('status', 'active')
            ->get()
            ->filter(fn (BillingRule $rule): bool => $this->ruleMatchesScope($rule, $profile, $enrollment))
            ->values();

        if ($rules->isEmpty()) {
            return new Collection();
        }

        /** @var Collection<int, Billing> $created */
        $created = DB::transaction(function () use ($rules, $profile, $enrollment, $generatedByFinanceUserId): Collection {
            $rows = new Collection();

            foreach ($rules as $rule) {
                $billing = Billing::query()->firstOrCreate(
                    [
                        'school_id' => $enrollment->school_id,
                        'semester_id' => $enrollment->semester_id,
                        'student_user_id' => $enrollment->student_user_id,
                        'billing_rule_id' => $rule->id,
                        'charge_type' => $rule->charge_type,
                        'description' => $rule->description,
                    ],
                    [
                        'enrollment_id' => $enrollment->id,
                        'amount_due' => $rule->amount,
                        'amount_paid' => 0,
                        'payment_status' => 'unpaid',
                        'clearance_status' => 'not_cleared',
                        'generated_by_finance_user_id' => $generatedByFinanceUserId,
                    ],
                );

                // If an older billing exists without enrollment_id, attach it.
                if ($billing->enrollment_id === null) {
                    $billing->enrollment_id = $enrollment->id;
                    $billing->save();
                }

                $rows->push($billing);
            }

            $this->auditLogService->log(
                action: 'billing.generated',
                entityType: 'enrollment',
                entityId: (int) $enrollment->id,
                metadata: [
                    'billing_count' => $rows->count(),
                    'student_profile_id' => $profile->id,
                ],
                schoolId: (int) $enrollment->school_id,
                actorUserId: $generatedByFinanceUserId,
                actorRoleCode: 'finance_staff',
            );

            return $rows;
        });

        return $created;
    }

    public function submitPayment(Billing $billing, int $studentUserId, float $amount, ?string $referenceNo = null): Payment
    {
        /** @var Payment $payment */
        $payment = DB::transaction(function () use ($billing, $studentUserId, $amount, $referenceNo): Payment {
            $payment = Payment::query()->create([
                'school_id' => $billing->school_id,
                'billing_id' => $billing->id,
                'student_user_id' => $studentUserId,
                'amount' => $amount,
                'reference_no' => $referenceNo,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $submittedAmount = (float) Payment::query()
                ->where('billing_id', $billing->id)
                ->whereIn('status', ['submitted', 'verified'])
                ->sum('amount');

            if ($submittedAmount <= 0.0) {
                $billing->payment_status = 'unpaid';
            } elseif ($submittedAmount < (float) $billing->amount_due) {
                $billing->payment_status = 'partial';
            } else {
                $billing->payment_status = 'paid_unverified';
            }

            $billing->save();

            $this->auditLogService->log(
                action: 'payment.submitted',
                entityType: 'payment',
                entityId: (int) $payment->id,
                newValues: $payment->toArray(),
                schoolId: (int) $billing->school_id,
                actorUserId: $studentUserId,
                actorRoleCode: 'student',
            );

            return $payment;
        });

        // Auto-approve (auto-verify) if enabled for the school.
        $setting = FinanceSetting::query()->firstOrCreate(
            ['school_id' => (int) $billing->school_id],
            ['auto_approve_payments' => false],
        );

        if ((bool) $setting->auto_approve_payments) {
            $financeUserId = $this->rbacService->firstFinanceUserId((int) $billing->school_id)
                ?? (int) ($billing->generated_by_finance_user_id ?? 0);

            if ($financeUserId > 0) {
                return $this->verifyPayment(
                    payment: $payment->fresh(),
                    financeUserId: $financeUserId,
                    approved: true,
                    remarks: 'Auto-approved',
                );
            }
        }

        return $payment;
    }

    public function verifyPayment(Payment $payment, int $financeUserId, bool $approved, ?string $remarks = null): Payment
    {
        /** @var Payment $updated */
        $updated = DB::transaction(function () use ($payment, $financeUserId, $approved, $remarks): Payment {
            $oldStatus = $payment->status;
            $payment->status = $approved ? 'verified' : 'rejected';
            $payment->remarks = $remarks;
            $payment->verified_by_finance_user_id = $financeUserId;
            $payment->verified_at = now();
            $payment->save();

            $billing = Billing::query()
                ->lockForUpdate()
                ->findOrFail($payment->billing_id);

            $verifiedAmount = (float) Payment::query()
                ->where('billing_id', $billing->id)
                ->where('status', 'verified')
                ->sum('amount');

            $billing->amount_paid = $verifiedAmount;
            $billing->verified_by_finance_user_id = $financeUserId;
            $billing->verified_at = now();

            if ($verifiedAmount <= 0.0) {
                $billing->payment_status = 'unpaid';
            } elseif ($verifiedAmount < (float) $billing->amount_due) {
                $billing->payment_status = 'partial';
            } else {
                $billing->payment_status = 'verified';
            }

            $billing->save();

            if ($billing->enrollment_id !== null && $billing->payment_status === 'verified') {
                $enrollment = Enrollment::query()
                    ->lockForUpdate()
                    ->find($billing->enrollment_id);

                if ($enrollment !== null) {
                    $allVerified = Billing::query()
                        ->where('school_id', $enrollment->school_id)
                        ->where('student_user_id', $enrollment->student_user_id)
                        ->where('semester_id', $enrollment->semester_id)
                        ->where('payment_status', '!=', 'verified')
                        ->doesntExist();

                    if ($allVerified && in_array((string) $enrollment->status, ['billing_pending', 'validated', 'selected'], true)) {
                        $enrollment->status = 'payment_verified';
                        $enrollment->save();
                    }
                }
            }

            $this->auditLogService->log(
                action: 'payment.verified',
                entityType: 'payment',
                entityId: (int) $payment->id,
                oldValues: ['status' => $oldStatus],
                newValues: $payment->toArray(),
                metadata: ['approved' => $approved],
                schoolId: (int) $payment->school_id,
                actorUserId: $financeUserId,
                actorRoleCode: 'finance_staff',
            );

            return $payment->fresh();
        });

        return $updated;
    }

    public function issueClearance(Billing $billing, int $financeUserId): Billing
    {
        /** @var Billing $updated */
        $updated = DB::transaction(function () use ($billing, $financeUserId): Billing {
            $billing = Billing::query()->lockForUpdate()->findOrFail($billing->id);

            if ($billing->payment_status !== 'verified') {
                throw new \RuntimeException('Billing must be verified before issuing clearance.');
            }

            $billing->clearance_status = 'pending_approval';
            $billing->cleared_by_finance_user_id = $financeUserId;
            $billing->cleared_at = now();
            $billing->save();

            $this->auditLogService->log(
                action: 'billing.clearance_issued',
                entityType: 'billing',
                entityId: (int) $billing->id,
                newValues: ['clearance_status' => 'pending_approval'],
                schoolId: (int) $billing->school_id,
                actorUserId: $financeUserId,
                actorRoleCode: 'finance_staff',
            );

            return $billing;
        });

        return $updated;
    }

    public function approveClearance(Billing $billing, int $registrarUserId): Billing
    {
        /** @var Billing $updated */
        $updated = DB::transaction(function () use ($billing, $registrarUserId): Billing {
            $billing = Billing::query()->lockForUpdate()->findOrFail($billing->id);

            if ($billing->clearance_status !== 'pending_approval') {
                throw new \RuntimeException('Billing clearance must be pending approval.');
            }

            $billing->clearance_status = 'cleared';
            $billing->approved_by_registrar_user_id = $registrarUserId;
            $billing->approved_at = now();
            $billing->save();

            // Check if all billings for this student's semester are now cleared
            $allCleared = $this->allBillingsClearedForSemester(
                (int) $billing->school_id,
                (int) $billing->student_user_id,
                (int) $billing->semester_id,
            );

            if ($allCleared) {
                // Update enrollments that are waiting for clearance approval
                $enrollments = Enrollment::query()
                    ->where('school_id', (int) $billing->school_id)
                    ->where('student_user_id', (int) $billing->student_user_id)
                    ->where('semester_id', (int) $billing->semester_id)
                    ->whereIn('status', ['payment_verified', 'billing_pending', 'registrar_confirmed'])
                    ->get();

                foreach ($enrollments as $enrollment) {
                    // If already confirmed by registrar (has confirmed_by_registrar_user_id), move directly to enrolled
                    if ($enrollment->confirmed_by_registrar_user_id !== null || $enrollment->status === 'registrar_confirmed') {
                        $enrollment->status = 'enrolled';
                        if ($enrollment->enrolled_at === null) {
                            $enrollment->enrolled_at = now();
                        }
                        if ($enrollment->confirmed_by_registrar_user_id === null) {
                            $enrollment->confirmed_by_registrar_user_id = $registrarUserId;
                            $enrollment->confirmed_at = now();
                        }
                        $enrollment->save();

                        // Activate student account if needed
                        User::query()
                            ->where('id', (int) $enrollment->student_user_id)
                            ->where('status', 'disabled')
                            ->update(['status' => 'active']);
                    } elseif (in_array($enrollment->status, ['payment_verified', 'billing_pending'], true)) {
                        // When all clearances are approved, automatically enroll (registrar approval of clearance = enrollment ready)
                        $enrollment->status = 'enrolled';
                        $enrollment->enrolled_at = now();
                        // Set registrar confirmation if not already set
                        if ($enrollment->confirmed_by_registrar_user_id === null) {
                            $enrollment->confirmed_by_registrar_user_id = $registrarUserId;
                            $enrollment->confirmed_at = now();
                        }
                        $enrollment->save();

                        // Activate student account if needed
                        User::query()
                            ->where('id', (int) $enrollment->student_user_id)
                            ->where('status', 'disabled')
                            ->update(['status' => 'active']);
                    }
                }
            }

            $this->auditLogService->log(
                action: 'billing.clearance_approved',
                entityType: 'billing',
                entityId: (int) $billing->id,
                oldValues: ['clearance_status' => 'pending_approval'],
                newValues: ['clearance_status' => 'cleared'],
                schoolId: (int) $billing->school_id,
                actorUserId: $registrarUserId,
                actorRoleCode: 'registrar_staff',
            );

            return $billing;
        });

        return $updated;
    }

    public function allBillingsClearedForSemester(int $schoolId, int $studentUserId, int $semesterId): bool
    {
        // Check if there are any billings that are:
        // 1. Payment verified BUT clearance not yet cleared (pending_approval or not_cleared)
        // OR
        // 2. Payment not yet verified
        //
        // We only consider billings that are relevant to enrollment clearance:
        // - If payment_status is 'verified', clearance_status must be 'cleared'
        // - Unpaid billings that have clearance_status = 'not_cleared' are ignored (they're just unpaid bills)
        // - Unpaid billings that have clearance_status = 'pending_approval' means finance tried to clear them, so they count
        
        return Billing::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->where('semester_id', $semesterId)
            ->where(function ($query): void {
                // Case 1: Payment is verified but clearance is not cleared yet
                $query->where(function ($q): void {
                    $q->where('payment_status', 'verified')
                        ->whereIn('clearance_status', ['not_cleared', 'pending_approval']);
                })
                // Case 2: Clearance is pending approval (regardless of payment status)
                // This means finance issued a clearance that needs registrar approval
                ->orWhere('clearance_status', 'pending_approval');
            })
            ->doesntExist();
    }

    private function ruleMatchesScope(BillingRule $rule, StudentProfile $profile, Enrollment $enrollment): bool
    {
        if ($rule->year_level !== null && (int) $rule->year_level !== (int) $profile->year_level) {
            return false;
        }

        if ($rule->program_id !== null) {
            return (int) $rule->program_id === (int) $profile->program_id;
        }
        if ($rule->department_id !== null) {
            return (int) $rule->department_id === (int) $profile->department_id;
        }
        if ($rule->section_id !== null) {
            return (int) $rule->section_id === (int) $enrollment->section_id;
        }
        if ($rule->scope_student_user_id !== null) {
            return (int) $rule->scope_student_user_id === (int) $enrollment->student_user_id;
        }

        // No scope columns means school-wide rule.
        return true;
    }
}
