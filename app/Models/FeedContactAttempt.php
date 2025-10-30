<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['feed_contact_valid_id','call_status_option_id', 'comments','campaign_id', 'updated_by'];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(FeedContactValid::class, 'feed_contact_valid_id');
    }
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(DialerCallStatusOption::class, 'call_status_option_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
