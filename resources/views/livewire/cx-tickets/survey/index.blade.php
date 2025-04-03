<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('CX-Tickets Survey ')  }}
            </h2>
            {{-- <div class="flex space-x-1">
    
            <x-button icon="user-add" label="Add Ticket" onclick="$openModal('creatingCxTicket') " />
           </div> --}}
        </div>
    </x-slot>
    
    <div class="flex justify-between pt-4 w-full">
        
        <div class="w-full overflow-x-auto p-4">
            
            <livewire:cx-tickets-survey-table />
        </div>
    </div>
    
    @push('modals')
    @livewire('cx-tickets.survey.rating-panel')
@endpush