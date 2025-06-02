<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ticket Dashboard') }}
            </h2>

        </div>
    </x-slot>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-end space-x-2 pt-6">
            @if ($userid == 1)
                @livewire('ticket-items.department-panel')
            @endif
            @if ($userid == 9)
                @livewire('ticket-items.user-menu-panel')
            @endif
        </div>

        <div class="">

            <div class="flex justify-between pb-2 gap-4">
                <div class="flex-1">
                    @livewire('ticket-items.counts.new-count')
                </div>
                <div class="flex-1">
                    @livewire('ticket-items.counts.open-count')
                </div>
                <div class="flex-1">
                    @livewire('ticket-items.counts.overdue-count')
                </div>
                <div class="flex-1">
                    @livewire('ticket-items.counts.closed-count')
                </div>
                <div class="flex-1">
                    @livewire('ticket-items.counts.canceled-count')
                </div>
            </div>


            <div class="bg-white  shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- @livewire('ticket-items.count-panel') --}}


                    @livewire('new-ticket-table')
                    {{-- @livewire('ticket-table') --}}


                </div>
            </div>
        </div>
    </div>
</div>
@push('modals')
    @livewire('tickets.create')
    @livewire('orders.create')
@endpush
