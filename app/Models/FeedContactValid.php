<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedContactValid extends Model
{
    use HasFactory;

    protected $table = 'feed_contact_valids';

    protected $fillable = [
        'feed_id', 'contact_no_01', 'contact_no_02', 'priority_field', 'lang', 'data', 'status', 'assigned_to',
        'next_available_at', 'attempt_count', 'in_queue',
        'call_status_option_id', 'call_status_option_type', 'rate', 'comments', 'promised_date',
        'campaign_id', 'updated_by', 'attempted_at', 'copied_at',
    ];

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
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Advance a record to the next "not answered" status.
     *
     * The attempt count is authoritative for skipped/callback (status 3)
     * records, otherwise the current 2/22 status determines the attempt.
     * Progression is 2 (1st) -> 22 (2nd) -> 222 (3rd, terminal).
     */
    public function markNotAnswered(): void
    {
        $currentStatus = (string) $this->status;
        $attemptCount = (int) ($this->attempt_count ?? 0);

        if (str_starts_with($currentStatus, '2')) {
            $previousAttempts = max(strlen($currentStatus), $attemptCount);
        } elseif ($currentStatus === '3') {
            // A skipped/callback record has already had at least one attempt.
            $previousAttempts = max(1, $attemptCount);
        } else {
            $previousAttempts = $attemptCount;
        }

        $attempt = min(3, $previousAttempts + 1);

        $this->status = (int) str_repeat('2', $attempt);
        $this->attempt_count = $attempt;
        $this->next_available_at = now()->addDay();
    }

    //status
    //['1'=>'answered'] rated,cancel,reopen
    //['2'=>'no answered']
    //['22'=>' 2 no answered']
    //['222'=>'3 no answered']
    //['4'=>'3 no answered'] removed 
}
