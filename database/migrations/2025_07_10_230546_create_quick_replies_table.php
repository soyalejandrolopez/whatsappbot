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
        Schema::create('quick_replies', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->text('content');
            $table->string('language', 5)->default('es');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index(['category', 'is_active']);
            $table->index(['language', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_replies');
    }
};
