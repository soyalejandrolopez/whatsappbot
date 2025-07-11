<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'assigned_user_id',
        'status',
        'priority',
        'type',
        'language',
        'current_flow_id',
        'flow_context',
        'message_count',
        'usage_count',
        'last_message_at',
        'started_at',
        'ended_at',
        'closed_at',
        'closed_by',
        'assigned_at',
        'satisfaction_rating',
        'satisfaction_comment',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'flow_context' => 'array',
        'last_message_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'closed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relaciones
     */
    public function contact()
    {
        return $this->belongsTo(WhatsappContact::class, 'contact_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function analytics()
    {
        return $this->hasMany(ConversationAnalytic::class);
    }

    /**
     * Métodos de utilidad
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isAssigned()
    {
        return !is_null($this->assigned_user_id);
    }

    public function assignTo(User $user)
    {
        $this->update([
            'assigned_user_id' => $user->id,
            'assigned_at' => now(),
            'type' => 'human'
        ]);
    }

    public function close(User $user = null)
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => $user?->id,
            'ended_at' => now()
        ]);
    }

    public function incrementMessageCount()
    {
        $this->increment('message_count');
        $this->update(['last_message_at' => now()]);
    }

    public function updateFlowContext($context)
    {
        $this->update(['flow_context' => $context]);
    }

    public function setCurrentFlow($flowId)
    {
        $this->update(['current_flow_id' => $flowId]);
    }
}
