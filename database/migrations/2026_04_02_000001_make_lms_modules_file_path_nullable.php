<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE lms_modules MODIFY file_path VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('UPDATE lms_modules SET file_path = "" WHERE file_path IS NULL');
            DB::statement('ALTER TABLE lms_modules MODIFY file_path VARCHAR(255) NOT NULL');
        }
    }
};
