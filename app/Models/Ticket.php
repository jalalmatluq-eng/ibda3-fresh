<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'title', 'description', 'type', 'priority',
        'status', 'user_id', 'assigned_to', 'attachment',
    ];

    // Labels للعرض بالعربي
    public static array $typeLabels = [
        'technical'   => 'تقني',
        'accounting'  => 'استفسار محاسبي',
        'development' => 'طلب تطوير',
    ];

    public static array $priorityLabels = [
        'normal' => 'عادية',
        'high'   => 'مرتفعة',
        'urgent' => 'عاجلة',
    ];

    public static array $statusLabels = [
        'open'             => 'مفتوحة',
        'in_progress'      => 'قيد المعالجة',
        'waiting_customer' => 'بانتظار العميل',
        'resolved'         => 'محلولة',
        'closed'           => 'مغلقة',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                // Generate a ticket number like TKT-YYYYMMDD-XXXX
                $date = now()->format('Ymd');
                $latest = static::whereDate('created_at', now()->toDateString())->latest('id')->first();
                $sequence = $latest ? intval(substr($latest->ticket_number, -4)) + 1 : 1;
                $ticket->ticket_number = 'TKT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // علاقات
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::$priorityLabels[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }
}
