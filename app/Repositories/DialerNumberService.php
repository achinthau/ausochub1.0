<?php

namespace App\Repositories;

use App\Models\Campaign;
use App\Models\CampaignAgentDialLimit;
use App\Models\FeedContactValid;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class DialerNumberService
{
    /**
     * Redis key prefix holding the number currently shown to an agent.
     * Only the dispatcher service writes these keys.
     */
    public const SHOWN_PREFIX = 'dialer:shown';

    /**
     * Redis key prefix holding whether an agent has an active queued skill
     * (used by the call panel to decide display mode without a DB query).
     */
    public const QUEUED_PREFIX = 'dialer:queued';

    /**
     * Time-to-live for the "shown number" keys (seconds).
     */
    protected int $keyTtl = 90;

    /**
     * Time-to-live for the cached "has active queued skill" flag (seconds).
     * Kept short so it tracks real queue state, but long enough that the
     * dispatcher does not hit MySQL on every tick.
     */
    protected int $queuedTtl = 15;

    public function shownKey(int $userId): string
    {
        return self::SHOWN_PREFIX . ':' . $userId;
    }

    public function queuedKey(int $userId): string
    {
        return self::QUEUED_PREFIX . ':' . $userId;
    }

    public function getShown(int $userId): ?array
    {
        $raw = Redis::get($this->shownKey($userId));

        if (!$raw) {
            return null;
        }

        $data = json_decode($raw, true);

        return is_array($data) ? $data : null;
    }

    public function setShown(int $userId, array $payload): void
    {
        Redis::setex($this->shownKey($userId), $this->keyTtl, json_encode($payload));
    }

    public function clearShown(int $userId): void
    {
        Redis::del($this->shownKey($userId));
    }

    /**
     * The agent's current contact: the lowest-id callable contact that is
     * either unassigned or already assigned to them. This is the exact same
     * selection the lead window uses for "Next Customer", so the dashboard
     * and the lead window can never disagree on which contact is current.
     *
     * Does not claim/assign anything; callers that are allowed to write use
     * resolveCurrentContact() instead.
     */
    public function resolveCurrentContactRecord(User $user): ?FeedContactValid
    {
        [$record] = $this->findCurrentContact($user);

        return $record;
    }

    /**
     * Same selection as resolveCurrentContactRecord(), but claims the contact
     * for the agent (and its related same-phone work orders) and returns the
     * payload to publish to Redis. Returns null when nothing is available.
     */
    public function resolveCurrentContact(User $user): ?array
    {
        [$record, $campaigns] = $this->findCurrentContact($user);

        if (!$record) {
            return null;
        }

        $this->claimContact($record, $user);

        return $this->payloadFromRecord($record, $campaigns);
    }

    /**
     * Name of the campaign (from the user's active queued skills) that owns
     * the given feed, used to label the shown number.
     */
    public function campaignNameForFeed(User $user, $feedId): ?string
    {
        $campaign = $this->campaignsForUser($user)->first(function ($campaign) use ($feedId) {
            $campaignFeedIds = is_array($campaign->feed_ids)
                ? $campaign->feed_ids
                : json_decode($campaign->feed_ids, true);

            return in_array($feedId, $campaignFeedIds ?: []);
        });

        return $campaign?->name;
    }

    public function setQueued(int $userId, bool $queued): void
    {
        Redis::setex($this->queuedKey($userId), $this->queuedTtl, $queued ? '1' : '0');
    }

    public function hasQueued(int $userId): ?bool
    {
        $raw = Redis::get($this->queuedKey($userId));

        if ($raw === null) {
            return null;
        }

        return $raw === '1';
    }

    /**
     * @return array{0: ?FeedContactValid, 1: Collection}
     */
    protected function findCurrentContact(User $user): array
    {
        $campaigns = $this->campaignsForUser($user);

        if ($campaigns->isEmpty()) {
            return [null, $campaigns];
        }

        $feedIds = $campaigns->flatMap->feed_ids->unique()->values()->toArray();

        if (empty($feedIds)) {
            return [null, $campaigns];
        }

        $record = $this->candidateQuery($user, $feedIds)
            ->where(function ($query) use ($user) {
                $query->whereNull('assigned_to')
                    ->orWhere('assigned_to', (int) $user->id);
            })
            ->orderBy('id')
            ->first();

        return [$record, $campaigns];
    }

    /**
     * Active campaigns whose name matches one of the user's queued skills.
     * Campaigns where the agent reached the dial limit are excluded.
     */
    protected function campaignsForUser(User $user): Collection
    {
        $userId = (int) $user->id;

        $currentSkills = $user->currentQueues()->active()->pluck('skill')->unique();

        if ($currentSkills->isEmpty()) {
            return collect();
        }

        return Campaign::where('status', 1)
            ->whereIn('name', $currentSkills)
            ->get()
            ->filter(function ($campaign) use ($userId) {
                return !CampaignAgentDialLimit::hasReachedLimit((int) $campaign->id, $userId);
            });
    }

    /**
     * Contacts in the given feeds that are currently callable and match the
     * user's language preference (availability rules from the old panel).
     */
    protected function candidateQuery(User $user, array $feedIds): Builder
    {
        $userLanguageNames = $user->languages->pluck('name')->toArray();

        return FeedContactValid::whereIn('feed_id', $feedIds)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNull('status')
                            ->orWhereIn('status', [2, 22]);
                    })
                    ->where(function ($qq) {
                        $qq->whereNull('next_available_at')
                            ->orWhere('next_available_at', '<=', now()->endOfDay());
                    });
                })
                ->orWhere(function ($q) {
                    $q->where('status', 3)
                        ->whereNotNull('next_available_at')
                        ->where('next_available_at', '<=', now());
                });
            })
            ->where(function ($q) use ($userLanguageNames) {
                $q->whereNull('lang')
                    ->orWhereIn('lang', $userLanguageNames);
            });
    }

    /**
     * Claim the contact and all related work orders sharing its primary phone
     * for the agent, matching the Next Customer logic in leads.show.
     */
    protected function claimContact(FeedContactValid $record, User $user): void
    {
        $userId = (int) $user->id;

        if ((int) $record->assigned_to === $userId) {
            return;
        }

        try {
            $record->update(['assigned_to' => $userId]);

            $phone = !empty($record->contact_no_01) ? $record->contact_no_01 : $record->contact_no_02;
            $userLanguageNames = $user->languages->pluck('name')->toArray();

            $relatedContacts = FeedContactValid::where('contact_no_01', $phone)
                ->when($record->feed_id, fn($query) => $query->where('feed_id', $record->feed_id))
                ->where(function ($q) use ($userLanguageNames) {
                    $q->whereNull('lang')
                        ->orWhereIn('lang', $userLanguageNames);
                })
                ->get();

            if ($relatedContacts->isNotEmpty()) {
                FeedContactValid::whereIn('id', $relatedContacts->pluck('id')->unique()->values())
                    ->update(['assigned_to' => $userId]);
            }
        } catch (\Throwable $e) {
            \Log::warning('DialerNumberService: failed to claim contact', [
                'feed_contact_id' => $record->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function payloadFromRecord(FeedContactValid $record, Collection $campaigns): array
    {
        $data = json_decode($record->data, true);

        $campaignForNumber = $campaigns->first(function ($campaign) use ($record) {
            $campaignFeedIds = is_array($campaign->feed_ids)
                ? $campaign->feed_ids
                : json_decode($campaign->feed_ids, true);

            return in_array($record->feed_id, $campaignFeedIds ?: []);
        });

        return [
            'contact_id' => $record->id,
            'phone' => !empty($record->contact_no_01) ? $record->contact_no_01 : $record->contact_no_02,
            'phone2' => !empty($record->contact_no_02) ? $record->contact_no_02 : $record->contact_no_01,
            'customer_name' => $data['cust_name'] ?? null,
            'address_line_1' => $data['add1'] ?? null,
            'address_line_2' => $data['add2'] ?? null,
            'feed_id' => $record->feed_id,
            'campaign_name' => $campaignForNumber ? $campaignForNumber->name : null,
            'reason' => null,
        ];
    }
}