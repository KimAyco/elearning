<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_code', 30)->unique();
            $table->string('name', 150);
            $table->string('short_description', 255);
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->enum('subscription_state', ['trial', 'active', 'past_due', 'expired', 'cancelled'])->default('trial');
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('subscription_state');
        });

        Schema::create('super_admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 120);
            $table->string('email', 190)->unique();
            $table->string('password_hash');
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'email']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 80);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('module', 40);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index(['module', 'code']);
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'school_id', 'role_id']);
            $table->index(['school_id', 'role_id', 'is_active']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->string('name', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planned', 'active', 'closed'])->default('planned');
            $table->timestamps();

            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'status']);
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->enum('term_code', ['1ST', '2ND', 'SUMMER']);
            $table->string('name', 30);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planned', 'enrollment_open', 'in_progress', 'closed'])->default('planned');
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id', 'term_code']);
            $table->index(['school_id', 'status']);
        });

        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->string('code', 20);
            $table->string('name', 150);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'status']);
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('college_id')->constrained('colleges')->onDelete('restrict');
            $table->string('code', 20);
            $table->string('name', 150);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['school_id', 'college_id', 'code']);
            $table->index(['school_id', 'college_id']);
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->string('code', 20);
            $table->string('name', 150);
            $table->enum('degree_level', ['certificate', 'diploma', 'bachelor', 'master', 'doctorate']);
            $table->decimal('max_units_per_semester', 4, 1)->default(24.0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'department_id']);
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('code', 20);
            $table->string('title', 200);
            $table->decimal('units', 4, 1);
            $table->decimal('weekly_hours', 4, 1);
            $table->unsignedTinyInteger('duration_weeks');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'department_id']);
        });

        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('prerequisite_subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->primary(['school_id', 'subject_id', 'prerequisite_subject_id']);
        });

        Schema::create('subject_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->foreignId('assigned_teacher_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('schedule_summary', 255)->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'cancelled'])->default('draft');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['school_id', 'semester_id', 'status']);
            $table->index(['school_id', 'assigned_teacher_user_id']);
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('subject_offering_id')->constrained('subject_offerings')->onDelete('restrict');
            $table->string('identifier', 20);
            $table->unsignedInteger('max_capacity');
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');
            $table->timestamps();

            $table->unique(['school_id', 'subject_offering_id', 'identifier']);
            $table->index(['school_id', 'subject_offering_id', 'status']);
        });

        Schema::create('section_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->index(['school_id', 'day_of_week', 'start_time', 'end_time'], 'section_sched_time_idx');
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('student_no', 40);
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->unsignedTinyInteger('year_level');
            $table->timestamps();

            $table->unique(['school_id', 'student_no']);
            $table->unique(['school_id', 'user_id']);
            $table->index(['school_id', 'program_id']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->foreignId('student_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('subject_offering_id')->constrained('subject_offerings')->onDelete('restrict');
            $table->foreignId('section_id')->constrained('sections')->onDelete('restrict');
            $table->enum('status', ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled', 'dropped', 'cancelled', 'rejected'])->default('selected');
            $table->string('validation_remarks', 255)->nullable();
            $table->foreignId('confirmed_by_registrar_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('dropped_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'semester_id', 'student_user_id', 'subject_offering_id'], 'enroll_unique_student_offer');
            $table->index(['school_id', 'student_user_id', 'semester_id', 'status']);
            $table->index(['section_id', 'status']);
        });

        Schema::create('billing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->enum('charge_type', ['tuition', 'misc_fee', 'lab_fee', 'penalty', 'other']);
            $table->foreignId('billing_category_id')->nullable()->constrained('billing_categories')->nullOnDelete();
            $table->enum('scope_type', ['program', 'department', 'section', 'student']);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('description', 255);
            $table->decimal('amount', 12, 2);
            $table->boolean('is_required')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by_finance_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['school_id', 'semester_id', 'status']);
            $table->index(['school_id', 'scope_type', 'scope_id']);
        });

        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->foreignId('student_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->enum('charge_type', ['tuition', 'misc_fee', 'lab_fee', 'penalty', 'other']);
            $table->enum('scope_type', ['program', 'department', 'section', 'student']);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('description', 255);
            $table->decimal('amount_due', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid_unverified', 'verified', 'waived', 'void'])->default('unpaid');
            $table->enum('clearance_status', ['not_cleared', 'cleared', 'revoked'])->default('not_cleared');
            $table->foreignId('generated_by_finance_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('verified_by_finance_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cleared_by_finance_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'student_user_id', 'semester_id', 'payment_status'], 'billing_student_sem_pay_idx');
            $table->index(['school_id', 'scope_type', 'scope_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('billing_id')->constrained('billing')->onDelete('restrict');
            $table->foreignId('student_user_id')->constrained('users')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->string('reference_no', 80)->nullable();
            $table->enum('status', ['submitted', 'verified', 'rejected', 'void'])->default('submitted');
            $table->string('remarks', 255)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by_finance_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'billing_id', 'status']);
            $table->index(['school_id', 'student_user_id', 'status']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->onDelete('restrict');
            $table->foreignId('student_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('subject_offering_id')->constrained('subject_offerings')->onDelete('restrict');
            $table->foreignId('section_id')->constrained('sections')->onDelete('restrict');
            $table->string('grade_value', 10)->nullable();
            $table->enum('status', ['draft', 'submitted', 'dean_approved', 'dean_rejected', 'registrar_finalized', 'released'])->default('draft');
            $table->foreignId('teacher_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('submitted_at')->nullable();
            $table->string('submitted_remarks', 255)->nullable();
            $table->foreignId('dean_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dean_decision_remarks', 255)->nullable();
            $table->timestamp('dean_decided_at')->nullable();
            $table->foreignId('registrar_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'student_user_id', 'status']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_super_admin_id')->nullable()->constrained('super_admin_users')->nullOnDelete();
            $table->string('actor_role_code', 40)->nullable();
            $table->string('action', 80);
            $table->string('entity_type', 80);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->char('request_id', 36)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('created_at', 6);

            $table->index(['school_id', 'created_at']);
            $table->index(['entity_type', 'entity_id', 'created_at']);
            $table->index(['actor_user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE subjects ADD CONSTRAINT chk_subjects_units_positive CHECK (units > 0)');
            DB::statement('ALTER TABLE subjects ADD CONSTRAINT chk_subjects_weekly_hours_positive CHECK (weekly_hours > 0)');
            DB::statement('ALTER TABLE subjects ADD CONSTRAINT chk_subjects_duration_positive CHECK (duration_weeks > 0)');
            DB::statement('ALTER TABLE sections ADD CONSTRAINT chk_sections_capacity_positive CHECK (max_capacity > 0)');
            DB::statement('ALTER TABLE subject_prerequisites ADD CONSTRAINT chk_subject_prereq_not_self CHECK (subject_id <> prerequisite_subject_id)');
            DB::statement('ALTER TABLE section_schedules ADD CONSTRAINT chk_section_day_valid CHECK (day_of_week BETWEEN 1 AND 7)');
            DB::statement('ALTER TABLE section_schedules ADD CONSTRAINT chk_section_time_order CHECK (start_time < end_time)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('billing');
        Schema::dropIfExists('billing_rules');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('section_schedules');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('subject_offerings');
        Schema::dropIfExists('subject_prerequisites');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('super_admin_users');
        Schema::dropIfExists('schools');
    }
};
