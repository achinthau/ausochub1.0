<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSkill extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_agentskills";

    public $timestamps = false;

    protected $casts = [
        'skill_ids' => 'array',
    ];

    protected $fillable=[
        'agentid',
        'skills',
        'skill_ids',
    ];
    


}
