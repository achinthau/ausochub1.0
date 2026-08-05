<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CampaignAgentDialLimit;
use App\Models\DialerCallStatusOption;
use App\Models\FeedContactAttempt;
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

    protected function selectedReasonIds(): array
    {
        if (empty($this->selectedReasons)) {
            return [];
        }

        return DialerCallStatusOption::where('campaign_id', $this->campaignId)
            ->whereIn('option', $this->selectedReasons)
            ->pluck('id')
            ->values()
            ->toArray();
    }

    protected function selectedReasonTypes(array $ids): array
    {
        $types = [];
        foreach ($ids as $id) {
            $type = DialerCallStatusOption::where('id', $id)->value('type');
            if ($type !== null) {
                $types[] = (string) $type;
            }
        }
        return array_values(array_unique($types));
    }

    public function cancelRatings()
    {
        $ids = $this->selectedReasonIds();
        $types = $this->selectedReasonTypes($ids);
        if (empty($types)) {
            $types = ['3'];
        }

        if ($this->feed) {
            FeedContactAttempt::create([
                'feed_contact_valid_id' => $this->feed->id,
                'call_status_option_id' => implode(',', $ids),
                'call_status_option_type' => implode(',', $types),
                'rate' => null,
                'comments' => $this->CancelComment,
                'campaign_id' => $this->campaignId,
                'updated_by' => Auth::id(),
            ]);

            $this->feed->status = 1;
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

        $attempt = $this->feed
            ? FeedContactAttempt::where('feed_contact_valid_id', $this->feed->id)
                ->orderByDesc('id')
                ->first()
            : null;

        if ($attempt) {
            $this->rating = (int) $attempt->rate ?: 0;

            $ids = array_filter(explode(',', (string) $attempt->call_status_option_id));
            $names = DialerCallStatusOption::whereIn('id', $ids)->pluck('option')->toArray();

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

        $ids = $this->selectedReasonIds();

        if ($this->feed) {
            FeedContactAttempt::create([
                'feed_contact_valid_id' => $this->feed->id,
                'call_status_option_id' => implode(',', $ids),
                'call_status_option_type' => implode(',', $this->selectedReasonTypes($ids)),
                'rate' => $this->rating,
                'comments' => '',
                'campaign_id' => $this->campaignId,
                'updated_by' => Auth::id(),
            ]);

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
