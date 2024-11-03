<x-modal.card title="Create Extension" blur wire:model="createExtensionModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input type="number" label="Extension" placeholder="3-4 Digit Number" wire:model="extension.extension" />
        <x-input label="Password" placeholder="Enter password " wire:model="extension.password" />
        <x-native-select label="Extension Type" placeholder="Select extension type" :options="['sip', 'iax']" wire:model="extension.exten_type" />
        <x-native-select label="Context" placeholder="Select context" :options="['intental', 'external']" wire:model="extension.context" />
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
