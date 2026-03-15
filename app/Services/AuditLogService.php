<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    public function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?Request $request = null,
        ?int $schoolId = null,
        ?int $actorUserId = null,
        ?int $actorSuperAdminId = null,
        ?string $actorRoleCode = null,
    ): void {
        $request ??= request();

        $sessionRoleCodes = (array) $request->session()->get('role_codes', []);

        $resolvedSchoolId = $schoolId
            ?? $request->attributes->get('active_school_id')
            ?? $request->session()->get('active_school_id');

        $resolvedActorUserId = $actorUserId
            ?? $request->attributes->get('actor_user_id')
            ?? $request->session()->get('user_id');

        $resolvedActorSuperAdminId = $actorSuperAdminId
            ?? $request->session()->get('super_admin_id');

        $resolvedRoleCode = $actorRoleCode
            ?? ($sessionRoleCodes[0] ?? null);

        $requestId = $request->attributes->get('request_id');
        if (! is_string($requestId)) {
            $requestId = (string) Str::uuid();
            $request->attributes->set('request_id', $requestId);
        }

        AuditLog::query()->create([
            'school_id' => $resolvedSchoolId,
            'actor_user_id' => $resolvedActorUserId,
            'actor_super_admin_id' => $resolvedActorSuperAdminId,
            'actor_role_code' => $resolvedRoleCode,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'request_id' => $requestId,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}

