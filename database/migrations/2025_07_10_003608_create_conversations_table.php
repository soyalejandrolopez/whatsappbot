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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('whatsapp_contacts')->onDelete('cascade');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('active'); // active, closed, transferred, waiting
            $table->string('type')->default('chatbot'); // chatbot, human, mixed
            $table->string('language', 5)->default('es');
            $table->string('current_flow_id')->nullable(); // ID del flujo actual del chatbot
            $table->json('flow_context')->nullable(); // Contexto del flujo actual
            $table->integer('message_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('satisfaction_rating')->nullable(); // 1-5
            $table->text('satisfaction_comment')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->timestamps();

            $table->index(['contact_id', 'status']);
            $table->index(['assigned_user_id', 'status']);
            $table->index(['status', 'last_message_at']);
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
