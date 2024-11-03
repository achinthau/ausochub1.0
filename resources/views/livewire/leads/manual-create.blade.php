<x-modal.card title="Add Lead" blur wire:model.defer="showCreateLeadModal">
    <div class="bg-white p-4 gap-4 grid grid-cols-2">
        <div class="">
            <div class="flex justify-between mb-1">
                <label class="block text-sm font-medium text-secondary-700 dark:text-gray-400">
                    Contact Number
                </label>

            </div>

            <div class="relative rounded-md  shadow-sm ">
                <x-input  wire:model.defer='lead.contact_number' placeholder="Contact Number 771234567" />


            </div>


        </div>
        <div class="">
            <div class="flex justify-between mb-1">
                <label class="block text-sm font-medium text-secondary-700 dark:text-gray-400">
                    Queue
                </label>

            </div>

            <div class="relative rounded-md  shadow-sm ">

               
                <x-select  placeholder="Select Skill" :options="$skills" option-label="skillname"
                    option-value="skillid" wire:model.defer="lead.skill_id" />

            </div>


        </div>
        <x-input label="First Name" wire:model.defer='lead.first_name' placeholder="First Name" />
        <x-input label="Last Name" wire:model.defer='lead.last_name' placeholder="Last Name" />
        <x-input label="NIC" wire:model.defer='lead.nic' placeholder="NIC Name" />
        <x-input label="Email" wire:model.defer='lead.email' placeholder="Email" />
        <x-input label="Alternative Contact" wire:model.defer='lead.contact_number_2'
            placeholder="Alternative Contact" />
        <div></div>
        <div class="col-span-2">
            <div class="grid grid-cols-3 gap-x-2">
                <x-input label="House or Apartment No" wire:model.defer='lead.address_line_1'
                    placeholder="House or Apartment No" />
                <x-input label="Street" wire:model.defer='lead.address_line_2' placeholder="Street" />
                <x-input label="City" wire:model.defer='lead.city' placeholder="City" />
            </div>
        </div>

    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <x-button flat negative label="Delete" wire:click="delete" />

            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Save" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
