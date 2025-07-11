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
        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('whatsapp_id')->unique();
            $table->string('name')->nullable();
            $table->string('profile_name')->nullable();
            $table->string('language', 5)->default('es');
            $table->json('profile_data')->nullable(); // Datos adicionales del perfil
            $table->boolean('is_blocked')->default(false);
            $table->boolean('opt_in')->default(true); // Consentimiento para recibir mensajes
            $table->timestamp('last_interaction_at')->nullable();
            $table->json('tags')->nullable(); // Etiquetas para categorizar contactos
            $table->text('notes')->nullable(); // Notas del agente
            $table->timestamps();

            $table->index(['phone_number', 'is_blocked']);
            $table->index(['language', 'opt_in']);
            $table->index('last_interaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_contacts');
    }
};
