<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneEvent extends Model
{
    protected $fillable = [
        'event_type',
        'call_id',
        'direction',
        'cli',
        'extension',
        'user_id',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload'     => 'array',
        'occurred_at' => 'datetime',
    ];
}
