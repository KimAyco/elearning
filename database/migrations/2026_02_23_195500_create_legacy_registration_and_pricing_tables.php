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
        if (! Schema::hasTable('payment_plans')) {
            Schema::create('payment_plans', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('name');
                $table->integer('months');
                $table->decimal('price_per_month', 12, 2)->default(0);
                $table->decimal('total_price', 12, 2)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table): void {
                $table->increments('id');
                $table->decimal('price_per_month', 12, 2)->default(0);
                $table->boolean('auto_approve_after_payment')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('school_registrations')) {
            Schema::create('school_registrations', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('subdomain')->nullable();
                $table->integer('plan_months')->default(1);
                $table->string('status')->default('pending');
                $table->boolean('auto_approved')->default(false);
                $table->text('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_registrations');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('payment_plans');
    }
};

