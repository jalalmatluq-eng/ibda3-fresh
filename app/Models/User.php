<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'code', 'name', 'email', 'password', 'phone',
        'role', 'department', 'specialization', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->code)) {
                // Generate a code like EMP-001
                $latest = static::latest('id')->first();
                $nextId = $latest ? $latest->id + 1 : 1;
                $user->code = 'EMP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // علاقات
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function articles()
    {
        return $this->hasMany(KnowledgeArticle::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSupport($query)
    {
        return $query->where('role', 'support');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupport(): bool
    {
        return in_array($this->role, ['support', 'admin']);
    }
}
