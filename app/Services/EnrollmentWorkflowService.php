<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Models\SubjectOffering;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrollmentWorkflowService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly BillingWorkflowService $billingWorkflowService,
    ) {
    }

    public function selectOffering(
        int $schoolId,
        int $studentUserId,
        int $semesterId,
        int $subjectOfferingId,
        int $sectionId,
    ): Enrollment {
        /** @var Enrollment $enrollment */
        $enrollment = DB::transaction(function () use ($schoolId, $studentUserId, $semesterId, $subjectOfferingId, $sectionId): Enrollment {
            $offering = SubjectOffering::query()
                ->with('subject:id,units')
                ->where('id', $subjectOfferingId)
                ->where('school_id', $schoolId)
                ->where('semester_id', $semesterId)
                ->whereIn('status', ['open', 'draft'])
                ->first();

            if ($offering === null) {
                throw new \RuntimeException('Subject offering was not found for this school and semester.');
            }

            $section = Section::query()
                ->where('id', $sectionId)
                ->where('school_id', $schoolId)
                ->where('subject_offering_id', $offering->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if ($section === null) {
                throw new \RuntimeException('Section is not available for enrollment.');
            }

            $this->assertPrerequisitesMet($schoolId, $studentUserId, (int) $offering->subject_id);
            $this->assertSectionCapacity($section);
            $this->assertNoScheduleConflict($schoolId, $studentUserId, $semesterId, $sectionId);
            $this->assertMaxLoad($schoolId, $studentUserId, $semesterId, (float) $offering->subject->units);

            $enrollment = Enrollment::query()->firstOrNew([
                'school_id' => $schoolId,
                'semester_id' => $semesterId,
                'student_user_id' => $studentUserId,
                'subject_offering_id' => $offering->id,
            ]);

            $enrollment->section_id = $sectionId;
            $enrollment->status = 'billing_pending';
            $enrollment->validation_remarks = null;
            $enrollment->save();

            $generatedBilling = $this->billingWorkflowService->generateForEnrollment($enrollment);

            $this->auditLogService->log(
                action: 'enrollment.selected',
                entityType: 'enrollment',
                entityId: (int) $enrollment->id,
                newValues: [
                    'status' => 'billing_pending',
                    'section_id' => $sectionId,
                    'billing_generated_count' => $generatedBilling->count(),
                ],
                schoolId: $schoolId,
                actorUserId: $studentUserId,
                actorRoleCode: 'student',
            );

            return $enrollment->fresh();
        });

        return $enrollment;
    }

    public function confirmByRegistrar(Enrollment $enrollment, int $registrarUserId): Enrollment
    {
        /** @var Enrollment $updated */
        $updated = DB::transaction(function () use ($enrollment, $registrarUserId): Enrollment {
            $enrollment = Enrollment::query()
                ->lockForUpdate()
                ->findOrFail($enrollment->id);

            if (! in_array($enrollment->status, ['payment_verified', 'billing_pending'], true)) {
                throw new \RuntimeException('Enrollment is not in a confirmable status.');
            }

            $allClear = $this->billingWorkflowService->allBillingsClearedForSemester(
                (int) $enrollment->school_id,
                (int) $enrollment->student_user_id,
                (int) $enrollment->semester_id,
            );

            if (! $allClear) {
                throw new \RuntimeException('Cannot confirm enrollment until billing is verified and cleared.');
            }

            $enrollment->status = 'registrar_confirmed';
            $enrollment->confirmed_by_registrar_user_id = $registrarUserId;
            $enrollment->confirmed_at = now();
            $enrollment->save();

            $enrollment->status = 'enrolled';
            $enrollment->enrolled_at = now();
            $enrollment->save();

            // Registrar confirmation activates the student's account.
            User::query()
                ->where('id', (int) $enrollment->student_user_id)
                ->where('status', 'disabled')
                ->update(['status' => 'active']);

            $this->auditLogService->log(
                action: 'enrollment.confirmed',
                entityType: 'enrollment',
                entityId: (int) $enrollment->id,
                newValues: ['status' => 'enrolled'],
                schoolId: (int) $enrollment->school_id,
                actorUserId: $registrarUserId,
                actorRoleCode: 'registrar_staff',
            );

            return $enrollment->fresh();
        });

        return $updated;
    }

    private function assertPrerequisitesMet(int $schoolId, int $studentUserId, int $subjectId): void
    {
        $requiredSubjectIds = DB::table('subject_prerequisites')
            ->where('school_id', $schoolId)
            ->where('subject_id', $subjectId)
            ->pluck('prerequisite_subject_id')
            ->map(fn ($value) => (int) $value)
            ->all();

        if ($requiredSubjectIds === []) {
            return;
        }

        $releasedGrades = DB::table('grades')
            ->join('subject_offerings', 'subject_offerings.id', '=', 'grades.subject_offering_id')
            ->where('grades.school_id', $schoolId)
            ->where('grades.student_user_id', $studentUserId)
            ->where('grades.status', 'released')
            ->whereIn('subject_offerings.subject_id', $requiredSubjectIds)
            ->pluck('grades.grade_value', 'subject_offerings.subject_id');

        $failedValues = ['F', 'FAILED', 'INC', 'DROP', 'W'];
        foreach ($requiredSubjectIds as $requiredSubjectId) {
            $gradeValue = $releasedGrades[$requiredSubjectId] ?? null;
            if ($gradeValue === null) {
                throw new \RuntimeException('Prerequisite requirement not satisfied.');
            }

            if (in_array(strtoupper((string) $gradeValue), $failedValues, true)) {
                throw new \RuntimeException('Prerequisite requirement not satisfied.');
            }
        }
    }

    private function assertSectionCapacity(Section $section): void
    {
        $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];

        $occupied = Enrollment::query()
            ->where('section_id', $section->id)
            ->whereIn('status', $activeStatuses)
            ->lockForUpdate()
            ->count();

        if ($occupied >= $section->max_capacity) {
            throw new \RuntimeException('Section capacity has been reached.');
        }
    }

    private function assertNoScheduleConflict(int $schoolId, int $studentUserId, int $semesterId, int $sectionId): void
    {
        $hasConflict = DB::table('section_schedules as new_sched')
            ->join('section_schedules as existing_sched', function ($join): void {
                $join->on('existing_sched.day_of_week', '=', 'new_sched.day_of_week')
                    ->whereRaw('existing_sched.start_time < new_sched.end_time')
                    ->whereRaw('existing_sched.end_time > new_sched.start_time');
            })
            ->join('enrollments', 'enrollments.section_id', '=', 'existing_sched.section_id')
            ->where('new_sched.section_id', $sectionId)
            ->where('new_sched.school_id', $schoolId)
            ->where('enrollments.school_id', $schoolId)
            ->where('enrollments.student_user_id', $studentUserId)
            ->where('enrollments.semester_id', $semesterId)
            ->whereIn('enrollments.status', ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'])
            ->where('enrollments.section_id', '!=', $sectionId)
            ->exists();

        if ($hasConflict) {
            throw new \RuntimeException('Schedule conflict detected.');
        }
    }

    private function assertMaxLoad(int $schoolId, int $studentUserId, int $semesterId, float $newSubjectUnits): void
    {
        $profile = StudentProfile::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $studentUserId)
            ->first();

        if ($profile === null) {
            throw new \RuntimeException('Student profile not found for max load validation.');
        }

        $program = Program::query()
            ->where('id', $profile->program_id)
            ->where('school_id', $schoolId)
            ->first();

        if ($program === null) {
            throw new \RuntimeException('Program not found for max load validation.');
        }

        $currentUnits = (float) DB::table('enrollments')
            ->join('subject_offerings', 'subject_offerings.id', '=', 'enrollments.subject_offering_id')
            ->join('subjects', 'subjects.id', '=', 'subject_offerings.subject_id')
            ->where('enrollments.school_id', $schoolId)
            ->where('enrollments.student_user_id', $studentUserId)
            ->where('enrollments.semester_id', $semesterId)
            ->whereIn('enrollments.status', ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'])
            ->sum('subjects.units');

        if (($currentUnits + $newSubjectUnits) > (float) $program->max_units_per_semester) {
            throw new \RuntimeException('Maximum semester load exceeded.');
        }
    }
}
