<x-modal.card title="Create Feed" blur align="center" wire:model="createFeedModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Feed Name -->
        <x-input 
            label="Feed Name" 
            placeholder="Enter feed name" 
            wire:model.defer="feed.name"
        />

        <!-- Description -->
        <x-input 
            label="Description" 
            placeholder="Enter description" 
            wire:model.defer="feed.description"
        />

        <!-- File Upload -->
        {{-- <x-input 
            type="file" 
            label="Upload File" 
            wire:model="file"
            accept=".xlsx,.csv"
        /> --}}

    </div>
 
    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div></div>
 
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
