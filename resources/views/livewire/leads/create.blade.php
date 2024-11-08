<div>
    <x-modal.card blur wire:model.defer="showTicketEditModal" title="Complete Lead" squared="true">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input label="Contact Number" wire:model="lead.contact_number" disabled />
            <x-input label="Queue" value="{{ $lead->skill ? $lead->skill->skillname : '0'}}" disabled />
    
            <x-input label="First Name*" wire:model.defer='lead.first_name' placeholder="First Name" />
            <x-input label="Last Name" wire:model.defer='lead.last_name' placeholder="Last Name" />
            <x-input label="Email" wire:model.defer='lead.email' placeholder="Email" />
            <x-input label="Alternative Contact" wire:model.defer='lead.contact_number_2'
                placeholder="Alternative Contact" />
            <x-input label="NIC" wire:model.defer='lead.nic' placeholder="NIC Name" />
            <hr class="col-span-2">
            <x-input label="House or Apartment No" wire:model.defer='lead.address_line_1'
                placeholder="House or Apartment No" />
            <x-input label="Street" wire:model.defer='lead.address_line_2' placeholder="Street" />
            <x-input label="City" wire:model.defer='lead.city' placeholder="City" />
            <div class="col-span-2">
                <x-button icon="check-circle" positive label="Complete Lead" class="w-full" wire:click="save"
                    spinner="save" />
            </div>
        </div>
    </x-modal>
</div>
