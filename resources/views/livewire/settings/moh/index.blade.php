<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Music On Hold') }}
            </h2>
           <div class="flex space-x-1">
            {{-- <x-button icon="device-tablet" label="Assgin Extension" onclick="$openModal('assignUserExtensionModal') " /> --}}
            <x-button icon="view-grid-add" label="Create" onclick="$openModal('createMhoClassModal') " />
           </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.settings.moh-class-table')
        </div>
    </div>
</div>

@push('modals')
    @livewire('settings.moh.create')
    @livewire('settings.moh.partials.upload-file')
    {{-- @livewire('settings.users.show') --}}
    {{-- @livewire('settings.users.partials.assign-extension') --}}
@endpush