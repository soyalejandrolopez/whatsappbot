<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'metric_type',
        'metric_name',
        'metric_value',
        'metric_unit',
        'date',
        'hour',
        'additional_data',
    ];

    protected $casts = [
        'metric_value' => 'decimal:4',
        'date' => 'date',
        'additional_data' => 'array',
    ];

    /**
     * Relaciones
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Métodos de utilidad
     */
    public static function recordMetric($conversationId, $type, $name, $value, $unit = null, $additionalData = null)
    {
        return self::create([
            'conversation_id' => $conversationId,
            'metric_type' => $type,
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'date' => now()->toDateString(),
            'hour' => now()->hour,
            'additional_data' => $additionalData,
        ]);
    }
}
