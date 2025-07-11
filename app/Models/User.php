<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'last_login_at',
        'avatar',
        'bio',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'preferences' => 'array',
    ];

    /**
     * Relaciones
     */
    public function assignedConversations()
    {
        return $this->hasMany(Conversation::class, 'assigned_user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function createdFlows()
    {
        return $this->hasMany(ChatbotFlow::class, 'created_by');
    }

    public function createdResponses()
    {
        return $this->hasMany(ChatbotResponse::class, 'created_by');
    }

    /**
     * Métodos de utilidad
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
