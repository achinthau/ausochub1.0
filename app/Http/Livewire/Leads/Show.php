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

    public function mount($lead)
    {
        $this->lead = $lead->load('tickets','tickets.category','tickets.status','tickets.outlet','orders','orders.items');
        $this->refreshTimeline();
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
        $lead = $this->lead->load('tickets', 'tickets.category', 'tickets.status', 'tickets.outlet', 'orders', 'orders.items');
        $this->callLogs = QueueCount::with('agentInfo')->where(function ($query) use ($lead) {
            $query->orWhere('ani', $lead->contact_number)
                ->orWhere('dnis', $lead->contact_number);
        })->where('status', 2)->orderBy('date')->get();

        $this->outCallLogs = CallCount::with('agentInfo')->where(function ($query) use ($lead) {
            $query->orWhere('dnis', $lead->contact_number);
        })->where('direction', 'out')->orderBy('date')->get();

        $timelineLogs = collect([]);

        // Map call logs
        $timelineLogs = $timelineLogs->merge($this->callLogs->map(function ($item) {
            return [
                'title' => 'Incoming Call',
                'created_at' => $item->date,
                'created_date' => $item->date->format('F jS, Y'),
                'created_time' => $item->date->format('h:i A'),
                'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                'icon' => 'icon-phone',
                'bg-color' => 'bg-blue-200',
                'icon-color' => 'text-blue-600 dark:text-blue-400',
            ];
        }));

        // Map out call logs
        $timelineLogs = $timelineLogs->merge($this->outCallLogs->map(function ($item) {
            return [
                'title' => 'Outgoing Call',
                'created_at' => $item->date,
                'created_date' => $item->date->format('F jS, Y'),
                'created_time' => $item->date->format('h:i A'),
                'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                'icon' => 'icon-phone-out',
                'bg-color' => 'bg-blue-200',
                'icon-color' => 'text-blue-600 dark:text-blue-400',
            ];
        }));

        // Map lead tickets (with null check)
        if ($this->lead->tickets) {
            $timelineLogs = $timelineLogs->merge($this->lead->tickets->map(function ($item) {
                return [
                    'id' => $item->id,
                    'component' => $item->ticket_category_id == 3 ? 'orders.show' : 'tickets.show',
                    'action' => $item->ticket_category_id == 3 ? 'openTicket' : 'openTicket',
                    'title' => $item->ticket_category_id == 3 ? 'Order' : 'Ticket',
                    'outlet' => $item->outlet ? $item->outlet->title  : false,
                    'category' => $item->category->title,
                    'status' => $item->status->title,
                    'order_total' =>  $item->ticket_category_id == 3 ? $item->order_total  : 0,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'created_date' => $item->created_at->format('F jS, Y'),
                    'created_time' => $item->created_at->format('h:i A'),
                    'created_by' => 'System Agent',
                    'icon' => $item->ticket_category_id == 3 ? 'icon-order' : 'icon-ticket',
                    'bg-color' =>  $item->ticket_category_id == 3 ? 'bg-green-200' : 'bg-red-200',
                    'icon-color' =>  $item->ticket_category_id == 3 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                ];
            }));
        }

        // Sort the merged collection by created_at
        $this->timelineLogs = $timelineLogs->sortByDesc('created_at');
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
}
