<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SubjectOffering;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GradeWorkflowService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function upsertDraft(
        int $schoolId,
        int $enrollmentId,
        int $teacherUserId,
        ?string $gradeValue,
        ?string $submittedRemarks = null,
    ): Grade {
        /** @var Grade $grade */
        $grade = DB::transaction(function () use ($schoolId, $enrollmentId, $teacherUserId, $gradeValue, $submittedRemarks): Grade {
            $enrollment = Enrollment::query()
                ->where('id', $enrollmentId)
                ->where('school_id', $schoolId)
                ->first();

            if ($enrollment === null) {
                throw new \RuntimeException('Enrollment not found.');
            }

            $offering = SubjectOffering::query()
                ->where('id', $enrollment->subject_offering_id)
                ->where('school_id', $schoolId)
                ->first();

            if ($offering === null || (int) $offering->assigned_teacher_user_id !== $teacherUserId) {
                throw new \RuntimeException('Teacher is not assigned to this subject offering.');
            }

            $grade = Grade::query()->firstOrNew([
                'enrollment_id' => $enrollment->id,
            ]);

            if ($grade->exists && in_array($grade->status, ['registrar_finalized', 'released'], true)) {
                throw new \RuntimeException('Finalized grades are immutable.');
            }

            if ($grade->exists && ! in_array($grade->status, ['draft', 'dean_rejected'], true)) {
                throw new \RuntimeException('Only draft or dean-rejected grades can be modified by teachers.');
            }

            $grade->school_id = $schoolId;
            $grade->student_user_id = $enrollment->student_user_id;
            $grade->subject_offering_id = $enrollment->subject_offering_id;
            $grade->section_id = $enrollment->section_id;
            $grade->teacher_user_id = $teacherUserId;
            $grade->grade_value = $gradeValue;
            $grade->submitted_remarks = $submittedRemarks;
            $grade->status = 'draft';
            $grade->save();

            $this->auditLogService->log(
                action: 'grade.draft_saved',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: [
                    'status' => 'draft',
                    'grade_value' => $gradeValue,
                ],
                schoolId: $schoolId,
                actorUserId: $teacherUserId,
                actorRoleCode: 'teacher',
            );

            return $grade->fresh();
        });

        return $grade;
    }

    public function submitByTeacher(Grade $grade, int $teacherUserId, ?string $remarks = null): Grade
    {
        /** @var Grade $updated */
        $updated = DB::transaction(function () use ($grade, $teacherUserId, $remarks): Grade {
            $grade = Grade::query()->lockForUpdate()->findOrFail($grade->id);

            if ((int) $grade->teacher_user_id !== $teacherUserId) {
                throw new \RuntimeException('Teacher can only submit their own grade entries.');
            }

            if ($grade->status !== 'draft') {
                throw new \RuntimeException('Only draft grades can be submitted.');
            }

            $grade->status = 'submitted';
            $grade->submitted_remarks = $remarks;
            $grade->submitted_at = now();
            $grade->save();

            $this->auditLogService->log(
                action: 'grade.submitted',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: ['status' => 'submitted'],
                schoolId: (int) $grade->school_id,
                actorUserId: $teacherUserId,
                actorRoleCode: 'teacher',
            );

            return $grade->fresh();
        });

        return $updated;
    }

    public function deanDecision(Grade $grade, int $deanUserId, bool $approved, ?string $remarks): Grade
    {
        /** @var Grade $updated */
        $updated = DB::transaction(function () use ($grade, $deanUserId, $approved, $remarks): Grade {
            $grade = Grade::query()->lockForUpdate()->findOrFail($grade->id);

            if ($grade->status !== 'submitted') {
                throw new \RuntimeException('Only submitted grades can be reviewed by the dean.');
            }

            $grade->status = $approved ? 'dean_approved' : 'dean_rejected';
            $grade->dean_user_id = $deanUserId;
            $grade->dean_decision_remarks = $remarks;
            $grade->dean_decided_at = now();
            $grade->save();

            $this->auditLogService->log(
                action: $approved ? 'grade.dean_approved' : 'grade.dean_rejected',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: [
                    'status' => $grade->status,
                    'remarks' => $remarks,
                ],
                schoolId: (int) $grade->school_id,
                actorUserId: $deanUserId,
                actorRoleCode: 'dean',
            );

            return $grade->fresh();
        });

        return $updated;
    }

    public function returnToDraft(Grade $grade, int $teacherUserId, ?string $remarks = null): Grade
    {
        /** @var Grade $updated */
        $updated = DB::transaction(function () use ($grade, $teacherUserId, $remarks): Grade {
            $grade = Grade::query()->lockForUpdate()->findOrFail($grade->id);

            if ((int) $grade->teacher_user_id !== $teacherUserId) {
                throw new \RuntimeException('Only assigned teacher can revise rejected grades.');
            }

            if ($grade->status !== 'dean_rejected') {
                throw new \RuntimeException('Only dean-rejected grades can return to draft.');
            }

            $grade->status = 'draft';
            $grade->submitted_remarks = $remarks;
            $grade->save();

            $this->auditLogService->log(
                action: 'grade.returned_to_draft',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: ['status' => 'draft'],
                schoolId: (int) $grade->school_id,
                actorUserId: $teacherUserId,
                actorRoleCode: 'teacher',
            );

            return $grade->fresh();
        });

        return $updated;
    }

    public function finalizeByRegistrar(Grade $grade, int $registrarUserId): Grade
    {
        /** @var Grade $updated */
        $updated = DB::transaction(function () use ($grade, $registrarUserId): Grade {
            $grade = Grade::query()->lockForUpdate()->findOrFail($grade->id);

            if ($grade->status !== 'dean_approved') {
                throw new \RuntimeException('Only dean-approved grades can be finalized.');
            }

            $grade->status = 'registrar_finalized';
            $grade->registrar_user_id = $registrarUserId;
            $grade->finalized_at = now();
            $grade->save();

            $this->auditLogService->log(
                action: 'grade.registrar_finalized',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: ['status' => 'registrar_finalized'],
                schoolId: (int) $grade->school_id,
                actorUserId: $registrarUserId,
                actorRoleCode: 'registrar_staff',
            );

            return $grade->fresh();
        });

        return $updated;
    }

    public function releaseByRegistrar(Grade $grade, int $registrarUserId): Grade
    {
        /** @var Grade $updated */
        $updated = DB::transaction(function () use ($grade, $registrarUserId): Grade {
            $grade = Grade::query()->lockForUpdate()->findOrFail($grade->id);

            if ($grade->status !== 'registrar_finalized') {
                throw new \RuntimeException('Only registrar-finalized grades can be released.');
            }

            $grade->status = 'released';
            $grade->registrar_user_id = $registrarUserId;
            $grade->released_at = now();
            $grade->save();

            $this->auditLogService->log(
                action: 'grade.released',
                entityType: 'grade',
                entityId: (int) $grade->id,
                newValues: ['status' => 'released'],
                schoolId: (int) $grade->school_id,
                actorUserId: $registrarUserId,
                actorRoleCode: 'registrar_staff',
            );

            return $grade->fresh();
        });

        return $updated;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function releasedGradesForStudent(int $schoolId, int $studentUserId): Collection
    {
        return Grade::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->where('status', 'released')
            ->orderByDesc('released_at')
            ->get();
    }
}

