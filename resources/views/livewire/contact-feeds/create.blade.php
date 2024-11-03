<div>
    <x-modal.card title="Upload Contact Feed" blur align="center" wire:model="createContactFeedModal">
        <div class="grid grid-cols-1 gap-4">
            <x-input wire:model.defer="contactFeed.title" label="Contact Feed Name" placeholder="Your contact feed name" />
            <x-textarea wire:model.defer="contactFeed.description" label="Description" placeholder="write your description" />
            <x-input wire:model="file" label="File" type="file" placeholder="xlsx,xls,csv" />

            {{-- <div
                class=" cursor-pointer bg-gray-100 rounded-xl shadow-md h-72 flex items-center justify-center">
                <div class="flex flex-col items-center justify-center">
                    <input type="file" class="hidden">
                    <x-icon name="cloud-upload" class="w-16 h-16 text-blue-600" />
                    <p class="text-blue-600">Click or drop files here</p>
                </div>
            </div> --}}
        </div>

        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <div>
                    <x-button flat  label="Download Template"  wire:click="delete" />
                </div>
                <div class="flex">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button primary label="Save" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
