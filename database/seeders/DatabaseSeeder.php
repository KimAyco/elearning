<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SuperAdminUser;
use App\Models\User;
use App\Models\UserRole;
use App\Models\SchoolProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,
            TeacherStaffSeeder::class,
            SubjectPriceSeeder::class,
        ]);

        $school = School::query()->firstOrCreate([
            'school_code' => 'DEMO-U',
        ], [
            'name' => 'Demo University',
            'short_description' => 'Prototype tenant school for onboarding and smoke tests.',
            'status' => 'active',
            'subscription_state' => 'trial',
        ]);

        // Seed public page content for the demo school
        SchoolProfile::query()->updateOrCreate(
            ['school_id' => $school->id],
            [
                'intro' => 'A student-centered public university committed to inclusive, industry-aligned education and community impact.',
                'tag_primary' => 'Public higher-education institution',
                'tag_neutral' => 'Digital campus ready',
                'tag_accent' => 'Scholarships & support programs',
                'fact1_label' => 'Estimated founded',
                'fact1_value' => '1995',
                'fact1_caption' => 'Decades of academic excellence',
                'fact2_label' => 'Approx. students',
                'fact2_value' => '8,500+',
                'fact2_caption' => 'Undergraduate & graduate learners',
                'fact3_label' => 'Programs offered',
                'fact3_value' => '60+',
                'fact3_caption' => 'Across colleges and departments',
                'campus_title' => 'Campus life & student support',
                'campus_bullet1' => 'Modern smart classrooms, laboratories, and a comprehensive learning resource center.',
                'campus_bullet2' => 'Active student organizations, athletics, and cultural affairs office.',
                'campus_bullet3' => 'Scholarship and financial assistance programs for qualified students.',
                'campus_bullet4' => 'Career & placement services to support internships and graduate employability.',
            ],
        );

        SuperAdminUser::query()->firstOrCreate([
            'email' => 'root@platform.local',
        ], [
            'full_name' => 'Root Super Admin',
            'password_hash' => Hash::make('SuperAdmin123!'),
            'status' => 'active',
        ]);

        $tenantUsers = [
            ['full_name' => 'Demo School Admin', 'email' => 'schooladmin@demo-u.local', 'password' => 'Tenant123!', 'role' => 'school_admin'],
            ['full_name' => 'Demo Finance Staff', 'email' => 'finance@demo-u.local', 'password' => 'Tenant123!', 'role' => 'finance_staff'],
            ['full_name' => 'Demo Registrar Staff', 'email' => 'registrar@demo-u.local', 'password' => 'Tenant123!', 'role' => 'registrar_staff'],
            ['full_name' => 'Demo Dean', 'email' => 'dean@demo-u.local', 'password' => 'Tenant123!', 'role' => 'dean'],
            ['full_name' => 'Demo Teacher', 'email' => 'teacher@demo-u.local', 'password' => 'Tenant123!', 'role' => 'teacher'],
            ['full_name' => 'Demo Student', 'email' => 'student@demo-u.local', 'password' => 'Tenant123!', 'role' => 'student'],
        ];

        foreach ($tenantUsers as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'full_name' => $data['full_name'],
                    'password_hash' => Hash::make($data['password']),
                    'status' => 'active',
                ],
            );

            $roleId = DB::table('roles')->where('code', $data['role'])->value('id');
            if ($roleId === null) {
                continue;
            }

            UserRole::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'role_id' => (int) $roleId,
                ],
                [
                    'is_active' => true,
                    'assigned_at' => now(),
                ],
            );
        }
    }
}
