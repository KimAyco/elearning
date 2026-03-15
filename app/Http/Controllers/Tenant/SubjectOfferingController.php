<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SubjectOffering;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectOfferingController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function assignTeacher(Request $request, SubjectOffering $subjectOffering): JsonResponse
    {
        $payload = $request->validate([
            'teacher_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $subjectOffering->school_id !== $schoolId) {
            return response()->json(['message' => 'Subject offering does not belong to active tenant.'], 404);
        }

        $teacherBelongsToSchool = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $payload['teacher_user_id'])
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->exists();

        if (! $teacherBelongsToSchool) {
            return response()->json(['message' => 'Selected user has no active teacher role in this school.'], 422);
        }

        $teacherCanTeachSubject = DB::table('teacher_subjects')
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', (int) $payload['teacher_user_id'])
            ->where('subject_id', (int) $subjectOffering->subject_id)
            ->exists();

        if (! $teacherCanTeachSubject) {
            return response()->json(['message' => 'Selected teacher is not allowed to teach this subject.'], 422);
        }

        $oldTeacher = $subjectOffering->assigned_teacher_user_id;
        $subjectOffering->assigned_teacher_user_id = (int) $payload['teacher_user_id'];
        $subjectOffering->save();

        $this->auditLogService->log(
            action: 'subject_offering.teacher_assigned',
            entityType: 'subject_offering',
            entityId: (int) $subjectOffering->id,
            oldValues: ['assigned_teacher_user_id' => $oldTeacher],
            newValues: ['assigned_teacher_user_id' => $subjectOffering->assigned_teacher_user_id],
            schoolId: $schoolId,
            actorUserId: (int) $request->attributes->get('actor_user_id'),
            actorRoleCode: 'dean',
        );

        return response()->json([
            'message' => 'Teacher assignment updated.',
            'data' => $subjectOffering,
        ]);
    }
}
