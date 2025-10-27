<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactValid extends Model
{
    use HasFactory;

    protected $table = 'feed_contact_valids';

    protected $fillable = ['feed_id',  'contact_no_01','contact_no_02', 'priority_field', 'data', 'status', 'assigned_to'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    //status
    //['1'=>'answered'] rated,cancel,reopen
    //['2'=>'no answered']
    //['22'=>' 2 no answered']
    //['222'=>'3 no answered']
    //['4'=>'3 no answered'] removed 
}
