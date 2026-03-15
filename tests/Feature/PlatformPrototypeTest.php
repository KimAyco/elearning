<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\School;
use App\Models\SuperAdminUser;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PlatformPrototypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_lists_only_active_schools_and_has_no_admin_link(): void
    {
        School::query()->create([
            'school_code' => 'ACTIVE-U',
            'name' => 'Active University',
            'short_description' => 'Visible school.',
            'status' => 'active',
            'subscription_state' => 'active',
        ]);

        School::query()->create([
            'school_code' => 'HOLD-U',
            'name' => 'Suspended University',
            'short_description' => 'Should not be visible.',
            'status' => 'suspended',
            'subscription_state' => 'past_due',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Active University');
        $response->assertDontSee('Suspended University');
        $response->assertDontSee('/superadmin/login');
    }

    public function test_super_admin_login_is_isolated_from_tenant_api_context(): void
    {
        SuperAdminUser::query()->create([
            'full_name' => 'Root SA',
            'email' => 'root@example.com',
            'password_hash' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $loginResponse = $this->post('/superadmin/login', [
            'email' => 'root@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/superadmin/schools');

        $this->get('/superadmin/schools')->assertOk();
        $this->getJson('/api/tenant/enrollments/mine')->assertStatus(401);
    }

    public function test_tenant_login_stores_school_context_and_roles(): void
    {
        $this->seed(RbacSeeder::class);

        $school = School::query()->create([
            'school_code' => 'TENANT-U',
            'name' => 'Tenant University',
            'short_description' => 'Tenant scope test',
            'status' => 'active',
            'subscription_state' => 'active',
        ]);

        $user = User::query()->create([
            'full_name' => 'Student User',
            'email' => 'student@example.com',
            'password_hash' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $studentRoleId = Role::query()->where('code', 'student')->value('id');
        UserRole::query()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'role_id' => $studentRoleId,
            'is_active' => true,
            'assigned_at' => now(),
        ]);

        $response = $this->post('/login', [
            'school_code' => 'TENANT-U',
            'email' => 'student@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/tenant/dashboard');
        $this->getJson('/tenant/session')
            ->assertOk()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.active_school_id', $school->id);
    }

    public function test_student_role_cannot_access_finance_endpoint(): void
    {
        $this->seed(RbacSeeder::class);

        $school = School::query()->create([
            'school_code' => 'DENY-U',
            'name' => 'Denied University',
            'short_description' => 'RBAC deny test',
            'status' => 'active',
            'subscription_state' => 'active',
        ]);

        $user = User::query()->create([
            'full_name' => 'Student User',
            'email' => 'deny-student@example.com',
            'password_hash' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $studentRoleId = Role::query()->where('code', 'student')->value('id');
        UserRole::query()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'role_id' => $studentRoleId,
            'is_active' => true,
            'assigned_at' => now(),
        ]);

        $this->withSession([
            'user_id' => $user->id,
            'active_school_id' => $school->id,
            'role_codes' => ['student'],
        ])->postJson('/api/tenant/billing/rules', [])
            ->assertStatus(403);
    }
}
