<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Service-Tickets ')  }}
            </h2>
            <div class="flex space-x-2">
                                <x-button icon="user-add" label="Add Ticket" onclick="$openModal('creatingCxTicket') " />

                <!-- <x-button icon="chart-bar" class="bg-blue-500 hover:bg-blue-600" label="Analytics" onclick="window.open('/admin/technician-performance-dashboard', '_blank')" /> -->
                <x-button
    class="relative group flex items-center"
    href="{{ route('filament.pages.technician-performance-dashboard') }}"
>
    <svg
        class="w-6 h-6 text-blue-500"
        fill="currentColor"
        viewBox="0 0 24 24"
    >
        <path d="M15.86 4.39V19.39C15.86 21.06 17 22 18.25 22C19.39 22 20.64 21.21 20.64 19.39V4.5C20.64 2.96 19.5 2 18.25 2S15.86 3.06 15.86 4.39M9.61 12V19.39C9.61 21.07 10.77 22 12 22C13.14 22 14.39 21.21 14.39 19.39V12.11C14.39 10.57 13.25 9.61 12 9.61S9.61 10.67 9.61 12M5.75 17.23C7.07 17.23 8.14 18.3 8.14 19.61C8.14 20.93 7.07 22 5.75 22S3.36 20.93 3.36 19.61C3.36 18.3 4.43 17.23 5.75 17.23Z"/>
    </svg>

    <span
        class="absolute bottom-full mb-2 hidden group-hover:block
               bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap"
    >
        Analytics
    </span>
</x-button>




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
                <div wire:click="$emit('filterTicketsByStatus', 'Skip')"  class="flex-1">
                @livewire('cx-tickets.counts.skipped')
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
                <div wire:click="$emit('filterTicketsByStatus', 'Remind')" class="flex-1">
                
                @livewire('cx-tickets.counts.remind')
                </div>
            </div>
            <livewire:cx-tickets-table />
        </div>
    </div>
    
@push('modals')
    @livewire('cx-tickets.create-cx-ticket')
    @livewire('cx-tickets.edit')
@endpush