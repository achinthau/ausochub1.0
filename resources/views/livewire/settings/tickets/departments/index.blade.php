<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Service Tickets Department Management') }}
            </h2>
            <x-button icon="user-add" label="Create Department" onclick="$openModal('createTicketDepartmentModal') " />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.settings.crm-department-table')
            {{-- @livewire('crm-departments-table') --}}
        </div>
    </div>
</div>

@push('modals')
    @livewire('settings.tickets.departments.create')
    @livewire('settings.tickets.departments.show')
@endpush
