<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentQueueStatus extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_agentqueuestatus";


    public function scopeActive($query)
    {
        return $query->where('status',1);
    }

}
