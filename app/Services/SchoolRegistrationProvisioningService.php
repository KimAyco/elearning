<?php

namespace App\Services;

use App\Models\College;
use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolRegistration;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolRegistrationProvisioningService
{
    public function provision(SchoolRegistration $registration): ?string
    {
        $meta = (array) ($registration->metadata ?? []);
        if (isset($meta['school_code']) && is_string($meta['school_code']) && $meta['school_code'] !== '') {
            return $meta['school_code'];
        }

        $schoolAdminRole = Role::query()->firstOrCreate(
            ['code' => 'school_admin'],
            [
                'name' => 'School Administrator',
                'description' => 'Default school owner role created during registration provisioning.',
            ]
        );
        $this->ensureSchoolAdminPermissions($schoolAdminRole);
        $roleId = $schoolAdminRole->id;

        $passwordHash = isset($meta['admin_password_hash']) && is_string($meta['admin_password_hash'])
            ? $meta['admin_password_hash']
            : null;
        if ($passwordHash === null || $passwordHash === '') {
            return null;
        }

        return DB::transaction(function () use ($registration, $passwordHash, $roleId, $meta): string {
            $schoolCode = $this->generateUniqueSchoolCode($registration);

            $school = School::query()->create([
                'school_code' => $schoolCode,
                'name' => $registration->name,
                'short_description' => Str::limit((string) ($registration->address ?? ('School portal for ' . $registration->name)), 255, ''),
                'status' => 'active',
                'subscription_state' => 'active',
            ]);

            College::query()->create([
                'school_id' => (int) $school->id,
                'code' => 'MAIN',
                'name' => $registration->name,
                'status' => 'active',
            ]);

            $user = User::query()->create([
                'full_name' => $registration->name . ' Admin',
                'email' => $registration->email,
                'password_hash' => $passwordHash,
                'status' => 'active',
                'phone' => $registration->phone,
                'address' => $registration->address,
            ]);

            $nonStudentRoleIds = Role::query()
                ->where('code', '!=', 'student')
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();
            if ($nonStudentRoleIds === []) {
                $nonStudentRoleIds = [(int) $roleId];
            }

            foreach ($nonStudentRoleIds as $activeRoleId) {
                UserRole::query()->updateOrCreate(
                    [
                        'user_id' => (int) $user->id,
                        'school_id' => (int) $school->id,
                        'role_id' => $activeRoleId,
                    ],
                    [
                        'is_active' => true,
                        'assigned_at' => now(),
                    ]
                );
            }

            $registration->update([
                'metadata' => array_merge($meta, [
                    'school_id' => (int) $school->id,
                    'school_code' => $schoolCode,
                    'admin_user_id' => (int) $user->id,
                    'provisioned_at' => now()->toDateTimeString(),
                ]),
            ]);

            return $schoolCode;
        });
    }

    private function generateUniqueSchoolCode(SchoolRegistration $registration): string
    {
        $seed = $registration->subdomain ?: $registration->name;
        $base = strtoupper((string) Str::of($seed)->replaceMatches('/[^a-zA-Z0-9]+/', '-')->trim('-'));
        $base = Str::limit($base !== '' ? $base : 'SCHOOL', 24, '');

        $candidate = $base;
        $counter = 1;
        while (School::query()->where('school_code', $candidate)->exists()) {
            $counter++;
            $suffix = '-' . $counter;
            $candidate = Str::limit($base, 30 - strlen($suffix), '') . $suffix;
        }

        return $candidate;
    }

    private function ensureSchoolAdminPermissions(Role $schoolAdminRole): void
    {
        if (Permission::query()->count() === 0) {
            $permissions = [
                ['code' => 'school_admin.manage_staff', 'module' => 'school_admin', 'description' => 'Manage tenant staff members.'],
                ['code' => 'school_admin.manage_curriculum', 'module' => 'school_admin', 'description' => 'Configure programs and subjects.'],
                ['code' => 'school_admin.assign_roles', 'module' => 'school_admin', 'description' => 'Assign roles to tenant users.'],
                ['code' => 'finance.billing.manage', 'module' => 'finance', 'description' => 'Create and manage billing entries and rules.'],
                ['code' => 'finance.payment.verify', 'module' => 'finance', 'description' => 'Verify student payments.'],
                ['code' => 'finance.clearance.issue', 'module' => 'finance', 'description' => 'Issue or revoke financial clearances.'],
                ['code' => 'registrar.enrollment.confirm', 'module' => 'registrar', 'description' => 'Confirm enrollments after clearance.'],
                ['code' => 'registrar.records.manage', 'module' => 'registrar', 'description' => 'Maintain academic records.'],
                ['code' => 'registrar.grades.finalize', 'module' => 'registrar', 'description' => 'Finalize dean-approved grades.'],
                ['code' => 'registrar.grades.release', 'module' => 'registrar', 'description' => 'Release finalized grades to students.'],
                ['code' => 'dean.grades.review', 'module' => 'dean', 'description' => 'Approve or reject submitted grades.'],
                ['code' => 'dean.subject_assign', 'module' => 'dean', 'description' => 'Assign teachers to subject offerings.'],
                ['code' => 'teacher.grades.submit', 'module' => 'teacher', 'description' => 'Prepare and submit grades.'],
                ['code' => 'teacher.content.manage', 'module' => 'teacher', 'description' => 'Maintain instructional content.'],
                ['code' => 'student.enrollment.create', 'module' => 'student', 'description' => 'Select and submit enrollment requests.'],
                ['code' => 'student.records.view', 'module' => 'student', 'description' => 'View personal enrollment and billing records.'],
                ['code' => 'student.grades.view', 'module' => 'student', 'description' => 'View released grades only.'],
            ];

            foreach ($permissions as $permissionData) {
                Permission::query()->updateOrCreate(
                    ['code' => $permissionData['code']],
                    $permissionData,
                );
            }
        }

        $allPermissionIds = Permission::query()->pluck('id')->all();
        if ($allPermissionIds !== []) {
            $schoolAdminRole->permissions()->syncWithoutDetaching($allPermissionIds);
        }
    }
}
