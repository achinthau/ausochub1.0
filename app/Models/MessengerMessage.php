<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_name',
        'message_text',
        'message_id',
        'from_me',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'from_me'  => 'boolean',
        'is_read'  => 'boolean',
        'sent_at'  => 'datetime',
    ];

    /**
     * Scope: all messages belonging to a conversation thread (keyed by sender_id).
     */
    public function scopeForThread($query, string $senderId)
    {
        return $query->where('sender_id', $senderId)->orderBy('sent_at');
    }

    /**
     * Scope: unread incoming messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('from_me', false)->where('is_read', false);
    }
}
