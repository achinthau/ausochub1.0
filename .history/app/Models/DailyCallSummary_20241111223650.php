<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCallSummary extends Model
{
    use HasFactory;

    protected $table = "daily_queue_summaries";

    protected $fillable = [
        'date',
        'inbound',
        'outbound',
        'queued',
        'abandent',
        'answered',
    ];
}
