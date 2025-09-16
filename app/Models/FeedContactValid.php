<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactValid extends Model
{
    use HasFactory;

    protected $table = 'feed_contact_valids';

    protected $fillable = ['feed_id',  'contact_no_01','contact_no_02', 'customer_name', 'data', 'status'];

    // protected $casts = [
    //     'data' => 'array', 
    // ];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }
}
