<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_user";
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'fname',
        'lname',
        'usertype',
        'DOB',
        'NIC',
        'gender',
        'email',
        'addressNo',
        'numberone',
        'createdat',
        'updatedat',
        'updatedby',
        'status',
        'extension',
    ];

    public function queue()
    {
        return $this->hasMany(QueueCount::class, 'agent', 'extension');
    }

    public function todayQueues()
    {
        return $this->hasMany(QueueCount::class, 'agent', 'extension')->where('queuecount.date','>',Carbon::now()->startOfDay());
    }

    public function getFullNameAttribute()
    {
        return ucwords(strtolower($this->fname . " " . $this->lname));
    }

    public function getCurrentActiveQueuesTitleAttribute()
    {
        // return $this->currentActiveQueues()->exists() ? implode(",",$this->currentActiveQueues()->pluck('skill')->toArray()) : false;
        return $this->currentActiveQueues()->exists() ? $this->currentActiveQueues()->pluck('skill')->toArray() : false;
    }

    public function currentQueues()
    {
        return $this->hasMany(AgentQueueStatus::class, 'agentid');
    }

    public function currentActiveQueues()
    {
        return $this->hasMany(AgentQueueStatus::class, 'agentid')->active();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'id','agent_id');
    }

    public function extensionDetails()
    {
        return $this->belongsTo(Extension::class,'extension','extension');
    }

    public function scopeUsers($query)
    {
        $query->where('usertype', 'User');
    }

    public function scopeNoAssigned($query)
    {
        $query->whereNull('extension');
    }

    public function scopeCallApplicable($query)
    {
        $query->whereIn('user_type_id',[3,4,5,6]);
    }
}
