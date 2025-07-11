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
        Schema::table('conversations', function (Blueprint $table) {
            // Agregar columnas faltantes para gestión de conversaciones
            $table->string('priority')->default('medium')->after('status'); // low, medium, high, urgent
            $table->timestamp('closed_at')->nullable()->after('ended_at');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null')->after('closed_at');
            $table->timestamp('assigned_at')->nullable()->after('assigned_user_id');
            $table->text('notes')->nullable()->after('satisfaction_comment');
            $table->integer('usage_count')->default(0)->after('message_count');

            // Agregar índices para optimizar consultas
            $table->index(['priority', 'status']);
            $table->index('assigned_at');
            $table->index('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Eliminar columnas agregadas
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['priority', 'closed_at', 'closed_by', 'assigned_at', 'notes', 'usage_count']);

            // Eliminar índices
            $table->dropIndex(['priority', 'status']);
            $table->dropIndex(['assigned_at']);
            $table->dropIndex(['closed_at']);
        });
    }
};
