<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->string('name');
            $table->enum('type', ['amount', 'percentage'])->default('amount');
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('placement', ['regular', 'admission', 'all'])->default('regular');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->string('name');
            $table->enum('type', ['amount', 'percentage'])->default('amount');
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('placement', ['regular', 'admission', 'all'])->default('regular');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('student_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('student_user_id')->index();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'student_user_id']);
        });

        Schema::create('cashier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('student_user_id')->index();
            $table->unsignedBigInteger('billing_id')->nullable()->index();
            $table->string('transaction_id')->unique();
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['cash', 'card', 'bank_transfer', 'wallet', 'other'])->default('cash');
            $table->string('reference_no')->nullable();
            $table->enum('status', ['completed', 'pending', 'cancelled', 'refunded'])->default('completed');
            $table->unsignedBigInteger('processed_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_payments');
        Schema::dropIfExists('student_wallets');
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('discounts');
    }
};
