<?php

namespace App\Services;

use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

class RbacService
{
    /**
     * @return array<int, string>
     */
    public function roleCodesForUserInSchool(int $userId, int $schoolId): array
    {
        return UserRole::query()
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->pluck('roles.code')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function permissionCodesForUserInSchool(int $userId, int $schoolId): array
    {
        return DB::table('user_roles')
            ->join('role_permissions', 'role_permissions.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->distinct()
            ->pluck('permissions.code')
            ->values()
            ->all();
    }

    public function hasPermission(int $userId, int $schoolId, string $permissionCode): bool
    {
        return DB::table('user_roles')
            ->join('role_permissions', 'role_permissions.role_id', '=', 'user_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('permissions.code', $permissionCode)
            ->exists();
    }

    public function firstFinanceUserId(int $schoolId): ?int
    {
        $id = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'finance_staff')
            ->value('user_roles.user_id');

        return $id !== null ? (int) $id : null;
    }
}

