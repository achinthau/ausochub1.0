<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasFactory;

    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return $this->first_name." ".$this->last_name;
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class,'skill_id');
    }

    public function status()
    {
        return $this->belongsTo(LeadStatus::class,'status_id');
    }

    public function callLogs()
    {
        return $this->hasMany(QueueCount::class,'contact_number','ani');
    }
    
    public function outCallLogs()
    {
        return $this->hasMany(CallCount::class,'contact_number','dnis')->where('direction','out');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function latestTickets()
    {
        return $this->hasMany(Ticket::class)->latest();
    }

    public function ticketsNew()
    {
        return $this->hasMany(Ticket::class)->whereIn('ticket_category_id',[1,2]);
    }

    public function openedTickets()
    {
        return $this->hasMany(Ticket::class)->whereIn('ticket_category_id',[1,2])->where('ticket_status_id','<>',4);
    }

    public function orders()
    {
        return $this->hasMany(Ticket::class)->where('ticket_category_id',3);    
    }

    public function lastOrder()
    {
        return $this->hasOne(Ticket::class)->where('ticket_category_id',3)->latest();    
    }

    public function scopeRelavant($query)
    {
        // Auth::user()->user_type_id==4 ? $query->where('extension',Auth::user()->agent->extension) :$query;
        $query;
    }
}
