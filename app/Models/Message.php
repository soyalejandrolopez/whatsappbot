<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'whatsapp_message_id',
        'direction',
        'type',
        'content',
        'media_data',
        'interactive_data',
        'status',
        'whatsapp_timestamp',
        'is_automated',
        'flow_step',
        'metadata',
    ];

    protected $casts = [
        'media_data' => 'array',
        'interactive_data' => 'array',
        'whatsapp_timestamp' => 'datetime',
        'is_automated' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Relaciones
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Métodos de utilidad
     */
    public function isInbound()
    {
        return $this->direction === 'inbound';
    }

    public function isOutbound()
    {
        return $this->direction === 'outbound';
    }

    public function isAutomated()
    {
        return $this->is_automated;
    }

    public function isFromBot()
    {
        return $this->isOutbound() && $this->isAutomated();
    }

    public function isFromHuman()
    {
        return $this->isOutbound() && !$this->isAutomated();
    }

    public function isFromContact()
    {
        return $this->isInbound();
    }

    public function markAsDelivered()
    {
        $this->update(['status' => 'delivered']);
    }

    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
}
