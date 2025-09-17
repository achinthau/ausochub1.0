<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['feed_id', 'contact_no_01', 'contact_no_02', 'status', 'comments', 'updated_by'];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
