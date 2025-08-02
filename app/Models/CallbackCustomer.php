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
        "callback_at",
        "comment",
        "called_at",
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function leads()
{
    return $this->belongsTo(Lead::class);
}

}
