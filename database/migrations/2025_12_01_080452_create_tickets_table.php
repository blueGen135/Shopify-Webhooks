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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gorgias_ticket_id')->unique();
            $table->text('subject')->nullable();
            $table->text('summary')->nullable();
            $table->string('uri')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();

            // Requester information, we need for faster search
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requester_name')->nullable();
            $table->string('requester_firstname')->nullable();
            $table->string('requester_lastname')->nullable();

            // Assignment, linking to local users for internal tracking 
            $table->unsignedBigInteger('assignee_user_id')->nullable();
            $table->unsignedBigInteger('assignee_team_id')->nullable();
            $table->foreignId('gorgias_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Status flags
            $table->boolean('is_unread')->default(false);

            // Datetime fields from Gorgias
            $table->timestamp('created_datetime')->nullable();
            $table->timestamp('opened_datetime')->nullable();
            $table->timestamp('last_received_message_datetime')->nullable();
            $table->timestamp('last_message_datetime')->nullable();
            $table->timestamp('updated_datetime')->nullable();
            $table->timestamp('closed_datetime')->nullable();
            $table->timestamp('trashed_datetime')->nullable();
            $table->timestamp('snooze_datetime')->nullable();

            // Custom fields stored as JSON for flexibility
            $table->json('custom_fields')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('gorgias_ticket_id');
            $table->index('requester_email');
            $table->index('requester_name');
            $table->index('status');
            $table->index('assignee_user_id');
            $table->index('created_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
