<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Services\GradeWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(
        private readonly GradeWorkflowService $gradeWorkflowService,
    ) {
    }

    public function upsertDraft(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'grade_value' => ['nullable', 'string', 'max:10'],
            'submitted_remarks' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $grade = $this->gradeWorkflowService->upsertDraft(
                schoolId: (int) $request->attributes->get('active_school_id'),
                enrollmentId: (int) $payload['enrollment_id'],
                teacherUserId: (int) $request->attributes->get('actor_user_id'),
                gradeValue: $payload['grade_value'] ?? null,
                submittedRemarks: $payload['submitted_remarks'] ?? null,
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Grade draft saved.',
            'data' => $grade,
        ]);
    }

    public function submit(Request $request, Grade $grade): JsonResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Grade does not belong to active tenant.'], 404);
        }

        $payload = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $updated = $this->gradeWorkflowService->submitByTeacher(
                grade: $grade,
                teacherUserId: (int) $request->attributes->get('actor_user_id'),
                remarks: $payload['remarks'] ?? null,
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Grade submitted for dean review.',
            'data' => $updated,
        ]);
    }

    public function deanDecision(Request $request, Grade $grade): JsonResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Grade does not belong to active tenant.'], 404);
        }

        $payload = $request->validate([
            'approved' => ['required', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $updated = $this->gradeWorkflowService->deanDecision(
                grade: $grade,
                deanUserId: (int) $request->attributes->get('actor_user_id'),
                approved: (bool) $payload['approved'],
                remarks: $payload['remarks'] ?? null,
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Dean decision saved.',
            'data' => $updated,
        ]);
    }

    public function returnToDraft(Request $request, Grade $grade): JsonResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Grade does not belong to active tenant.'], 404);
        }

        $payload = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $updated = $this->gradeWorkflowService->returnToDraft(
                grade: $grade,
                teacherUserId: (int) $request->attributes->get('actor_user_id'),
                remarks: $payload['remarks'] ?? null,
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Grade moved back to draft.',
            'data' => $updated,
        ]);
    }

    public function finalize(Request $request, Grade $grade): JsonResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Grade does not belong to active tenant.'], 404);
        }

        try {
            $updated = $this->gradeWorkflowService->finalizeByRegistrar(
                grade: $grade,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Grade finalized by registrar.',
            'data' => $updated,
        ]);
    }

    public function release(Request $request, Grade $grade): JsonResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Grade does not belong to active tenant.'], 404);
        }

        try {
            $updated = $this->gradeWorkflowService->releaseByRegistrar(
                grade: $grade,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Grade released to student.',
            'data' => $updated,
        ]);
    }

    public function myReleasedGrades(Request $request): JsonResponse
    {
        $rows = $this->gradeWorkflowService->releasedGradesForStudent(
            schoolId: (int) $request->attributes->get('active_school_id'),
            studentUserId: (int) $request->attributes->get('actor_user_id'),
        );

        return response()->json([
            'data' => $rows,
        ]);
    }
}

