<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Service-Tickets ')  }}
            </h2>
            <div class="flex space-x-1">
    
            <x-button icon="user-add" label="Add Ticket" onclick="$openModal('creatingCxTicket') " />
           </div>
        </div>
    </x-slot>
    
    <div class="flex justify-between pt-4 w-full">
        {{-- <div class="flex mx-auto pt-4 max-w-7xl"> --}}
        
        <div class="w-full overflow-x-auto p-4 mx-8">
            <div class="flex justify-between pb-4 gap-4">

                <div  wire:click="$emit('filterTicketsByStatus', 'Open')" class="flex-1">
                    @livewire('cx-tickets.counts.open')
                </div>
                <div wire:click="$emit('filterTicketsByStatus', 'ReOpened')"  class="flex-1">
                @livewire('cx-tickets.counts.re-opened')
                </div>
                <div wire:click="$emit('filterTicketsByStatus', 'Closed')"  class="flex-1">
                @livewire('cx-tickets.counts.closed')
                </div>
                <div wire:click="$emit('filterTicketsByStatus', 'Canceled')"  class="flex-1">
                @livewire('cx-tickets.counts.canceled')
                </div>

                </div>
                <div class="flex justify-between pb-4 gap-4">
                <div wire:click="$emit('filterTicketsByStatus', 'Rated')"  class="flex-1">
                @livewire('cx-tickets.counts.rated')
                </div>

                <div wire:click="$emit('filterTicketsByStatus', 'Satisfied')"  class="flex-1">
                @livewire('cx-tickets.counts.satisfied')
                </div>

                <div wire:click="$emit('filterTicketsByStatus', 'Unsatisfied')" class="flex-1">
                
                @livewire('cx-tickets.counts.un-satisfied')
                </div>
                <div wire:click="$emit('filterTicketsByStatus', 'Passive')" class="flex-1">
                
                @livewire('cx-tickets.counts.passive')
                </div>
            </div>
            <livewire:cx-tickets-table />
        </div>
    </div>
    
@push('modals')
    @livewire('cx-tickets.create-cx-ticket')
    @livewire('cx-tickets.edit')
@endpush