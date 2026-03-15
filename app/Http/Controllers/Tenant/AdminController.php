<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\FinanceFeeSetting;
use App\Models\Subject;
use App\Models\UserRole;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function assignRole(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_code' => ['required', 'string', 'exists:roles,code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $roleId = DB::table('roles')->where('code', $payload['role_code'])->value('id');

        $userRole = UserRole::query()->updateOrCreate(
            [
                'user_id' => (int) $payload['user_id'],
                'school_id' => $schoolId,
                'role_id' => (int) $roleId,
            ],
            [
                'is_active' => $payload['is_active'] ?? true,
                'assigned_by_user_id' => (int) $request->attributes->get('actor_user_id'),
                'assigned_at' => now(),
            ],
        );

        $this->auditLogService->log(
            action: 'school_admin.role_assigned',
            entityType: 'user_role',
            entityId: (int) $userRole->id,
            newValues: $userRole->toArray(),
            schoolId: $schoolId,
            actorUserId: (int) $request->attributes->get('actor_user_id'),
            actorRoleCode: 'school_admin',
        );

        return response()->json([
            'message' => 'Role assignment saved.',
            'data' => $userRole,
        ]);
    }

    public function createSubject(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:200'],
            'units' => ['required', 'numeric', 'min:0.1'],
            'price_per_subject' => ['nullable', 'numeric', 'min:0'],
            'weekly_hours' => ['required', 'numeric', 'min:0.1'],
            'duration_weeks' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', 'in:active,inactive'],
            'prerequisite_subject_ids' => ['nullable', 'array'],
            'prerequisite_subject_ids.*' => ['nullable', 'integer', 'distinct', 'exists:subjects,id'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        if (isset($payload['department_id'])) {
            $departmentBelongsToSchool = Department::query()
                ->where('school_id', $schoolId)
                ->whereKey((int) $payload['department_id'])
                ->exists();
            if (! $departmentBelongsToSchool) {
                throw ValidationException::withMessages([
                    'department_id' => 'Department must belong to your school.',
                ]);
            }
        }

        $prerequisiteSubjectIds = collect($payload['prerequisite_subject_ids'] ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($prerequisiteSubjectIds->isNotEmpty()) {
            $ownedPrerequisiteIds = Subject::query()
                ->where('school_id', $schoolId)
                ->whereIn('id', $prerequisiteSubjectIds->all())
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (count($ownedPrerequisiteIds) !== $prerequisiteSubjectIds->count()) {
                throw ValidationException::withMessages([
                    'prerequisite_subject_ids' => 'Prerequisites must belong to your school.',
                ]);
            }
        }

        $subject = Subject::query()->create([
            'school_id' => $schoolId,
            'department_id' => $payload['department_id'] ?? null,
            'code' => $payload['code'],
            'title' => $payload['title'],
            'units' => $payload['units'],
            'price_per_subject' => $this->resolveSubjectPriceForSchool(
                schoolId: $schoolId,
                units: (float) $payload['units'],
                fallbackPrice: (float) ($payload['price_per_subject'] ?? 0)
            ),
            'weekly_hours' => $payload['weekly_hours'],
            'duration_weeks' => $payload['duration_weeks'],
            'status' => $payload['status'] ?? 'active',
        ]);

        $prerequisiteRows = $prerequisiteSubjectIds
            ->filter(fn ($id) => $id !== (int) $subject->id)
            ->map(fn ($prerequisiteSubjectId) => [
                'school_id' => $schoolId,
                'subject_id' => (int) $subject->id,
                'prerequisite_subject_id' => (int) $prerequisiteSubjectId,
            ])
            ->values()
            ->all();

        if ($prerequisiteRows !== []) {
            DB::table('subject_prerequisites')->upsert(
                $prerequisiteRows,
                ['school_id', 'subject_id', 'prerequisite_subject_id'],
                []
            );
        }

        $this->auditLogService->log(
            action: 'school_admin.subject_created',
            entityType: 'subject',
            entityId: (int) $subject->id,
            newValues: $subject->toArray(),
            schoolId: $schoolId,
            actorUserId: (int) $request->attributes->get('actor_user_id'),
            actorRoleCode: 'school_admin',
        );

        return response()->json([
            'message' => 'Subject created.',
            'data' => $subject,
        ], 201);
    }

    private function resolveSubjectPriceForSchool(int $schoolId, float $units, float $fallbackPrice): float
    {
        $pricePerCourseUnit = FinanceFeeSetting::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereNull('semester_id')
            ->whereNull('academic_year_id')
            ->whereNull('program_id')
            ->orderByDesc('id')
            ->value('price_per_course_unit');

        if ($pricePerCourseUnit === null) {
            return round($fallbackPrice, 2);
        }

        return round($units * (float) $pricePerCourseUnit, 2);
    }
}
