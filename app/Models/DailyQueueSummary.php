<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQueueSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'queue',
        'calls',
        'answered',
        'abandoned',
        'agents',
    ];

}
