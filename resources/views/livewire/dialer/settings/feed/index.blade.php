<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Base Management') }}
            </h2>
           <div class="flex space-x-1">
            {{-- <x-button icon="device-tablet" label="Assgin Extension" onclick="$openModal('assignUserExtensionModal') " /> --}}
            {{-- <x-button icon="user-add" label="Add User" onclick="$openModal('createUserModal') " /> --}}
            <x-button icon="plus" label="Add" onclick="$openModal('createFeedModal')" />
           </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('feed-table')
        </div>
    </div>
</div>

@push('modals')
    @livewire('dialer.settings.feed.create')
    @livewire('dialer.settings.feed.file-upload')
@endpush