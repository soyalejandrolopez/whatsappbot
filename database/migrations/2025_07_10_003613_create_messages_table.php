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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null'); // null si es del contacto
            $table->string('whatsapp_message_id')->unique()->nullable();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('type', ['text', 'image', 'document', 'audio', 'video', 'location', 'contact', 'interactive', 'template']);
            $table->text('content')->nullable(); // Contenido del mensaje
            $table->json('media_data')->nullable(); // Datos de archivos multimedia
            $table->json('interactive_data')->nullable(); // Datos de mensajes interactivos
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamp('whatsapp_timestamp')->nullable();
            $table->boolean('is_automated')->default(false); // Si fue enviado por el bot
            $table->string('flow_step')->nullable(); // Paso del flujo que generó este mensaje
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['direction', 'is_automated']);
            $table->index(['status', 'created_at']);
            $table->index('whatsapp_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
