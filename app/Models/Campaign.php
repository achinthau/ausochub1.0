<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';

    protected $fillable = ['name', 'status', 'assigned_users', 'assigned_feeds','company','created_by','schedule','type','service_type'];

     //status
    //['0'=>'inactive'] not started
    //['1'=>'active'] running
    //['2'=>'hold']
    //['3'=>'completed']
    //['4'=>'canceled']
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'company', 'id');
    }
    public function types()
    {
        return $this->belongsTo(CampaignType::class, 'type', 'id');
    }

    public function getFeedIdsAttribute(): array
    {
        return $this->assigned_feeds
            ? array_filter(explode(',', $this->assigned_feeds))
            : [];
    }
}
