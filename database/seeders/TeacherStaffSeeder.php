<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherStaffSeeder extends Seeder
{
    /**
     * Seed teacher/staff user accounts and random allowed subjects.
     */
    public function run(): void
    {
        $school = School::query()->first();
        if ($school === null) {
            return;
        }

        $teacherRoleId = DB::table('roles')->where('code', 'teacher')->value('id');
        if ($teacherRoleId === null) {
            return;
        }

        $seedPassword = 'password';

        $teachers = [
            ['full_name' => 'jake doe', 'email' => 'jake@gmail.com', 'phone' => '+639111000001'],
            ['full_name' => 'nina cruz', 'email' => 'nina@gmail.com', 'phone' => '+639111000002'],
            ['full_name' => 'paul lim', 'email' => 'paul@gmail.com', 'phone' => '+639111000003'],
            ['full_name' => 'ella santos', 'email' => 'ella@gmail.com', 'phone' => '+639111000004'],
            ['full_name' => 'mark reyes', 'email' => 'mark@gmail.com', 'phone' => '+639111000005'],
            ['full_name' => 'ivy tan', 'email' => 'ivy@gmail.com', 'phone' => '+639111000006'],
        ];

        $subjectIds = DB::table('subjects')
            ->where('school_id', (int) $school->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($teachers as $teacher) {
            $user = User::query()->updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'full_name' => $teacher['full_name'],
                    'password_hash' => Hash::make($seedPassword),
                    'status' => 'active',
                    'phone' => $teacher['phone'],
                ]
            );

            UserRole::query()->updateOrCreate(
                [
                    'user_id' => (int) $user->id,
                    'school_id' => (int) $school->id,
                    'role_id' => (int) $teacherRoleId,
                ],
                [
                    'is_active' => true,
                    'assigned_by_user_id' => null,
                    'assigned_at' => now(),
                ]
            );

            DB::table('teacher_subjects')
                ->where('school_id', (int) $school->id)
                ->where('teacher_user_id', (int) $user->id)
                ->delete();

            if ($subjectIds === []) {
                continue;
            }

            $maxPick = min(6, count($subjectIds));
            $pickCount = max(2, min($maxPick, random_int(2, $maxPick)));
            $picked = collect($subjectIds)->shuffle()->take($pickCount)->values()->all();

            $rows = array_map(function (int $subjectId) use ($school, $user): array {
                return [
                    'school_id' => (int) $school->id,
                    'teacher_user_id' => (int) $user->id,
                    'subject_id' => $subjectId,
                    'assigned_by_user_id' => null,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $picked);

            DB::table('teacher_subjects')->insert($rows);
        }
    }
}

