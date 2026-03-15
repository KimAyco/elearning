<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL doesn't support ALTER ENUM directly, so we need to modify the column
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE billing MODIFY COLUMN clearance_status ENUM('not_cleared', 'pending_approval', 'cleared', 'revoked') DEFAULT 'not_cleared'");
        } else {
            // For other databases, use standard ALTER TABLE
            Schema::table('billing', function (Blueprint $table) {
                $table->enum('clearance_status', ['not_cleared', 'pending_approval', 'cleared', 'revoked'])
                    ->default('not_cleared')
                    ->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE billing MODIFY COLUMN clearance_status ENUM('not_cleared', 'cleared', 'revoked') DEFAULT 'not_cleared'");
        } else {
            Schema::table('billing', function (Blueprint $table) {
                $table->enum('clearance_status', ['not_cleared', 'cleared', 'revoked'])
                    ->default('not_cleared')
                    ->change();
            });
        }
    }
};
