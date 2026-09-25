<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\Campaign;
use App\Models\CampaignAgentDialLimit;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
use Hamcrest\Type\IsInteger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SubmitCallStatus extends Component
{

    public $CallStatusModal = false;
    public $feed;
    public $status;

    public $options = [];
    public $selectedOption = null;
    public $comment = '';

    public $applyToAll = false;
    public $feedCount = 0;
    public $paymentDate;
    public $campaign;
    public $isSatisfaction = false;
    public $selectedReason = null;
    public $selectedReasons = [];

    protected $listeners = ['openCallStatusModal' => 'openModal'];

    public function openModal($id, $status, $feedCount, $feedContactId = null, $campaignId = null)
    {
        $this->dialerLog()->info('dialer.status.open_modal_started', [
            'requested_id' => $id,
            'requested_feed_contact_id' => $feedContactId,
            'status' => $status,
            'feed_count' => $feedCount,
            'campaign_id' => $campaignId,
        ]);

        $this->CallStatusModal = true;
        $this->feedCount = $feedCount;

        try {
            if ($feedContactId) {
                $this->feed = FeedContactValid::find($feedContactId);
            } elseif (is_numeric($id)) {
                $this->feed = FeedContactValid::find($id);
            } else {
                $this->feed = FeedContactValid::whereRaw('TRIM(priority_field) = ?', [trim($id)])->first();
            }
        } catch (\Throwable $e) {
            $this->logDatabaseError('dialer.status.open_modal_feed_lookup_failed', [
                'requested_id' => $id,
                'requested_feed_contact_id' => $feedContactId,
            ], $e);
            throw $e;
        }

        $this->dialerLog()->info('dialer.status.open_modal_feed_lookup_returned', [
            'requested_id' => $id,
            'requested_feed_contact_id' => $feedContactId,
            'found' => (bool) $this->feed,
            'feed_contact_id' => $this->feed?->id,
            'feed_id' => $this->feed?->feed_id,
            'status' => $this->feed?->status,
        ]);
        // dd($this->feedCount);
        // dd($this->feed->id);
        // $this->campaign = Campaign::whereIn('assigned_feeds', [$this->feed->feed_id])->get();
        if ($campaignId) {
            $this->campaign = $campaignId;
        } else {
            $feedId = $this->feed->feed_id;
            $campaigns = Campaign::whereRaw('FIND_IN_SET(?, assigned_feeds)', [$feedId])->get();
            $this->campaign = $campaigns->first()->id;
        }

        // dd($this->campaign);
        $this->status = $status;
        $this->isSatisfaction = Campaign::find($this->campaign)?->service_type === 'satisfaction';

if ($status === 'answered') {
            $statusTypes = [1, 41];
        } elseif ($this->isSatisfaction) {
            $statusTypes = [4, 42];
        } else {
            $statusTypes = [2, 42];
        }
        $campaignOptions = DialerCallStatusOption::where('campaign_id', $this->campaign)
            ->whereIn('type', $statusTypes)
            ->get();

        if ($campaignOptions->isNotEmpty()) {
            $this->options = $campaignOptions;
        } else {
            $this->options = DialerCallStatusOption::whereNull('campaign_id')
                ->whereIn('type', $statusTypes)
                ->get();
        }

        $this->selectedOption = null;
        $this->selectedReason = null;
        $this->selectedReasons = [];
        $this->comment = '';
    }

    public function updatedSelectedReason($value)
    {
        if ($value && !in_array($value, $this->selectedReasons)) {
            $this->selectedReasons[] = $value;
        }
    }

    public function removeReason($reason)
    {
        $this->selectedReasons = array_values(array_filter($this->selectedReasons, fn($r) => $r !== $reason));
    }

    // protected $rules = [
    //     // 'comment' => 'required|string|min:5',
    //     'selectedOption' => 'required|exists:dialer_call_status_options,id',
    // ];

    protected $messages = [
        // 'comment.required' => 'Please select a payment date.',
        'paymentDate.required' => 'The payment date must be a valid date.',
        'paymentDate.after_or_equal' => 'The payment date cannot be in the past.',
        'selectedOption.required' => 'You must select a call status.',
    ];


    protected function incrementDialCount()
    {
        $campaignId = (int) $this->campaign;
        $agentId = (int) Auth::id();
        $context = [
            'campaign_id' => $campaignId,
            'agent_id' => $agentId,
        ];

        try {
            CampaignAgentDialLimit::incrementCount($campaignId, $agentId);
            $this->dialerLog()->info('dialer.dial_count.increment_returned', $context);
        } catch (\Throwable $e) {
            $this->logDatabaseError('dialer.dial_count.increment_failed', $context, $e);
            throw $e;
        }
    }

    protected function dialerLog()
    {
        return Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/verify.log'),
            'level' => 'debug',
        ]);
    }

    protected function logDatabaseError(string $operation, array $context, \Throwable $exception): void
    {
        $this->dialerLog()->error($operation, array_merge($context, [
            'exception' => get_class($exception),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]));
    }

    protected function saveFeedWithLogging(FeedContactValid $feed, string $operation): bool
    {
        $context = [
            'operation' => $operation,
            'feed_contact_id' => $feed->id,
            'feed_id' => $feed->feed_id,
            'status' => $feed->status,
        ];

        $this->dialerLog()->info('dialer.feed_contact_table.save_started', $context);

        try {
            $returned = $feed->save();

            $this->dialerLog()->info('dialer.feed_contact_table.save_returned', array_merge($context, [
                'returned' => $returned,
                'exists' => $feed->exists,
                'status' => $feed->status,
            ]));

            if (!$returned) {
                $this->dialerLog()->warning('dialer.feed_contact_table.save_returned_false', array_merge($context, [
                    'returned' => $returned,
                ]));
            }

            return $returned;
        } catch (\Throwable $e) {
            $this->logDatabaseError('dialer.feed_contact_table.save_failed', $context, $e);
            throw $e;
        }
    }

    /**
     * When a contact reaches a terminal status it is archived into the report
     * table. The active valid row is left in place here and removed later by
     * the "Next Customer" action. Any other status keeps current behaviour.
     */
    protected function copyCompletedToReport(FeedContactValid $feed): void
    {
        $completedStatuses = [1, 41, 42, 222];
        $status = (int) $feed->status;
        $context = [
            'component' => 'submit_call_status',
            'feed_contact_id' => $feed->id,
            'feed_id' => $feed->feed_id,
            'status' => $status,
        ];

        $this->dialerLog()->info('dialer.report.copy_called', $context);

        if (!in_array($status, $completedStatuses, true)) {
            $this->dialerLog()->info('dialer.report.copy_skipped', array_merge($context, [
                'completed_statuses' => $completedStatuses,
            ]));
            return;
        }

        try {
            $attributes = [];

            foreach ($feed->getAttributes() as $attribute => $value) {
                if (in_array($attribute, (new FeedContactValidReport())->getFillable(), true)) {
                    $attributes[$attribute] = $value;
                }
            }

            $attributes['feed_contact_id'] = $feed->id;

            $report = FeedContactValidReport::updateOrCreate(
                ['feed_contact_id' => $feed->id],
                $attributes
            );

            $this->dialerLog()->info('dialer.report.copy_returned', array_merge($context, [
                'returned' => [
                    'id' => $report->id,
                    'feed_contact_id' => $report->feed_contact_id,
                    'status' => $report->status,
                    'exists' => $report->exists,
                    'was_recently_created' => $report->wasRecentlyCreated,
                    'was_changed' => $report->wasChanged(),
                ],
            ]));
        } catch (\Throwable $e) {
            $this->logDatabaseError('dialer.report.copy_failed', $context, $e);
            throw $e;
        }
    }

    public function submit()
    {
        $this->dialerLog()->info('dialer.status.submit_started', [
            'feed_contact_id' => $this->feed?->id,
            'feed_id' => $this->feed?->feed_id,
            'status' => $this->status,
            'campaign_id' => $this->campaign,
            'apply_to_all' => $this->applyToAll,
            'agent_id' => Auth::id(),
        ]);

        $isNotAnsweredSatisfaction = $this->isSatisfaction && $this->status == 'not_answered';

        if ($isNotAnsweredSatisfaction) {
            if (empty($this->selectedReasons)) {
                $this->dialerLog()->warning('dialer.status.submit_validation_skipped', [
                    'reason' => 'missing_satisfaction_reasons',
                    'feed_contact_id' => $this->feed?->id,
                ]);
                $this->addError('selectedReasons', 'Please select at least one reason.');
                return;
            }

            $optionNameString = implode(',', array_values(array_unique($this->selectedReasons)));
            $optionTypeString = DialerCallStatusOption::whereIn('option', $this->selectedReasons)
                ->pluck('type')
                ->map(fn ($type) => (string) $type)
                ->unique()
                ->values()
                ->implode(',');
        } else {
            if (empty($this->selectedOption)) {
                $this->dialerLog()->warning('dialer.status.submit_validation_skipped', [
                    'reason' => 'missing_selected_option',
                    'feed_contact_id' => $this->feed?->id,
                ]);
                $this->addError('selectedOption', 'Please select an option.');
                return;
            }

            // Decode the JSON from the dropdown
            $option = json_decode($this->selectedOption, true);

            // Validation based on the option value
            if ($option['value'] === 'Promised to pay') {
                $this->validate([
                    'paymentDate' => 'required|date|after_or_equal:today',
                    'selectedOption' => 'required',
                ]);

                // Append payment date to comment
                $this->comment = $this->comment . ' Payment Date: ' . $this->paymentDate;
            } else {
                $this->validate([
                    'selectedOption' => 'required',
                ]);
            }

            $optionNameString = $option['value'];
            $optionTypeString = $option['type'];
        }

        $isChangeRequest = collect(explode(',', $optionNameString))
            ->map(fn ($name) => str_replace(['_', ' '], '', strtolower((string) trim($name))))
            ->contains('changerequest');

        $optionTypes = collect(explode(',', (string) $optionTypeString))
            ->map(fn ($type) => (string) trim($type))
            ->filter()
            ->unique()
            ->values();

        if ($isChangeRequest) {
            $targetStatus = 5;
        } elseif ($optionTypes->contains('41')) {
            $targetStatus = 41; // Answered cancel
        } elseif ($optionTypes->contains('42')) {
            $targetStatus = 42; // Not answered cancel
        } elseif ($this->status == 'answered') {
            $targetStatus = 1; // Answered
        } else {
            $targetStatus = null; // Not answered - uses per-feed retry logic
        }

        $this->dialerLog()->info('dialer.status.target_resolved', [
            'feed_contact_id' => $this->feed?->id,
            'target_status' => $targetStatus,
            'option_type' => $optionTypeString,
            'option_name' => $optionNameString,
        ]);

        $phones = collect([$this->feed->contact_no_01, $this->feed->contact_no_02])
            ->map(function ($phone) {
                return trim((string) $phone);
            })
            ->filter(function ($phone) {
                return $phone !== '';
            })
            ->unique()
            ->values();

        if ($this->applyToAll && $phones->isNotEmpty()) {
            // Fetch only feeds with same number AND not updated before
            try {
                $feeds = FeedContactValid::where(function ($query) use ($phones) {
                    foreach ($phones as $phone) {
                        $query->orWhere('contact_no_01', $phone)
                            ->orWhere('contact_no_02', $phone);
                    }
                })
                    ->where(function ($query) {
                        $query->whereNull('status') // Fresh ones
                            ->orWhere('status', 3)
                            ->orWhere('status', "LIKE", '2%'); // No Answer retries
                    })
                    ->when($this->feed->feed_id, function ($query, $feedId) {
                        $query->where('feed_id', $feedId);
                    })
                    ->get();
            } catch (\Throwable $e) {
                $this->logDatabaseError('dialer.feed_contact_table.apply_to_all_query_failed', [
                    'feed_contact_id' => $this->feed?->id,
                    'feed_id' => $this->feed?->feed_id,
                    'phones' => $phones->all(),
                ], $e);
                throw $e;
            }

            $this->dialerLog()->info('dialer.feed_contact_table.apply_to_all_query_returned', [
                'source_feed_contact_id' => $this->feed?->id,
                'feed_id' => $this->feed?->feed_id,
                'count' => $feeds->count(),
                'feed_contact_ids' => $feeds->pluck('id')->all(),
            ]);

            foreach ($feeds as $feed) {
                if ($targetStatus !== null) {
                    $feed->status = $targetStatus;
                } else {
                    if (str_starts_with((string) $feed->status, '2')) {
                        $feed->status = (int) ($feed->status . '2');
                    } else {
                        $feed->status = 2; // First time "no answer"
                    }

                    // Set next date each time for status 2-based
                    $feed->next_available_at = now()->addDay();
                }

                $feed->call_status_option_id = $optionNameString;
                $feed->call_status_option_type = $isChangeRequest ? 'change_request' : $optionTypeString;
                $feed->comments = $this->comment;
                $feed->campaign_id = $this->campaign;
                $feed->updated_by = Auth::id();
                $feed->attempted_at = now();
                $saved = $this->saveFeedWithLogging($feed, 'submit_call_status.apply_to_all');

                $this->dialerLog()->info('feed saved', [
                    'id' => $feed->id,
                    'returned' => $saved,
                    'attrs' => $feed->getAttributes(),
                ]);

                $this->copyCompletedToReport($feed);
            }

        } else {
            // ✅ Update only the current feed
            if ($targetStatus !== null) {
                $this->feed->status = $targetStatus;
            } else {
                if (str_starts_with((string) $this->feed->status, '2')) {
                    $this->feed->status = (int) ($this->feed->status . '2');
                } else {
                    $this->feed->status = 2; // First time "no answer"
                }

                // Set next date each time for status 2-based
                $this->feed->next_available_at = now()->addDay();
            }

            $this->feed->call_status_option_id = $optionNameString;
            $this->feed->call_status_option_type = $isChangeRequest ? 'change_request' : $optionTypeString;
            $this->feed->comments = $this->comment;
            $this->feed->campaign_id = $this->campaign;
            $this->feed->updated_by = Auth::id();
            $this->feed->attempted_at = now();
            $this->saveFeedWithLogging($this->feed, 'submit_call_status.single');

            $this->copyCompletedToReport($this->feed);
        }

        if ($this->status == 'answered') {
            $this->incrementDialCount();
        }

        $this->emit('FeedCompleted');
        $this->CallStatusModal = false;

        $this->dialerLog()->info('dialer.status.submit_completed', [
            'feed_contact_id' => $this->feed?->id,
            'feed_id' => $this->feed?->feed_id,
            'apply_to_all' => $this->applyToAll,
            'status' => $this->feed?->status,
        ]);
    }


    public function render()
    {
        return view('livewire.leads.partials.submit-call-status');
    }
}
