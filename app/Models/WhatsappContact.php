<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'whatsapp_id',
        'name',
        'profile_name',
        'language',
        'profile_data',
        'is_blocked',
        'opt_in',
        'last_interaction_at',
        'tags',
        'notes',
    ];

    protected $casts = [
        'profile_data' => 'array',
        'is_blocked' => 'boolean',
        'opt_in' => 'boolean',
        'last_interaction_at' => 'datetime',
        'tags' => 'array',
    ];

    /**
     * Relaciones
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'contact_id');
    }

    public function activeConversation()
    {
        return $this->hasOne(Conversation::class, 'contact_id')
                    ->where('status', 'active')
                    ->latest();
    }

    /**
     * Accessors para compatibilidad
     */
    public function getPhoneAttribute()
    {
        return $this->phone_number;
    }

    /**
     * Métodos de utilidad
     */
    public function isBlocked()
    {
        return $this->is_blocked;
    }

    public function hasOptedIn()
    {
        return $this->opt_in;
    }

    public function updateLastInteraction()
    {
        $this->update(['last_interaction_at' => now()]);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }
}
