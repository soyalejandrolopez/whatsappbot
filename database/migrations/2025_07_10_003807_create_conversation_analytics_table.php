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
        Schema::create('conversation_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // response_time, satisfaction, resolution_rate, etc.
            $table->string('metric_name');
            $table->decimal('metric_value', 10, 4);
            $table->string('metric_unit')->nullable(); // seconds, percentage, count, etc.
            $table->date('date');
            $table->integer('hour')->nullable(); // Para métricas por hora
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'metric_type']);
            $table->index(['date', 'metric_type']);
            $table->index(['metric_type', 'metric_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_analytics');
    }
};
