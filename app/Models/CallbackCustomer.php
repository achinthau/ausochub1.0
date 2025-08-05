<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallbackCustomer extends Model
{
    use HasFactory;

//     protected $casts = [
//     'callback_at' => 'datetime',
// ];


    protected $fillable = [
        "agent_id",
        "lead_id",
        "unique_id",
        "callback_at",
        "comment",
        "called_at",
    ];

    public function users()
{
    return $this->belongsTo(User::class, 'agent_id');
}

public function leads()
{
    return $this->belongsTo(Lead::class);
}

}
