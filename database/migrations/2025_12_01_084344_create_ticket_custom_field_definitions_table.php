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
        Schema::create('ticket_custom_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gorgias_field_id');
            $table->string('external_id')->nullable();
            $table->string('object_type')->default('Ticket');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('required')->default(false);
            $table->string('requirement_type')->default('visible');
            $table->string('managed_type')->nullable();
            $table->json('definition')->nullable(); // Stores data_type and input_settings
            $table->timestamp('gorgias_created_datetime')->nullable();
            $table->timestamp('gorgias_updated_datetime')->nullable();
            $table->timestamp('deactivated_datetime')->nullable();
            $table->timestamps();

            $table->index('gorgias_field_id');
            $table->index('managed_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_custom_field_definitions');
    }
};
