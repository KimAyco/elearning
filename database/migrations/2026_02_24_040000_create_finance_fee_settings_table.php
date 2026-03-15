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
        Schema::create('finance_fee_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->enum('scope_type', ['semester', 'academic_year', 'program']);
            $table->unsignedBigInteger('scope_id');
            $table->decimal('enrollment_fee', 12, 2)->default(0);
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'scope_type', 'scope_id', 'status'], 'finance_fee_settings_scope_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_fee_settings');
    }
};

