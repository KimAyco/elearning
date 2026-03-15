<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Seed the application's RBAC definitions.
     */
    public function run(): void
    {
        $roles = [
            ['code' => 'school_admin', 'name' => 'School Admin', 'description' => 'Full access to all school operations including finance, registrar, dean, and teacher functions.'],
            ['code' => 'finance_staff', 'name' => 'Finance Staff', 'description' => 'Manage billing, payment verification, and clearances.'],
            ['code' => 'registrar_staff', 'name' => 'Registrar Staff', 'description' => 'Confirm enrollments and release grades.'],
            ['code' => 'dean', 'name' => 'Dean', 'description' => 'Approve or reject grade submissions and assign teachers.'],
            ['code' => 'teacher', 'name' => 'Teacher', 'description' => 'Manage instructional content and submit grades.'],
            ['code' => 'student', 'name' => 'Student', 'description' => 'Enroll and view personal released records.'],
        ];

        foreach ($roles as $roleData) {
            Role::query()->updateOrCreate(
                ['code' => $roleData['code']],
                $roleData,
            );
        }

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

        // School Admin gets ALL permissions (full access to finance, registrar, dean, teacher functions)
        $allPermissionCodes = array_map(fn ($p) => $p['code'], $permissions);

        $rolePermissionMap = [
            'school_admin' => $allPermissionCodes, // Full access to everything
            'finance_staff' => [
                'finance.billing.manage',
                'finance.payment.verify',
                'finance.clearance.issue',
            ],
            'registrar_staff' => [
                'registrar.enrollment.confirm',
                'registrar.records.manage',
                'registrar.grades.finalize',
                'registrar.grades.release',
            ],
            'dean' => [
                'dean.grades.review',
                'dean.subject_assign',
            ],
            'teacher' => [
                'teacher.grades.submit',
                'teacher.content.manage',
            ],
            'student' => [
                'student.enrollment.create',
                'student.records.view',
                'student.grades.view',
            ],
        ];

        foreach ($rolePermissionMap as $roleCode => $permissionCodes) {
            $role = Role::query()->where('code', $roleCode)->first();
            if ($role === null) {
                continue;
            }

            $permissionIds = Permission::query()
                ->whereIn('code', $permissionCodes)
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
