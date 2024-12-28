<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBreakSummaryReport extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_agentbreak_summery_report";

    public $timestamps = false;


    public function agent()
    {
        return $this->belongsTo(Agent::class,'id','agentid');
    }
}
