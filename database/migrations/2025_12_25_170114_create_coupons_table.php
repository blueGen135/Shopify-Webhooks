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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_price_rule_id')->nullable()->index();
            $table->string('shopify_discount_code_id')->nullable()->index();
            $table->string('code')->unique();
            $table->string('title');
            $table->enum('value_type', ['percentage', 'fixed_amount']);
            $table->decimal('value', 10, 2);
            $table->enum('target_type', ['line_item', 'shipping_line'])->default('line_item');
            $table->enum('target_selection', ['all', 'entitled'])->default('all');
            $table->enum('allocation_method', ['each', 'across'])->default('across');
            $table->integer('usage_limit')->nullable();
            $table->integer('times_used')->default(0);
            $table->enum('customer_selection', ['all', 'prerequisite'])->default('all');
            $table->boolean('once_per_customer')->default(false);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('raw_price_rule')->nullable();
            $table->json('raw_discount_code')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
