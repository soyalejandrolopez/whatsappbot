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
        Schema::create('chatbot_responses', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Clave única para la respuesta
            $table->string('category'); // welcome, menu, error, etc.
            $table->text('message_text');
            $table->json('message_data')->nullable(); // Datos adicionales (botones, media, etc.)
            $table->string('language', 5)->default('es');
            $table->boolean('is_active')->default(true);
            $table->json('variables')->nullable(); // Variables que se pueden usar en el mensaje
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['category', 'language', 'is_active']);
            $table->index(['key', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_responses');
    }
};
