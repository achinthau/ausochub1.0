<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CampaignAgentDialLimit;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactValid;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SatisfactionRatingPanel extends Component
{
    public $cxTicketRatingModal = false;
    public $ratingLabel;

    public $rating = 0;
    public $hoverRating = 0;

    protected $listeners = ['showCxTicketRating' => 'showCxTicketRating'];

    public $feedContactId;
    public $feed = null;
    public $campaignId;

    public array $satisfactionReasons = [];
    public array $dissatisfactionReasons = [];
    public array $cancelReasons = [];
    public array $selectedReasons = [];

    public $selectedSatisfactionReason = null;
    public $selectedDissatisfactionReason = null;
    public $selectedCancellingReason = null;

    public $isCancel = false;
    public $CancelComment = '';

    public function updatedSelectedSatisfactionReason($value)
    {
        if ($value) {
            $this->selectReason($value);
        }
    }

    public function updatedSelectedDissatisfactionReason($value)
    {
        if ($value) {
            $this->selectReason($value);
        }
    }

    public function updatedselectedCancellingReason($value)
    {
        if ($value) {
            $this->selectReason($value);
        }
    }

    public function selectReason($reason)
    {
        if (!in_array($reason, $this->selectedReasons)) {
            $this->selectedReasons[] = $reason;
        }
    }

    public function removeReason($reason)
    {
        $this->selectedReasons = array_filter($this->selectedReasons, fn($r) => $r !== $reason);
    }

    protected function loadCampaignReasons()
    {
        $options = DialerCallStatusOption::where('campaign_id', $this->campaignId)->get();

        $this->satisfactionReasons = $options->where('type', 1)->pluck('option')->values()->toArray();
        $this->dissatisfactionReasons = $options->where('type', 2)->pluck('option')->values()->toArray();
        $this->cancelReasons = $options->where('type', 3)->pluck('option')->values()->toArray();
    }

    protected function selectedReasonTypes(): array
    {
        if (empty($this->selectedReasons)) {
            return [];
        }

        $types = DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->whereIn('option', $this->selectedReasons)
            ->pluck('type')
            ->map(fn ($type) => (string) $type)
            ->unique()
            ->values()
            ->toArray();

        return array_values(array_filter($types));
    }

    protected function hasChangeRequestReason(): bool
    {
        return collect($this->selectedReasons)
            ->map(fn ($reason) => str_replace(['_', ' '], '', strtolower((string) trim($reason))))
            ->contains('changerequest');
    }

    public function cancelRatings()
    {
        $types = $this->selectedReasonTypes();
        if (empty($types)) {
            $types = ['3'];
        }

        $isChangeRequest = $this->hasChangeRequestReason();

        if ($isChangeRequest && !in_array('change_request', $types)) {
            $types[] = 'change_request';
        }

        if ($this->feed) {
            $this->feed->call_status_option_id = implode(',', array_values(array_unique($this->selectedReasons)));
            $this->feed->call_status_option_type = implode(',', $types);
            $this->feed->rate = null;
            $this->feed->comments = $this->CancelComment;
            $this->feed->campaign_id = $this->campaignId;
            $this->feed->updated_by = Auth::id();
            $this->feed->attempted_at = now();
            $this->feed->status = $isChangeRequest ? 5 : 4;
            $this->feed->save();
            CampaignAgentDialLimit::incrementForFeed((int) $this->feed->feed_id, (int) Auth::id());
        }

        $this->emit('cxTicketSurveyUpdated');
        $this->emit('FeedCompleted');
        $this->cxTicketRatingModal = false;
    }

    public function showCxTicketRating($feedContactId, $isCancel, $campaignId)
    {
        $this->feedContactId = $feedContactId;
        $this->campaignId = $campaignId;
        $this->cxTicketRatingModal = true;

        $this->feed = $feedContactId ? FeedContactValid::find($feedContactId) : null;

        $this->loadCampaignReasons();

        $this->isCancel = $isCancel;
        $this->rating = 0;
        $this->hoverRating = 0;
        $this->selectedReasons = [];
        $this->CancelComment = '';

        $attempt = $this->feed;
        $hasAttempt = $attempt && ($attempt->rate !== null
            || $attempt->call_status_option_id !== null
            || $attempt->call_status_option_type !== null
            || $attempt->comments !== null);

        if ($hasAttempt) {
            $this->rating = (int) $attempt->rate ?: 0;

            $names = array_filter(array_map('trim', explode(',', (string) $attempt->call_status_option_id)));

            if ($isCancel) {
                $this->selectedReasons = array_values(array_intersect($names, $this->cancelReasons));
            } else {
                $this->selectedReasons = array_values(array_unique(array_merge(
                    array_intersect($names, $this->satisfactionReasons),
                    array_intersect($names, $this->dissatisfactionReasons)
                )));
            }

            $this->CancelComment = $attempt->comments ?? '';
        }
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        $this->hoverRating = $rating;
        $this->emit('set-rating', $this->rating);

        switch ($rating) {
            case 1:
                $this->ratingLabel = 'Very Bad !!!';
                break;
            case 2:
                $this->ratingLabel = 'Bad !!!';
                break;
            case 3:
                $this->ratingLabel = 'Neutral !!!';
                break;
            case 4:
                $this->ratingLabel = 'Good !!!';
                break;
            case 5:
                $this->ratingLabel = 'Excellent !!!';
                break;
            default:
                $this->ratingLabel = 'Neutral !!!';
        }
    }

    public function setHoverRating($rating)
    {
        $this->hoverRating = $rating;
    }

    public function resetHoverRating()
    {
        $this->hoverRating = $this->rating;
    }

    public function rate()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5'
        ], [
            'rating.required' => 'Please select a rating before submitting.',
            'rating.integer' => 'Please select a rating before submitting.',
            'rating.min' => 'Please select a rating before submitting.',
            'rating.max' => 'Please select a rating before submitting.'
        ]);

        if ($this->feed) {
            $this->feed->call_status_option_id = implode(',', array_values(array_unique($this->selectedReasons)));
            $this->feed->call_status_option_type = '1';
            $this->feed->rate = $this->rating;
            $this->feed->comments = '';
            $this->feed->campaign_id = $this->campaignId;
            $this->feed->updated_by = Auth::id();
            $this->feed->attempted_at = now();
            $this->feed->status = 1;
            $this->feed->next_available_at = null;
            $this->feed->save();
            CampaignAgentDialLimit::incrementForFeed((int) $this->feed->feed_id, (int) Auth::id());
        }

        $this->cxTicketRatingModal = false;
        $this->emit('cxTicketSurveyUpdated');
        $this->emit('FeedCompleted');
    }

    public function render()
    {
        return view('livewire.cx-tickets.survey.satisfaction-rating-panel');
    }
}
