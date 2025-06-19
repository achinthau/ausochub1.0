<?php

namespace App\Http\Livewire\Leads;

use App\Models\CallCount;
use App\Models\Lead;
use App\Models\QueueCount;
use App\Models\Ticket;
use Livewire\Component;
use WireUi\Traits\Actions;

class Show extends Component
{
    use Actions;

    public Lead $lead;
    public  $tickets;
    public  $callLogs;
    public  $outCallLogs;
    public  $timelineLogs;

    protected $listeners = ['refreshCard' => 'refreshCard'];

    protected $rules = [
        'lead.contact_number' => 'required',
        'lead.skill.skillname' => 'required',
        'lead.first_name' => 'required',
        'lead.last_name' => 'nullable',
        'lead.nic' => 'nullable',
        'lead.address_line_1' => 'nullable',
        'lead.address_line_2' => 'nullable',
        'lead.city' => 'nullable',
        'lead.contact_number_2' => 'nullable',
        'lead.email' => 'nullable|email',
        //'lead.priority_level_id'=>'required',
        //'lead.satisfaction_level_id'=>'required',

    ];

    public $moodStatus = false;
public $comment = '';

public function toggle()
{
    $this->moodStatus = !$this->moodStatus;
    $ani = $this->lead->contact_number;

    if ($ani) {
        $latestRecord = QueueCount::where('ani', $ani)
            ->where('status', 2)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestRecord) {
            if ($this->moodStatus) {
                // Toggle is ON (unsatisfied) – store reaction only
                $latestRecord->customer_reaction = 1;
                $latestRecord->save();
            } else {
                // Toggle is OFF – clear reaction and comment
                $latestRecord->customer_reaction = null;
                $latestRecord->comment = null;
                $latestRecord->save();

                // Also clear local state if needed
                $this->comment = '';
                session()->forget('message');
            }
        }
    }
}


public function submitReaction()
{
    $ani = $this->lead->contact_number;

    if ($ani) {
        $latestRecord = QueueCount::where('ani', $ani)->where('status', 2)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestRecord) {
            $latestRecord->customer_reaction = 1;
            $latestRecord->comment = $this->comment;
            $latestRecord->save();
        }
    }

    session()->flash('message', 'Feedback submitted successfully!');
    // $this->moodStatus = false;
    $this->comment = '';
}


    public function mount($lead)
    {
        $this->lead = $lead->load('tickets', 'tickets.category', 'tickets.status', 'tickets.outlet', 'orders', 'orders.items');
    }

    public function render()
    {
        return view('livewire.leads.show');
    }

    public function refreshCard()
    {
        $this->lead->refresh();
        $this->refreshTimeline();
    }

    public function refreshTimeline()
    {
        $this->emitTo('leads.partials.activity-log', 'refreshTimeline');
    }

    public function save()
    {
        $this->validate();
        $this->lead->status_id = 2;
        $this->lead->save();

        $this->notification()->success(
            $title = 'Success',
            $description = 'Customer successfull saved'
        );
    }

    public function openWhatsApp()
    {

        $number = preg_replace('/\s+/', '', $this->lead->whatsapp);

        if (str_starts_with($number, '94')) {
            $internationalNumber = $number;
        } elseif (str_starts_with($number, '0')) {
            $internationalNumber = '94' . substr($number, 1);
        } else {
            $internationalNumber = '94' . $number;
        }
        // dd($internationalNumber);

        $message = urlencode('Hello! I would like to chat with you.');
        $url = "https://wa.me/{$internationalNumber}?text={$message}";


        $this->emit('whatsappOpened', $url);
    }
}
