<x-modal.card title="Service Ticket Information" blur align="center" wire:model="CxTicketViewingModal">
<div>
@if($ticket)
        <div class="text-sm"> {{-- smaller text for all --}}

    {{-- Product Details --}}
    <div class="border p-1 rounded-lg shadow-md mt-0">
        <h1 class="p-1 pl-0 font-bold text-lg">Product Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Category:</label>
                    <span>{{ $ticket->category }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Product:</label>
                    <span>{{ $ticket->product }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Model:</label>
                    <span>{{ $ticket->model }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Work Order No:</label>
                    <span>{{ $ticket->work_order_no }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Service Center:</label>
                    <span>{{ $ticket->service_center }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Warranty Status:</label>
                    <span>{{ $ticket->warranty_status }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Sold Date:</label>
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
                    <label class="font-bold">Customer Name:</label>
                    <span>{{ $ticket->customer_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Customer Address:</label>
                    <span>{{ $ticket->customer_address }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Customer Contact 01:</label>
                    <span>{{ $ticket->customer_contact_01 }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Customer Contact 02:</label>
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
                    <label class="font-bold">Technician Name:</label>
                    <span>{{ $ticket->technician_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Technician Contact:</label>
                    <span>{{ $ticket->technician_contact }}</span>
                </div>
            </div>
            <div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Supervisor Name:</label>
                    <span>{{ $ticket->supervisor_name }}</span>
                </div>
                <div class="py-2 flex gap-2">
                    <label class="font-bold">Supervisor Contact:</label>
                    <span>{{ $ticket->supervisor_contact }}</span>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="border p-2 rounded-lg shadow-md mt-4">
    @if($ticket->status =="Skip")
<h1 class="p-1 pl-0 font-bold text-lg">Skipped Reasons</h1>
<div class="flex pt-2 space-x-3">
    <ul class="list-disc ml-4">
@foreach(explode(',', $ticket->skipped_reasons) as $reason)
    <li>{{ trim($reason) }}</li>
@endforeach
</ul>

</div>
@endif
</div>



<div class="flex pt-2 space-x-3">

    {{-- RATE --}}
    <div class="group relative inline-flex">
        <a href="#"
           wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel','showCxTicketRatingModal', {{ $ticket->id }}, false)"
           class="p-2 bg-teal-500 hover:bg-teal-600 text-black rounded-md">
            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"> <path d="M313.4 32.9c26 5.2 42.9 30.5 37.7 56.5l-2.3 11.4c-5.3 26.7-15.1 52.1-28.8 75.2l144 0c26.5 0 48 21.5 48 48c0 18.5-10.5 34.6-25.9 42.6C497 275.4 504 288.9 504 304c0 23.4-16.8 42.9-38.9 47.1c4.4 7.3 6.9 15.8 6.9 24.9c0 21.3-13.9 39.4-33.1 45.6c.7 3.3 1.1 6.8 1.1 10.4c0 26.5-21.5 48-48 48l-97.5 0c-19 0-37.5-5.6-53.3-16.1l-38.5-25.7C176 420.4 160 390.4 160 358.3l0-38.3 0-48 0-24.9c0-29.2 13.3-56.7 36-75l7.4-5.9c26.5-21.2 44.6-51 51.2-84.2l2.3-11.4c5.2-26 30.5-42.9 56.5-37.7zM32 192l64 0c17.7 0 32 14.3 32 32l0 224c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32-14.3-32-32L0 224c0-17.7 14.3-32 32-32z"/> </svg>
        </a>
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Rate</span>
    </div>

    {{-- CANCEL --}}
    <div class="group relative inline-flex">
        <a href="#"
           wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel','showCxTicketRatingModal', {{ $ticket->id }}, true)"
           class="p-2 bg-red-400 hover:bg-red-500 text-black rounded-md">
            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"> <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/> </svg>
        </a>
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Cancel</span>
    </div>

    {{-- REOPEN --}}
    <div class="group relative inline-flex">
        <a href="#"
           wire:click.prevent="$emitTo('cx-tickets.survey.reopen-panel','showReOpenPanel', {{ $ticket->id }}, 'reopen')"
           class="p-2 bg-orange-400 hover:bg-orange-500 text-black rounded-md">
            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"> <path d="M5 4a2 2 0 0 0-2 2v6H0l4 4 4-4H5V6h7l2-2H5zm10 4h-3l4-4 4 4h-3v6a2 2 0 0 1-2 2H6l2-2h7V8z"/> </svg>
        </a>
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">ReOpen</span>
    </div>

    {{-- SKIP --}}
    <div class="group relative inline-flex">
        <a href="#"
           wire:click.prevent="$emitTo('cx-tickets.survey.reopen-panel','showReOpenPanel', {{ $ticket->id }}, 'skip')"
           class="p-2 bg-yellow-400 hover:bg-yellow-500 text-black rounded-md">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M2 5v14c0 .86 1.012 1.318 1.659 .753l8 -7a1 1 0 0 0 0 -1.506l-8 -7c-.647 -.565 -1.659 -.106 -1.659 .753z"/>
                <path d="M13 5v14c0 .86 1.012 1.318 1.659 .753l8 -7a1 1 0 0 0 0 -1.506l-8 -7c-.647 -.565 -1.659 -.106 -1.659 .753z"/>
            </svg>
        </a>
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Skip</span>
    </div>

    {{-- REMIND --}}
    <div class="group relative inline-flex">
        <a href="#"
           wire:click.prevent="$emitTo('cx-tickets.survey.reopen-panel','showReOpenPanel', {{ $ticket->id }}, 'remind')"
           class="p-2 bg-blue-400 hover:bg-blue-500 text-black rounded-md">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
        </a>
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Remind</span>
    </div>

</div>



@endif
</div>
</x-modal.card>