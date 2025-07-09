<x-modal.card title="Create Service Center" blur wire:model="updateTicketServiceCenterModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <x-input label="Service Center" placeholder="Enter Service Center" wire:model="servicecenter.name"/>
    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div>

            </div>

            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
