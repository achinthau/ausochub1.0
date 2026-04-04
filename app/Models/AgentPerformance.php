<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPerformance extends Model
{
    use HasFactory;

    protected $table = 'ac_agent_performance';
    
    protected $connection = 'mysql-old';

    protected $fillable = [
        'date',
        'agent_id',
        'agent_name',
        'extension',
        'total_calls',
        'avg_calls',
        'total_missed',
        'avg_missed',
        'acw',
        'avg_acw',
        'other_break',
        'avg_oth_break',
        'active_time',
        'talk_time',
        'avg_talk_time',
        'tickets_created_count',
        'queues',
    ];

    protected $casts = [
        'date' => 'date',
        'queues' => 'array',
    ];

    /**
     * Get the agent associated with this performance record
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
}
