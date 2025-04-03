<?php

namespace App\Http\Livewire\CxTickets\Survey;

use App\Models\CxDisSatisReason;
use App\Models\CxSatisReason;
use App\Models\CxTicket;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RatingPanel extends Component
{

    public $cxTicketRatingModal = false;
    public $ratingLabel;

    public $rating = 0;
    public $hoverRating = 0;

    protected $listeners = ['showCxTicketRatingModal' => 'showCxTicketRating', 'cancelRatings' => 'cancelRatings'];

    public $ticket_id;

    public array $satisfactionReasons = [];
    public array $dissatisfactionReasons = [];
    public array $cancelReasons = [];
    public array $selectedReasons = [];

    public $selectedSatisfactionReason = null;
    public $selectedDissatisfactionReason = null;
    public $selectedCancellingReason = null;

    public $isCancel = false;

    public function mount()
    {
        // Fetch reasons dynamically from the database
        $this->satisfactionReasons = CxSatisReason::where('type', 'satisfaction')->pluck('reasons')->toArray();
        $this->dissatisfactionReasons = CxSatisReason::where('type', 'dissatisfaction')->pluck('reasons')->toArray();
        $this->cancelReasons = CxSatisReason::where('type', 'cancel')->pluck('reasons')->toArray();
    }

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

    public function cancelRatings()
    {
        $ticket = CxTicket::find($this->ticket_id);
        if ($ticket) {

            $cancelReasons = array_intersect($this->selectedReasons, $this->cancelReasons);

            $ticket->update([
                'status' => "Canceled",
                'cancelling_reasons' => implode(',', $cancelReasons),
            ]);
        }
        $this->emit('cxTicketSurveyUpdated');
        $this->cxTicketRatingModal = false;
    }


    public function showCxTicketRating($id, $isCancel)
    {
        $this->ticket_id = $id;
        $this->cxTicketRatingModal = true;

        $this->isCancel = $isCancel;

        $ticket = CxTicket::find($id);

        
            if ($ticket) {
                $this->rating = $ticket->satisfaction_rate;
    
                if(!$isCancel)
        {
    
                $this->selectedReasons = array_filter(array_merge(
                    explode(',', $ticket->satisfaction_reasons ?? ''),
                    explode(',', $ticket->dis_satisfaction_reasons ?? '')
                ));
            }
            else
        {
            $this->selectedReasons = array_filter(array_merge(
                explode(',', $ticket->cancelling_reasons ?? ''),
            ));
        }
        }
        
    }


    public function setRating($rating)
    {
        // Log::info('Set Ratings', ['time' => now()]);
        $this->rating = $rating;
        $this->hoverRating = $rating; // Ensure the clicked emoji remains highlighted
        $this->emit('set-rating', $this->rating);
        // dd($this->rating);


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
                $this->ratingLabel = 'Neutral !!!'; // Default case
        }
    }

    public function setHoverRating($rating)
    {
        $this->hoverRating = $rating;
    }

    public function resetHoverRating()
    {
        $this->hoverRating = $this->rating; // Ensure the clicked emoji remains highlighted
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

        $ticket = CxTicket::find($this->ticket_id);

        if ($ticket) {
            $satisfaction = array_intersect($this->selectedReasons, $this->satisfactionReasons);
            $dissatisfaction = array_intersect($this->selectedReasons, $this->dissatisfactionReasons);

            $ticket->update([
                'satisfaction_rate' => $this->rating,
                'satisfaction_reasons' => implode(',', $satisfaction),
                'dis_satisfaction_reasons' => implode(',', $dissatisfaction),
                'status' => "Rated",
            ]);

            $this->cxTicketRatingModal = false;
            $this->emit('cxTicketSurveyUpdated');
        }
    }


    public function render()
    {
        return view('livewire.cx-tickets.survey.rating-panel');
    }
}
