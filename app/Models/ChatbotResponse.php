<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'category',
        'message_text',
        'message_data',
        'language',
        'is_active',
        'variables',
        'created_by',
    ];

    protected $casts = [
        'message_data' => 'array',
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    /**
     * Relaciones
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Métodos de utilidad
     */
    public function isActive()
    {
        return $this->is_active;
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function getProcessedMessage($variables = [])
    {
        $message = $this->message_text;

        // Reemplazar variables en el mensaje
        foreach ($variables as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        return $message;
    }
}
