<x-modal.card title="Update User" blur align="center" wire:model="updateUserModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="Full Name" placeholder="Enter your full name" wire:model="user.name" />

        <x-native-select label="User Type" placeholder="Select user type" :options="$userTypes" wire:model="user.user_type_id"
            option-label="title" option-value="id" />
        <x-input label="Email" placeholder="example@mail.com" wire:model="user.email" />
        <x-input label="User Name" placeholder="Enter Unqie Username" wire:model.defer="user.user_name" />
        <x-input label="Phone" placeholder="+94 11 2223344" wire:model.defer="user.phone" />
        <x-input label="NIC" placeholder="892212312V" wire:model.defer="user.nic" />
        <x-native-select label="Gender" placeholder="Select gender" :options="[['id' => 'male', 'title' => 'Male'], ['id' => 'female', 'title' => 'Female']]" wire:model="user.gender"
            option-label="title" option-value="id" />

        <x-textarea label="Address" placeholder="Enter address" rows=1 wire:model.defer="user.address" />

        @if (($user && $user->agent) || ($user && $user->user_type_id > 2))
            <x-native-select label="Extension" placeholder="Select extension" :options="$extensions"
                wire:model="user.extension" option-label="extension" option-value="extension" />
        @endif


    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
            <div>

            </div>

            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
