<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCallSummary extends Model
{
    protected $table = 'daily_call_summaries';
    use HasFactory;

    protected $fillable = [
        'date',
        'inbound',
        'outbound',
        'queued',
        'abandent',
        'answered',
    ];
}
