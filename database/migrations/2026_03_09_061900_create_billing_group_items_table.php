<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // In case a previous failed migration left a partial table behind.
        Schema::dropIfExists('billing_group_items');

        Schema::create('billing_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_group_id')->constrained('billing_groups')->cascadeOnDelete();
            $table->foreignId('billing_rule_id')->constrained('billing_rules')->restrictOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['billing_group_id', 'billing_rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_group_items');
    }
};

