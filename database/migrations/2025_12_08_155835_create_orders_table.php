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
       Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // Shopify Reference
        $table->unsignedBigInteger('shopify_order_id')->unique();
        $table->string('order_number')->nullable();
        
        // Customer Information
        $table->string('customer_name')->nullable();
        $table->string('customer_email')->nullable();
        
        // Order Pricing
        $table->decimal('total_price', 10, 2)->nullable();
        $table->decimal('subtotal_price', 10, 2)->nullable();
        $table->decimal('total_tax', 10, 2)->nullable();
        $table->decimal('total_discounts', 10, 2)->nullable();
        $table->decimal('total_line_items_price', 10, 2)->nullable();
        $table->decimal('total_price_usd', 10, 2)->nullable();
        $table->decimal('total_shipping_price', 10, 2)->nullable();
        $table->decimal('total_refunded', 10, 2)->default(0);
        
        // Currency
        $table->string('currency', 10)->nullable();
        $table->string('presentment_currency')->nullable();
        
        // Status
        $table->string('financial_status')->nullable();
        $table->string('fulfillment_status')->nullable();
        $table->string('processing_method')->nullable();
        
        // JSON Data
        $table->json('billing_address')->nullable();
        $table->json('shipping_address')->nullable();
        $table->json('line_items')->nullable();
        $table->json('total_price_set')->nullable();
        $table->json('subtotal_price_set')->nullable();
        $table->json('total_discounts_set')->nullable();
        $table->json('total_shipping_price_set')->nullable();
        $table->json('total_tax_set')->nullable();
        
        // Webhook Tracking
        $table->string('webhook_id')->nullable();
        $table->timestamp('webhook_received_at')->nullable();
        // Discount percentage | fixed
        $table->string('discount_type')->nullable();  
        $table->decimal('discount_value', 10, 2)->nullable();
        // Timestamps
        $table->timestamp('shopify_created_at')->nullable();
        $table->timestamp('shopify_updated_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->timestamp('closed_at')->nullable();
        $table->timestamp('synced_at')->nullable();
        
        $table->timestamps();
        
        // Indexes
        $table->index('shopify_order_id');
        $table->index('order_number');
        $table->index('customer_email');
        $table->index('financial_status');
        $table->index('fulfillment_status');
        $table->index(['shopify_created_at', 'shopify_updated_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
