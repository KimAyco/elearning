<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->foreignId('approved_by_registrar_user_id')
                ->nullable()
                ->after('cleared_by_finance_user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')
                ->nullable()
                ->after('cleared_at');
        });
    }

    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropForeign(['approved_by_registrar_user_id']);
            $table->dropColumn(['approved_by_registrar_user_id', 'approved_at']);
        });
    }
};
