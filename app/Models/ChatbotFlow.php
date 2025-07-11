<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_conditions',
        'flow_steps',
        'language',
        'is_active',
        'priority',
        'created_by',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'flow_steps' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
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

    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }
}
