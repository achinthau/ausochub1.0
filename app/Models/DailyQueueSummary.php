<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyQueueSummary extends Model
{
    use HasFactory;

    // protected $connection = "mysql-old";
    protected $table = "daily_queue_summeries";

    protected $fillable = [
        'date',
        'queue',
        'calls',
        'answered',
        'abandoned',
        'agents',
    ];

}
