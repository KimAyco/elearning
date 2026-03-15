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
            if (! Schema::hasColumn('billing_rules', 'billing_category_id')) {
                $table->foreignId('billing_category_id')
                    ->nullable()
                    ->after('charge_type')
                    ->constrained('billing_categories')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_rules', function (Blueprint $table) {
            if (Schema::hasColumn('billing_rules', 'billing_category_id')) {
                $table->dropConstrainedForeignId('billing_category_id');
            }
        });
    }
};
