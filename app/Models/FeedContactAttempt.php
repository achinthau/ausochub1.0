<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['feed_contact_valid_id', 'comments', 'updated_by'];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(FeedContactValid::class, 'feed_contact_valid_id');
    }
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
