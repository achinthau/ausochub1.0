<div class="flex">

    @php
    switch($ticket->status) {
        case 'Closed':
            $color = 'text-teal-600 hover:bg-teal-600 hover:text-white';
            break;
        case 'Skip':
            $color = 'text-yellow-600 hover:bg-yellow-600 hover:text-white';
            break;
        case 'Remind':
            $color = 'text-blue-600 hover:bg-blue-600 hover:text-white';
            break;
        default:
            $color = 'text-teal-600 hover:bg-teal-600 hover:text-white';
            break;
    }
@endphp

<div class="flex space-x-1 justify-around">
    <a href="#"
       wire:click.prevent="$emitTo('cx-tickets.survey.view-panel', 'showCxTicketViewingModal', {{ $ticket->id }})"
       class="p-1 rounded {{ $color }}">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd"
                  d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                  clip-rule="evenodd"/>
        </svg>
    </a>
</div>

    {{-- <div class="flex space-x-1 justify-around">
        <a href="#"
            wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel', 'showCxTicketRatingModal',{{ $clientActivity }}, false)"
            class="p-1 px-6 bg-teal-600 text-black rounded-md">
            {{-- <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" viewBox="0 0 24 24">
                <path
                    d="M20 8h-5.612l1.123-3.367c.202-.608.1-1.282-.275-1.802S14.253 2 13.612 2H12c-.297 0-.578.132-.769.36L6.531 8H4c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h13.307a2.01 2.01 0 0 0 1.873-1.298l2.757-7.351A1 1 0 0 0 22 12v-2c0-1.103-.897-2-2-2zM4 10h2v9H4v-9zm16 1.819L17.307 19H8V9.362L12.468 4h1.146l-1.562 4.683A.998.998 0 0 0 13 10h7v1.819z">
                </path>
            </svg> 
            <button>Rate</button>
        </a>
    </div> --}}

    {{-- <div class="pl-2">
        <div class="py-1 px-4 bg-red-400 hover:bg-red-500 rounded-md"><a href="#"
                wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel', 'cancelRatings',{{ $clientActivity }})">Cancel</a>
        </div>
    </div> 

    <div class="flex space-x-1 justify-around pl-2">
        <a href="#"
    wire:click.prevent="$emitTo('cx-tickets.survey.rating-panel', 'showCxTicketRatingModal', {{ $clientActivity }}, true)"
    class="py-1 px-4 pt-1 bg-red-400 hover:bg-red-500 text-black rounded-md">
    <button >Cancel</button>
</a>

    </div>

    <div class="flex space-x-1 justify-around pl-2">
        <a href="#"
    wire:click.prevent="$emitTo('cx-tickets.survey.reopen-panel', 'showReOpenPanel', {{ $clientActivity }})"
    class="py-1 px-4 pt-1 bg-orange-400 hover:bg-orange-500 text-black rounded-md">
    <button >ReOpen</button>
</a>

    </div>
    
    <script>
        function confirmCancel(clientActivity) {
            if (confirm('Are you sure you want to cancel?')) {
                window.livewire.emitTo('cx-tickets.survey.rating-panel', 'cancelRatings', clientActivity);
            }
        }
    </script> --}}
    
</div>
