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
        Schema::create('product_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('ticket_tasks')->onDelete('cascade');
            $table->unsignedBigInteger('product_id'); // Order product ID
            $table->string('action')->nullable(); // e.g., 'verified', 'not_verified', 'ready_for_replacement', 'full_refund', 'partial_refund', 'wait_for_restock'
            $table->json('details')->nullable(); // Additional details like refund amount, notes, etc.
            $table->timestamps();

            $table->index(['task_id', 'product_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_task_statuses');
    }
};
