<x-modal.card title="Add MOH Class" blur align="center" wire:model="createMhoClassModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="MOH Name" placeholder="Enter your full name" wire:model.defer="mohClass.moh_class"/>
        
        

       
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