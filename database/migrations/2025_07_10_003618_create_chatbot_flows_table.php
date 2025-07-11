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
        Schema::create('chatbot_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type'); // keyword, menu_option, default, welcome
            $table->json('trigger_conditions'); // Condiciones para activar el flujo
            $table->json('flow_steps'); // Definición completa del flujo
            $table->string('language', 5)->default('es');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Prioridad para resolver conflictos
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index(['trigger_type', 'is_active']);
            $table->index(['language', 'is_active']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_flows');
    }
};
