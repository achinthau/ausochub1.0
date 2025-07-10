<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueueCount extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "queuecount";
    // protected $appends = ['duration'];

    protected $casts=[
        'date'=>'datetime'
    ];

    protected $fillable = ['date', 'queuename', 'uniqueid', 'ani', 'status', 'agent'];

    public function agentInfo()
    {
        return $this->belongsTo(Agent::class,'agent','extension');
    }


    public function scopeAnswered($query)
    {
        $query->where('queuecount.status',2);
    }

    public function scopeToday($query)
    {
        $query->whereBetween('date',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()]);
    }

    public function scopeTest($query)
    {
        return $query;
    }

    // public function scopeLatestRecord($query)
    // {
    //     return $query->select(DB::raw('MAX(date) AS Date, queuename, queuecount.uniqueid, ani'))
    //         ->groupBy('queuecount.uniqueid');
    // }

    // public function scopeWithStatusZero($query)
    // {
    //     return $query->leftJoinSub(function ($query) {
    //         $query->select('uniqueid')
    //             ->from('queuecount')
    //             ->where('status', 2)
    //             ->groupBy('uniqueid');
    //     }, 'answered_uniqueids', 'queuecount.uniqueid', '=', 'answered_uniqueids.uniqueid')
    //         ->where(function ($query) {
    //             $query->whereNull('answered_uniqueids.uniqueid')
    //             ->orWhere('queuecount.status', '!=', 0);
    //         });
    // }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent', 'extension');
    }
    
}
