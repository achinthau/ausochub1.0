<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QueueCountReport extends Model
{
    use HasFactory;

    protected $connection = "mysql-old";
    protected $table = "au_queuecount_report";

    protected $casts = [
        'date' => 'datetime'
    ];

    protected $fillable = ['date', 'queuename', 'uniqueid', 'ani', 'status', 'agent'];

    public function agentInfo()
    {
        return $this->belongsTo(Agent::class, 'agent', 'extension');
    }

    public function scopeAnswered($query)
    {
        $query->where('au_queuecount_report.status', 2);
    }

    public function scopeToday($query)
    {
        $query->whereBetween('date', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
    }

    public function scopeLatestRecord($query)
    {
        return $query->select(DB::raw('MAX(date) AS Date, queuename, au_queuecount_report.uniqueid, ani'))
            ->groupBy('au_queuecount_report.uniqueid');
    }

    public function scopeWithStatusZero($query)
    {
        return $query->leftJoinSub(function ($query) {
            $query->select('uniqueid')
                ->from('au_queuecount_report')
                ->where('status', 2)
                ->groupBy('uniqueid');
        }, 'answered_uniqueids', 'au_queuecount_report.uniqueid', '=', 'answered_uniqueids.uniqueid')
            ->where(function ($query) {
                $query->whereNull('answered_uniqueids.uniqueid')
                    ->orWhere('au_queuecount_report.status', '!=', 0);
            });
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent', 'extension');
    }
}
