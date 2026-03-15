<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CurriculumPrerequisiteValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_add_subject_when_prerequisite_is_not_in_previous_semester(): void
    {
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
        URL::forceScheme('http');
        $this->seed(RbacSeeder::class);

        $school = School::query()->create([
            'school_code' => 'PREREQ-U',
            'name' => 'Prereq University',
            'short_description' => 'Prerequisite validation test school',
            'status' => 'active',
            'subscription_state' => 'active',
        ]);

        $adminUser = User::query()->create([
            'full_name' => 'School Admin',
            'email' => 'school-admin@example.com',
            'password_hash' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $schoolAdminRoleId = (int) Role::query()->where('code', 'school_admin')->value('id');
        UserRole::query()->create([
            'user_id' => (int) $adminUser->id,
            'school_id' => (int) $school->id,
            'role_id' => $schoolAdminRoleId,
            'is_active' => true,
            'assigned_at' => now(),
        ]);

        $college = College::query()->create([
            'school_id' => (int) $school->id,
            'code' => 'CCS',
            'name' => 'College of Computing',
            'status' => 'active',
        ]);

        $department = Department::query()->create([
            'school_id' => (int) $school->id,
            'college_id' => (int) $college->id,
            'code' => 'CS',
            'name' => 'Computer Science',
            'status' => 'active',
        ]);

        $program = Program::query()->create([
            'school_id' => (int) $school->id,
            'department_id' => (int) $department->id,
            'code' => 'BSCS',
            'name' => 'BS Computer Science',
            'degree_level' => 'bachelor',
            'max_units_per_semester' => 24.0,
            'status' => 'active',
        ]);

        $academicYear = AcademicYear::query()->create([
            'school_id' => (int) $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-05-31',
            'status' => 'active',
        ]);

        $firstSemester = Semester::query()->create([
            'school_id' => (int) $school->id,
            'academic_year_id' => (int) $academicYear->id,
            'term_code' => '1ST',
            'name' => 'First Semester',
            'start_date' => '2026-06-01',
            'end_date' => '2026-10-31',
            'status' => 'planned',
        ]);

        $secondSemester = Semester::query()->create([
            'school_id' => (int) $school->id,
            'academic_year_id' => (int) $academicYear->id,
            'term_code' => '2ND',
            'name' => 'Second Semester',
            'start_date' => '2026-11-01',
            'end_date' => '2027-03-31',
            'status' => 'planned',
        ]);

        $introSubject = Subject::query()->create([
            'school_id' => (int) $school->id,
            'department_id' => (int) $department->id,
            'code' => 'CS101',
            'title' => 'Intro to Computing',
            'units' => 3,
            'weekly_hours' => 3,
            'duration_weeks' => 18,
            'status' => 'active',
        ]);

        $advancedSubject = Subject::query()->create([
            'school_id' => (int) $school->id,
            'department_id' => (int) $department->id,
            'code' => 'CS201',
            'title' => 'Data Structures',
            'units' => 3,
            'weekly_hours' => 3,
            'duration_weeks' => 18,
            'status' => 'active',
        ]);

        DB::table('subject_prerequisites')->insert([
            'school_id' => (int) $school->id,
            'subject_id' => (int) $advancedSubject->id,
            'prerequisite_subject_id' => (int) $introSubject->id,
        ]);

        $session = [
            'user_id' => (int) $adminUser->id,
            'active_school_id' => (int) $school->id,
        ];
        $curriculumItemsPath = '/tenant/admin/curriculum-items';

        $failedAddResponse = $this->withSession($session)->post($curriculumItemsPath, [
            'program_id' => (int) $program->id,
            'semester_id' => (int) $firstSemester->id,
            'subject_id' => (int) $advancedSubject->id,
            'year_level' => 1,
        ]);

        if ($failedAddResponse->status() === 422) {
            $failedAddResponse->assertJsonValidationErrors('curriculum');
        } else {
            $failedAddResponse->assertSessionHasErrors('curriculum');
        }

        $this->assertDatabaseMissing('program_curriculum_items', [
            'school_id' => (int) $school->id,
            'program_id' => (int) $program->id,
            'subject_id' => (int) $advancedSubject->id,
            'year_level' => 1,
        ]);

        $this->withSession($session)->post($curriculumItemsPath, [
            'program_id' => (int) $program->id,
            'semester_id' => (int) $firstSemester->id,
            'subject_id' => (int) $introSubject->id,
            'year_level' => 1,
        ])->assertSessionHasNoErrors();

        $this->withSession($session)->post($curriculumItemsPath, [
            'program_id' => (int) $program->id,
            'semester_id' => (int) $secondSemester->id,
            'subject_id' => (int) $advancedSubject->id,
            'year_level' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('program_curriculum_items', [
            'school_id' => (int) $school->id,
            'program_id' => (int) $program->id,
            'semester_id' => (int) $secondSemester->id,
            'subject_id' => (int) $advancedSubject->id,
            'year_level' => 1,
        ]);
    }
}
