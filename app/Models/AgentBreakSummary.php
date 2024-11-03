<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBreakSummary extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_agentbreak_summery";

    public $timestamps = false;


    public function agent()
    {
        return $this->belongsTo(Agent::class,'id','agentid');
    }

}
