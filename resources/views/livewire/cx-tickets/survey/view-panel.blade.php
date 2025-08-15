<x-modal.card title="Service Ticket View Panel" blur align="center" wire:model="CxTicketViewingModal">
<div>
@if($ticket)
        <div class="text-sm"> {{-- smaller text for all --}}

    {{-- Product Details --}}
    <div class="border p-1 rounded-lg shadow-md mt-0">
        <h1 class="p-1 pl-0 font-bold text-lg">Product Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Category:</label>
                    <span>{{ $ticket->category }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Product:</label>
                    <span>{{ $ticket->product }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Model:</label>
                    <span>{{ $ticket->model }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Work Order No:</label>
                    <span>{{ $ticket->work_order_no }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Service Center:</label>
                    <span>{{ $ticket->service_center }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Warranty Status:</label>
                    <span>{{ $ticket->warranty_status }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Sold Date:</label>
                    <span>{{ $ticket->sold_date }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Details --}}
    <div class="border p-2 rounded-lg shadow-md mt-4">
        <h1 class="p-1 pl-0 font-bold text-lg">Customer Details</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Customer Name:</label>
                    <span>{{ $ticket->customer_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Customer Address:</label>
                    <span>{{ $ticket->customer_address }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Customer Contact 01:</label>
                    <span>{{ $ticket->customer_contact_01 }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Customer Contact 02:</label>
                    <span>{{ $ticket->customer_contact_02 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Technician Details --}}
    <div class="border p-2 rounded-lg shadow-md mt-4">
        <h1 class="p-1 pl-0 font-bold text-lg">Technician Details</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Technician Name:</label>
                    <span>{{ $ticket->technician_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Technician Contact:</label>
                    <span>{{ $ticket->technician_contact }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Supervisor Name:</label>
                    <span>{{ $ticket->supervisor_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-semibold">Supervisor Contact:</label>
                    <span>{{ $ticket->supervisor_contact }}</span>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="flex pt-2">
    <div class="flex space-x-1 justify-around">
        <a href="#"
            wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel', 'showCxTicketRatingModal',{{ $ticket->id }}, false)"
            class="p-1 px-6 bg-teal-600 text-black rounded-md">
            
            <button>Rate</button>
        </a>
    </div>
    <div class="flex space-x-1 justify-around pl-2">
        <a href="#"
    wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel', 'showCxTicketRatingModal', {{  $ticket->id }}, true)"
    class="py-1 px-4 pt-1 bg-red-400 hover:bg-red-500 text-black rounded-md">
    <button >Cancel</button>
</a>

    </div>

    <div class="flex space-x-1 justify-around pl-2">
        <a href="#"
    wire:click.prevent="$emitTo('cx-tickets.survey.reopen-panel', 'showReOpenPanel', {{  $ticket->id }})"
    class="py-1 px-4 pt-1 bg-orange-400 hover:bg-orange-500 text-black rounded-md">
    <button >ReOpen</button>
</a>

    </div>
    
</div>
@endif
</div>
</x-modal.card>