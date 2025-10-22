<x-modal.card title="Create Feed" blur align="center" wire:model="FeedUploadModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Feed Name -->
        <x-input 
            label="Feed Name" 
            placeholder="Enter feed name" 
            wire:model.defer="name"
        />

        <!-- Description -->
        <x-input 
            label="Description" 
            placeholder="Enter description" 
            wire:model.defer="description"
        />

        <!-- File Upload -->
        <x-input 
            type="file" 
            label="Upload File" 
            wire:model="file"
            accept=".xlsx,.csv"
        />

         <!-- Type Dropdown -->
        <x-select
            label="Type"
            placeholder="Select Type"
            wire:model.defer="feed_type"
            :options="[
                ['name' => 'Satisfaction', 'id' => 'satisfaction'],
                ['name' => 'Confirmation', 'id' => 'confirmation'],
                ['name' => 'Follow up', 'id' => 'follow-up'],
            ]"
            
    option-value="id"
    option-label="name"
        />

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
