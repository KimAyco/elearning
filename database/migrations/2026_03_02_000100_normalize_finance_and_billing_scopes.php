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
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->foreignId('semester_id')->nullable()->after('school_id')->constrained('semesters')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->after('semester_id')->constrained('academic_years')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->after('academic_year_id')->constrained('programs')->nullOnDelete();
        });

        DB::statement(<<<'SQL'
UPDATE finance_fee_settings
SET
    semester_id = CASE
        WHEN scope_type = 'semester' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END,
    academic_year_id = CASE
        WHEN scope_type = 'academic_year' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END,
    program_id = CASE
        WHEN scope_type = 'program' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END
SQL);

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->index(['school_id', 'status', 'semester_id'], 'finance_fee_settings_semester_idx');
            $table->index(['school_id', 'status', 'academic_year_id'], 'finance_fee_settings_acyear_idx');
            $table->index(['school_id', 'status', 'program_id'], 'finance_fee_settings_program_idx');
        });

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->foreignId('program_id')->nullable()->after('semester_id')->constrained('programs')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('program_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('department_id')->constrained('sections')->nullOnDelete();
            $table->foreignId('scope_student_user_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();
        });

        DB::statement(<<<'SQL'
UPDATE billing_rules
SET
    program_id = CASE
        WHEN scope_type = 'program' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END,
    department_id = CASE
        WHEN scope_type = 'department' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END,
    section_id = CASE
        WHEN scope_type = 'section' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END,
    scope_student_user_id = CASE
        WHEN scope_type = 'student' AND scope_id > 0 THEN scope_id
        ELSE NULL
    END
SQL);

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->index(['school_id', 'semester_id', 'status', 'program_id'], 'billing_rules_program_idx');
            $table->index(['school_id', 'semester_id', 'status', 'department_id'], 'billing_rules_department_idx');
            $table->index(['school_id', 'semester_id', 'status', 'section_id'], 'billing_rules_section_idx');
            $table->index(['school_id', 'semester_id', 'status', 'scope_student_user_id'], 'billing_rules_student_idx');
        });

        Schema::table('billing', function (Blueprint $table): void {
            $table->foreignId('billing_rule_id')->nullable()->after('enrollment_id')->constrained('billing_rules')->nullOnDelete();
        });

        if ($driver === 'sqlite') {
            $rows = DB::table('billing')
                ->select(['id', 'school_id', 'semester_id', 'charge_type', 'description', 'scope_type', 'scope_id'])
                ->whereNull('billing_rule_id')
                ->get();

            foreach ($rows as $row) {
                $ruleQuery = DB::table('billing_rules')
                    ->where('school_id', (int) $row->school_id)
                    ->where('semester_id', (int) $row->semester_id)
                    ->where('charge_type', (string) $row->charge_type)
                    ->whereRaw("COALESCE(description, '') = ?", [(string) ($row->description ?? '')]);

                $scopeId = (int) ($row->scope_id ?? 0);
                $scopeType = (string) ($row->scope_type ?? '');

                $ruleQuery = match ($scopeType) {
                    'program' => $ruleQuery->where('program_id', $scopeId),
                    'department' => $ruleQuery->where('department_id', $scopeId),
                    'section' => $ruleQuery->where('section_id', $scopeId),
                    'student' => $ruleQuery->where('scope_student_user_id', $scopeId),
                    default => $ruleQuery->whereRaw('1 = 0'),
                };

                $ruleId = $ruleQuery->orderBy('id')->value('id');
                if ($ruleId !== null) {
                    DB::table('billing')
                        ->where('id', (int) $row->id)
                        ->update(['billing_rule_id' => (int) $ruleId]);
                }
            }
        } else {
            DB::statement(<<<'SQL'
UPDATE billing AS b
JOIN (
    SELECT
        b2.id AS billing_id,
        MIN(r.id) AS rule_id
    FROM billing AS b2
    JOIN billing_rules AS r
        ON r.school_id = b2.school_id
        AND r.semester_id = b2.semester_id
        AND r.charge_type = b2.charge_type
        AND COALESCE(r.description, '') = COALESCE(b2.description, '')
        AND (
            (b2.scope_type = 'program' AND r.program_id = b2.scope_id)
            OR (b2.scope_type = 'department' AND r.department_id = b2.scope_id)
            OR (b2.scope_type = 'section' AND r.section_id = b2.scope_id)
            OR (b2.scope_type = 'student' AND r.scope_student_user_id = b2.scope_id)
        )
    GROUP BY b2.id
) AS x
    ON x.billing_id = b.id
SET b.billing_rule_id = x.rule_id
WHERE b.billing_rule_id IS NULL
SQL);
        }

        Schema::table('billing', function (Blueprint $table): void {
            $table->index(['school_id', 'billing_rule_id'], 'billing_rule_lookup_idx');
        });

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->dropIndex('finance_fee_settings_scope_idx');
        });

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->dropIndex('billing_rules_school_id_scope_type_scope_id_index');
        });

        Schema::table('billing', function (Blueprint $table): void {
            $table->dropIndex('billing_school_id_scope_type_scope_id_index');
        });

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->dropColumn(['scope_type', 'scope_id']);
        });

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->dropColumn(['scope_type', 'scope_id']);
        });

        Schema::table('billing', function (Blueprint $table): void {
            $table->dropColumn(['scope_type', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->enum('scope_type', ['semester', 'academic_year', 'program'])->default('program')->after('school_id');
            $table->unsignedBigInteger('scope_id')->default(0)->after('scope_type');
        });

        DB::statement(<<<'SQL'
UPDATE finance_fee_settings
SET
    scope_type = CASE
        WHEN semester_id IS NOT NULL THEN 'semester'
        WHEN academic_year_id IS NOT NULL THEN 'academic_year'
        ELSE 'program'
    END,
    scope_id = CASE
        WHEN semester_id IS NOT NULL THEN semester_id
        WHEN academic_year_id IS NOT NULL THEN academic_year_id
        WHEN program_id IS NOT NULL THEN program_id
        ELSE 0
    END
SQL);

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->enum('scope_type', ['program', 'department', 'section', 'student'])->default('student')->after('charge_type');
            $table->unsignedBigInteger('scope_id')->nullable()->after('scope_type');
        });

        DB::statement(<<<'SQL'
UPDATE billing_rules
SET
    scope_type = CASE
        WHEN program_id IS NOT NULL THEN 'program'
        WHEN department_id IS NOT NULL THEN 'department'
        WHEN section_id IS NOT NULL THEN 'section'
        ELSE 'student'
    END,
    scope_id = CASE
        WHEN program_id IS NOT NULL THEN program_id
        WHEN department_id IS NOT NULL THEN department_id
        WHEN section_id IS NOT NULL THEN section_id
        ELSE scope_student_user_id
    END
SQL);

        Schema::table('billing', function (Blueprint $table): void {
            $table->enum('scope_type', ['program', 'department', 'section', 'student'])->default('student')->after('charge_type');
            $table->unsignedBigInteger('scope_id')->nullable()->after('scope_type');
        });

        if ($driver === 'sqlite') {
            $rows = DB::table('billing')
                ->select(['id', 'student_user_id', 'billing_rule_id'])
                ->get();

            foreach ($rows as $row) {
                $scopeType = 'student';
                $scopeId = (int) $row->student_user_id;

                if ($row->billing_rule_id !== null) {
                    $rule = DB::table('billing_rules')
                        ->select(['program_id', 'department_id', 'section_id', 'scope_student_user_id'])
                        ->where('id', (int) $row->billing_rule_id)
                        ->first();

                    if ($rule !== null) {
                        if ($rule->program_id !== null) {
                            $scopeType = 'program';
                            $scopeId = (int) $rule->program_id;
                        } elseif ($rule->department_id !== null) {
                            $scopeType = 'department';
                            $scopeId = (int) $rule->department_id;
                        } elseif ($rule->section_id !== null) {
                            $scopeType = 'section';
                            $scopeId = (int) $rule->section_id;
                        } elseif ($rule->scope_student_user_id !== null) {
                            $scopeType = 'student';
                            $scopeId = (int) $rule->scope_student_user_id;
                        }
                    }
                }

                DB::table('billing')
                    ->where('id', (int) $row->id)
                    ->update([
                        'scope_type' => $scopeType,
                        'scope_id' => $scopeId,
                    ]);
            }
        } else {
            DB::statement(<<<'SQL'
UPDATE billing AS b
LEFT JOIN billing_rules AS r
    ON r.id = b.billing_rule_id
SET
    b.scope_type = CASE
        WHEN r.program_id IS NOT NULL THEN 'program'
        WHEN r.department_id IS NOT NULL THEN 'department'
        WHEN r.section_id IS NOT NULL THEN 'section'
        ELSE 'student'
    END,
    b.scope_id = CASE
        WHEN r.program_id IS NOT NULL THEN r.program_id
        WHEN r.department_id IS NOT NULL THEN r.department_id
        WHEN r.section_id IS NOT NULL THEN r.section_id
        ELSE b.student_user_id
    END
SQL);
        }

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->index(['school_id', 'scope_type', 'scope_id'], 'finance_fee_settings_scope_idx');
        });

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->index(['school_id', 'scope_type', 'scope_id'], 'billing_rules_school_id_scope_type_scope_id_index');
        });

        Schema::table('billing', function (Blueprint $table): void {
            $table->index(['school_id', 'scope_type', 'scope_id'], 'billing_school_id_scope_type_scope_id_index');
        });

        Schema::table('billing', function (Blueprint $table): void {
            $table->dropIndex('billing_rule_lookup_idx');
            $table->dropConstrainedForeignId('billing_rule_id');
        });

        Schema::table('billing_rules', function (Blueprint $table): void {
            $table->dropIndex('billing_rules_program_idx');
            $table->dropIndex('billing_rules_department_idx');
            $table->dropIndex('billing_rules_section_idx');
            $table->dropIndex('billing_rules_student_idx');
            $table->dropConstrainedForeignId('scope_student_user_id');
            $table->dropConstrainedForeignId('section_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->dropIndex('finance_fee_settings_semester_idx');
            $table->dropIndex('finance_fee_settings_acyear_idx');
            $table->dropIndex('finance_fee_settings_program_idx');
            $table->dropConstrainedForeignId('program_id');
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropConstrainedForeignId('semester_id');
        });
    }
};
