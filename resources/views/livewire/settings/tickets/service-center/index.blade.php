<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Tickets Service Center Management') }}
            </h2>
            <x-button icon="user-add" label="Create Service Center " onclick="$openModal('createTicketServiceCenterModal') " />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.settings.service-center-table')
        </div>
    </div>
</div>

@push('modals')
    @livewire('settings.tickets.service-center.create')
    @livewire('settings.tickets.service-center.show')
@endpush
