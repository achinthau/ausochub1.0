<x-modal.card title="Add MOH Class" blur align="center" wire:model="uploadFileModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="Name" type="file" placeholder="your name" wire:model="mohFile" accept="audio/wav" />

        
        

       
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