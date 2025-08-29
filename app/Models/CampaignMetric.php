<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMetric extends Model
{
    use HasFactory;

    protected $table = 'campaign_metrics';
    protected $primaryKey = 'campaign_id';
    public $timestamps = false;

    public function types()
    {
        return $this->belongsTo(CampaignType::class, 'type', 'id');
    }
}
