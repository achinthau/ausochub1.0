<?php

namespace App\Http\Livewire\CxTickets\Survey;

use Livewire\Component;
use App\Models\CxSatisReason;
use App\Models\CxDisSatisReason;

class RatingReasons extends Component
{
    public array $satisfactionReasons = [];
    public array $dissatisfactionReasons = [];
    public array $selectedReasons = [];

    public $selectedSatisfactionReason = null;
    public $selectedDissatisfactionReason = null;

    public function mount()
    {
        // Fetch reasons dynamically from the database
        $this->satisfactionReasons = CxSatisReason::pluck('reasons')->toArray();
        $this->dissatisfactionReasons = CxDisSatisReason::pluck('reasons')->toArray();
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

    public function render()
    {
        return view('livewire.cx-tickets.survey.rating-reasons');
    }
}
