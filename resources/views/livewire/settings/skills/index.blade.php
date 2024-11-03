<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Skills Management') }}
            </h2>
           <div class="flex space-x-1">
            <x-button icon="user-add" label="Assgin User" onclick="$openModal('assignUserSkillModal') " />
            <x-button icon="plus" label="Create" onclick="$openModal('createSkillModal') " />
           </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.settings.skill-table')
        </div>
    </div>
</div>

@push('modals')
    @livewire('settings.skills.create')
    @livewire('settings.skills.partials.assign-user')
    {{-- @livewire('settings.users.show') --}}
    {{-- @livewire('settings.users.partials.assign-extension') --}}
@endpush