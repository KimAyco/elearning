<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billing_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('billing_rules', 'year_level')) {
                $table->unsignedTinyInteger('year_level')
                    ->nullable()
                    ->after('billing_category_id');
                $table->index(['school_id', 'semester_id', 'status', 'program_id', 'year_level'], 'billing_rules_program_year_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_rules', function (Blueprint $table) {
            if (Schema::hasColumn('billing_rules', 'year_level')) {
                $table->dropIndex('billing_rules_program_year_idx');
                $table->dropColumn('year_level');
            }
        });
    }
};
