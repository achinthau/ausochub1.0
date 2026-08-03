<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignAgentDialLimit extends Model
{
    use HasFactory;

    protected $table = 'campaign_agent_dial_limits';

    protected $fillable = [
        'campaign_id',
        'agent_id',
        'max_count',
        'current_count',
        'month',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public static function hasReachedLimit(int $campaignId, int $agentId): bool
    {
        $entry = static::where('campaign_id', $campaignId)
            ->where('agent_id', $agentId)
            ->first();

        if (!$entry || $entry->max_count === null) {
            return false;
        }

        $currentMonth = now()->format('Y-m');

        if ($entry->month && $entry->month !== $currentMonth) {
            return false;
        }

        return (int) $entry->current_count >= (int) $entry->max_count;
    }

    public static function incrementCount(int $campaignId, int $agentId): void
    {
        if (!$campaignId || !$agentId) {
            return;
        }

        $entry = static::firstOrNew([
            'campaign_id' => $campaignId,
            'agent_id' => $agentId,
        ]);

        $currentMonth = now()->format('Y-m');

        if ($entry->exists && $entry->month && $entry->month !== $currentMonth) {
            $entry->current_count = 1;
        } else {
            $entry->current_count = (int) $entry->current_count + 1;
        }

        $entry->month = $currentMonth;
        $entry->save();
    }

    public static function incrementForFeed(int $feedId, int $agentId): void
    {
        $campaignId = Campaign::whereRaw('FIND_IN_SET(?, assigned_feeds)', [$feedId])->value('id');

        if ($campaignId) {
            static::incrementCount((int) $campaignId, $agentId);
        }
    }
}
