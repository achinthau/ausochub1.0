<x-modal.card title="Add User" blur wire:model="assignUserExtensionModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        
        <x-native-select
            label="User"
            placeholder="Select user"
            :options="$users"
            wire:model="user"
            option-label="name"
            option-value="agent_id"
        />
        <x-native-select
            label="Extension"
            placeholder="Select extension"
            :options="$extensions"
            wire:model="extension"
            option-label="extension"
            option-value="extension"
        />
        

    </div>
 
    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
            <div>

            </div>
 
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Assign" wire:click="assign" />
            </div>
        </div>
    </x-slot>
</x-modal.card>