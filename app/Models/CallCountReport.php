<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCountReport extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_callcount_report";

    protected $casts=[
        'date'=>'datetime'
    ];

    public function agentInfo()
    {
        return $this->belongsTo(Agent::class,'ani','extension');
    }
}
