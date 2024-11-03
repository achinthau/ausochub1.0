<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsteriskEvents extends Component
{
    public $events = [];


    protected $listeners = ['echo:channel-events,AsteriskEvent' => 'updatedEventData'];

    public function mount()
    {
        // Initial fetch of data
        // $this->fetchEvents();
    }

    public function render()
    {
        return view('livewire.reports.asterisk-events');
    }

    public function fetchEvents()
    {
        try {
            // Implement fetching logic if needed
        } catch (\Exception $e) {
            Log::error('Error fetching data from Node.js API: ' . $e->getMessage());
            // Optionally, handle errors (show an alert, retry logic, etc.)
        }
    }

    public function hydrate()
    {
        // Optional: You can use this hook for any setup needed on each Livewire request
    }

    public function updatedEvents($data)
    {
        // Handle event data update
        // $this->events = $data;
    }

    public function updatedEventData($data)
    {
        // Handle event data update
        $channelStateDesc = $data['channelStateDesc'];
        $this->events[$channelStateDesc] = $data;

        // Livewire will automatically re-render the component with updated data
    }

    // protected $listeners = ['echo:channel-events,AsteriskEvent' => 'updatedAsteriskEvent'];

}