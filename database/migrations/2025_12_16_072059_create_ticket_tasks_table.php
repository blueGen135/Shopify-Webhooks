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
        Schema::create('ticket_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'verification', 'inventory_check', 'close_ticket'
            $table->string('status')->default('pending'); // e.g., 'pending', 'in_progress', 'completed'
            $table->json('sub_tasks')->nullable(); // Flexible JSON for task-specific sub-tasks with custom fields
            $table->integer('order')->default(0); // For ordering tasks
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_tasks');
    }
};
