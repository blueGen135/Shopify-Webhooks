<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., 'image_verification', 'inventory_check', 'fedex_link_generated'
            $table->string('title')->nullable(); // Human-readable title
            $table->string('status')->nullable(); // e.g., 'pending', 'completed', 'failed'
            $table->json('meta_data')->nullable(); // Flexible storage for action-specific data
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_activity_logs');
    }
};
