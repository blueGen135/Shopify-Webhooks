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
        Schema::create('shopify_customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shopify_customer_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('verified_email')->default(false);
            $table->string('state')->nullable(); // enabled, disabled, invited

            $table->integer('orders_count')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->boolean('accepts_marketing')->default(false);
            // JSON fields
            $table->json('addresses')->nullable();
            $table->json('default_address')->nullable();
            $table->json('raw_response')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_customers');
    }
};
