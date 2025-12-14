<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'message',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'support_ticket_id');
    }

    // تولید شماره تیکت یونیک
    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TK-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    // بررسی وضعیت
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAnswered(): bool
    {
        return $this->status === 'answered';
    }
}
