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
        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->decimal('price_per_course_unit', 12, 2)
                ->nullable()
                ->after('enrollment_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_fee_settings', function (Blueprint $table): void {
            $table->dropColumn('price_per_course_unit');
        });
    }
};

