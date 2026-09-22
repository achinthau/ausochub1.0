<?php

namespace App\Http\Livewire\Dashboard\Partials\Dialer;

use App\Models\Lead;
use App\Repositories\DialerNumberService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CallPanel extends Component
{
    public $phone;
    public $phone2;
    public $customerName;
    public $addressLine1;
    public $addressLine2;
    public $feed_id;
    public $campaignName;
    public $contactId;
    public $reason = null;

    public $displayNumber = false;

    public $skippedContactId = null;

    protected $listeners = ['contactSkipped' => 'handleContactSkipped', 'updateSkill' => 'displayPhone'];

    public function mount()
    {
        $this->displayNumber = $this->resolveDisplayNumber();

        $this->loadContact();
    }

    public function displayPhone()
    {
        $this->loadContact();
    }

    public function handleContactSkipped()
    {
        // Remember the skipped contact so a stale Redis payload for it is not
        // rendered again before the dispatcher refreshes the key.
        $this->skippedContactId = $this->contactId;

        $this->loadContact();
    }

    public function loadContact()
    {
        $this->displayNumber = $this->resolveDisplayNumber();

        $userId = (int) Auth::id();

        // The dispatcher service is the only writer of the assigned number.
        // This panel just renders whatever is published, so no per-second
        // MySQL query is needed to pick a contact.
        $payload = app(DialerNumberService::class)->getShown($userId);

        if ($this->skippedContactId !== null
            && $payload
            && (int) ($payload['contact_id'] ?? 0) === (int) $this->skippedContactId) {
            $payload = null;
        } else {
            $this->skippedContactId = null;
        }

        if ($payload && !empty($payload['phone'])) {
            $this->fillFromPayload($payload);
        } elseif ($payload) {
            $this->clearContact();
            $this->reason = $payload['reason'] ?? 'No available contacts in your assigned campaigns.';
        } else {
            // The dispatcher has not published anything yet (e.g. it is not
            // running). Fall back to picking directly from MySQL so the agent
            // is never stuck on "Fetching...". Once the dispatcher is up it
            // takes over the Redis key and this branch stops running.
            $this->fallbackPick();
        }
    }

    protected function fallbackPick(): void
    {
        if (!$this->displayNumber) {
            $this->clearContact();
            $this->reason = 'Please Login to a campaign';

            return;
        }

        // Same selection as the lead window's Next Customer, so the dashboard
        // and the lead window always show the same contact.
        $next = app(DialerNumberService::class)->resolveCurrentContact(Auth::user());

        if ($next && !empty($next['phone'])) {
            $this->fillFromPayload($next);
        } else {
            $this->clearContact();
            $this->reason = $next['reason'] ?? 'No available contacts in your assigned campaigns.';
        }
    }

    protected function fillFromPayload(array $payload): void
    {
        $this->contactId = $payload['contact_id'] ?? null;
        $this->phone = $payload['phone'] ?? null;
        $this->phone2 = $payload['phone2'] ?? null;
        $this->customerName = $payload['customer_name'] ?? null;
        $this->addressLine1 = $payload['address_line_1'] ?? null;
        $this->addressLine2 = $payload['address_line_2'] ?? null;
        $this->feed_id = $payload['feed_id'] ?? null;
        $this->campaignName = $payload['campaign_name'] ?? null;
        $this->reason = null;
    }

    protected function clearContact(): void
    {
        $this->contactId = null;
        $this->phone = null;
        $this->phone2 = null;
        $this->customerName = null;
        $this->addressLine1 = null;
        $this->addressLine2 = null;
        $this->feed_id = null;
        $this->campaignName = null;
    }

    /**
     * Whether the panel should show a number. Uses the dispatcher's cached
     * "has active queued skill" flag; falls back to a direct check the first
     * time the dispatcher has not published anything yet.
     */
    protected function resolveDisplayNumber(): bool
    {
        $queued = app(DialerNumberService::class)->hasQueued((int) Auth::id());

        if ($queued !== null) {
            return $queued;
        }

        return Auth::user()->currentQueues()->active()->exists();
    }

    public function openProfile($phone, $phone2)
    {
        $number = !empty($phone) ? $phone : $phone2;

        $lead = $this->findLeadByPhone($number);

        if (!$lead) {
            $lead = new Lead();
            $lead->contact_number = $this->canonicalPhone($number);
            $lead->first_name = $this->customerName;
            $lead->address_line_1 = $this->addressLine1;
            $lead->address_line_2 = $this->addressLine2;
            $lead->status_id = 1;
            $lead->agent_id = auth()->user()->id ?? null;
            $lead->extension = auth()->user()->extension ?? null;
            $lead->skill_id = 0;
            $lead->save();
        }

        $url = route('leads.show', ['lead' => $lead->id]) . '?feed=' . $this->feed_id . '&cmp=' . $this->campaignName;

        $this->dispatchBrowserEvent('open-lead-window', [
            'url' => $url,
            'lead_id' => $lead->id,
            'feed_id' => $this->feed_id,
            'cmp' => $this->campaignName,
        ]);
    }

    protected function canonicalPhone($phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }

    protected function phoneCandidates($phone): array
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return [];
        }

        $candidates = [$digits];

        if (strlen($digits) === 9) {
            $local = '0' . $digits;
            $candidates[] = $local;
            $candidates[] = '94' . $digits;
            $candidates[] = '9' . $local;
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $local = substr($digits, 1);
            $candidates[] = $local;
            $candidates[] = '94' . $local;
            $candidates[] = '9' . $digits;
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '94')) {
            $local = substr($digits, 2);
            $candidates[] = $local;
            $candidates[] = '0' . $local;
            $candidates[] = '9' . '0' . $local;
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    protected function findLeadByPhone($phone): ?Lead
    {
        $candidates = $this->phoneCandidates($phone);

        if ($candidates === []) {
            return null;
        }

        return Lead::where(function ($query) use ($candidates) {
            $query->whereIn('contact_number', $candidates)
                ->orWhereIn('contact_number_2', $candidates);
        })->orderBy('id')->first();
    }
}