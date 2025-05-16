<?php

namespace App\Http\Livewire\Tickets;

use App\Models\CrmDepartment;
use App\Models\Item;
use App\Models\Outlet;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketItem;
use App\Models\User;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{

    use Actions;

    public $leadId;
    public $ticket;
    public $ticketItems = [];
    public $portionSize = [];
    public $size = [];
    public $items = [];
    public $categories;
    public $subCategories;
    public $outlets;
    public $tags;
    public $creatingTicket = false;
    
    public $departments;
    public $users;
    public $departmentUsers;

    protected $listeners = ['showCreatingTicket' => 'showCreatingTicket'];

    protected $rules  = [
        'ticket.topic' => 'required_if:ticket.ticket_category_id,1,2',
        'ticket.description' => 'required_if:ticket.ticket_category_id,1,2',
        // 'ticket.outlet_id' => 'required_if:ticket.ticket_category_id,3',
        'ticket.outlet_id' => 'required|exists:outlets,id',
        'ticket.lead_id' => 'nullable',
        'ticket.ticket_category_id' => 'required|exists:ticket_categories,id',
        'ticket.ticket_sub_category_id' => 'required|exists:ticket_sub_categories,id',
        'ticket.tags' => 'required|array|min:1',
        'ticket.crm' => 'required',
        'ticket.order_ref' => 'required_if:ticket.crm,false',
        //'ticketItems.*.item_id' => 'required_if:ticket.crm,true',
        //'ticketItems.*.item_id' => 'required_if:ticket.ticket_category_id,3',
        //'ticketItems.*.size_id' => 'required_if:ticket.crm,true',
        //'ticketItems.*.size_id' => 'required_if:ticket.ticket_category_id,3',

        'ticket.department_id' => 'nullable',
        'ticket.assigned_user_id' => 'nullable',
    ];

    protected $validationAttributes = [
        'ticket.ticket_category_id' => 'category',
        'ticket.ticket_sub_category_id' => 'sub category',
        'ticket.outlet_id' => 'outlet',
        'ticketItems.*.item_id' => 'item',
        'ticketItems.*.size_id' => 'size',
    ];


    protected $messages = [
        'ticketItems.*.item_id.required_if' => 'The item field is required.',
        'ticketItems.*.size_id.required_if' => 'The size field is required.',
        'ticket.order_ref.required_if' => 'The order ref field is required.',
        'ticket.topic.required_if' => 'The topic field is required.',
        'ticket.description.required_if' => 'The description field is required.',
    ];


    public function mount($leadId = null)
    {
        $this->ticket = new Ticket;
        $this->ticket->lead_id = $leadId;

        $this->categories = TicketCategory::with('subCategories')->primary()->get();
        $this->subCategories = [];
        $this->tags = [];
        $this->outlets = Outlet::select('id', 'title')->get();
        $this->items = Item::select('id', 'title', 'description')->get()->toArray();

        $this->portionSize = [
            ['name' => 'Small', 'id' => 1, 'description' => 'For 2 Pax'],
            ['name' => 'Large', 'id' => 2, 'description' => 'For 3-4 Pax'],

        ];

        $this->addItem();

        $this->departments = CrmDepartment::select('id','name')->get()->toArray();
        $this->users = User::select('id', 'name', 'department_id')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.tickets.create');
    }

    public function showCreatingTicket()
    {
        $this->creatingTicket = true;
    }
    public function updatedTicketTicketCategoryId($value)
    {
        if ($value) {
            $selectedCategory = $this->categories->where('id', $value)->first();
            $this->subCategories = $selectedCategory->subCategories ?? [];

            // $this->ticket->outlet_id = $value != 3 ? null : 0;
            $this->ticket->outlet_id = 0;
        } else {
        }
        $this->ticket['ticket_sub_category_id'] = 0;
        $this->ticket['tags'] =  [];
        $this->tags = [];


        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedTicketTicketSubCategoryId($value)
    {
        if ($value) {
            $selectedSubCategory = $this->subCategories->where('id', $value)->first();
            $this->tags = $selectedSubCategory->tags ?? [];
        }
        $this->ticket['tags'] =  [];
    }


    public function updatedCreatingTicket($value)
    {
        $this->resetForm();
    }

    public function updatedTicketCrm($value) {}


    public function addItem()
    {
        array_push($this->ticketItems, [
            'id' => 0,
            'item_id' => null,
            'size_id' => null,
            'qty' => 1,
        ]);
    }

    public function removeItem($index)
    {
        array_splice($this->ticketItems, 1, $index);
    }

    public function save()
    {
        $this->validate();
        if ($this->ticket->ticket_category_id == 3 && $this->ticket->crm) {
            $this->validate([
                'ticketItems.*.item_id' => 'required',
                'ticketItems.*.size_id' => 'required',
            ]);
        }

        $this->ticket->topic = $this->ticket->ticket_category_id == 3 ? "Order" : $this->ticket->topic;
        
        $this->ticket->save();
        

        $this->ticket->logActivity('Ticket created');

        //Test
        if ($this->ticket->ticket_category_id == 3 && $this->ticket->crm) {
            foreach ($this->ticketItems as $key => $_ticketItem) {
                $ticketItem = TicketItem::updateOrCreate(
                    [
                        'id' => $_ticketItem['id']
                    ],
                    [
                        'item_id'  => $_ticketItem['item_id'],
                        'ticket_id'  => $this->ticket->id,
                        'portion_id'  => $_ticketItem['size_id'],
                        'qty'  => $_ticketItem['qty'],
                    ]
                );
            }
        }   

        // $this->ticket->refresh();
        // dd($this->ticket->subCategory);
        if (config('auso.ticket_sla_enabled') && $this->ticket->subCategory->due_in_hours) {
            $this->ticket->due_at = $this->ticket->created_at->addSeconds($this->ticket->subCategory->due_in_hours * 60 * 60);
            $this->ticket->save();
        }

        $this->creatingTicket = false;
        $this->resetForm();
        $this->notification()->success(
            $title = 'Success',
            $description = 'Ticket successfull created'
        );

        if (!$this->leadId) {
            $this->emitTo('tickets.index', 'refreshList');
        } else {
            $this->emitTo('leads.show', 'refreshCard');
        }
    }


    public function resetForm()
    {
        $this->ticket = new Ticket;
        $this->ticket->lead_id = $this->leadId;
        $this->tags = [];
        $this->ticketItems = [];
        $this->addItem();

        $this->resetErrorBag();
        $this->resetValidation();
        // $this->emit('refreshDatatable');
        redirect(route('tickets.index'));
    }


    public function updatedTicketDepartmentId($value)
{
    $this->departmentUsers = User::where('department_id', $value)
                                 ->select('id', 'name')
                                 ->get()
                                 ->toArray();
}
}
