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
        // 1. Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ticket_number')->unique(); // Unique Ticket ID
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['pending', 'answered'])->default('pending');
            $table->text('admin_reply')->nullable(); // Simple single reply (legacy support)
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });

        // 2. Ticket Replies (Conversation history)
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete(); // null = admin
            $table->text('message');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
            
            $table->index(['support_ticket_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};
