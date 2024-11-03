<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('User Management') }}
            </h2>
           <div class="flex space-x-1">
            <x-button icon="device-tablet" label="Assgin Extension" onclick="$openModal('assignUserExtensionModal') " />
            <x-button icon="user-add" label="Add User" onclick="$openModal('createUserModal') " />
           </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.settings.user-table')
        </div>
    </div>
</div>

@push('modals')
    @livewire('settings.users.create')
    @livewire('settings.users.show')
    @livewire('settings.users.partials.assign-extension')
@endpush