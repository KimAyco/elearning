<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\EnrollmentWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentWorkflowService $enrollmentWorkflowService,
    ) {
    }

    public function select(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'subject_offering_id' => ['required', 'integer', 'exists:subject_offerings,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
        ]);

        try {
            $enrollment = $this->enrollmentWorkflowService->selectOffering(
                schoolId: (int) $request->attributes->get('active_school_id'),
                studentUserId: (int) $request->attributes->get('actor_user_id'),
                semesterId: (int) $payload['semester_id'],
                subjectOfferingId: (int) $payload['subject_offering_id'],
                sectionId: (int) $payload['section_id'],
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Enrollment selection accepted and moved to billing_pending.',
            'data' => $enrollment,
        ], 201);
    }

    public function confirm(Request $request, Enrollment $enrollment): JsonResponse
    {
        if ((int) $enrollment->school_id !== (int) $request->attributes->get('active_school_id')) {
            return response()->json(['message' => 'Enrollment does not belong to active tenant.'], 404);
        }

        try {
            $updated = $this->enrollmentWorkflowService->confirmByRegistrar(
                enrollment: $enrollment,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Enrollment confirmed and marked as enrolled.',
            'data' => $updated,
        ]);
    }

    public function mine(Request $request): JsonResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');

        $rows = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $rows,
        ]);
    }
}

