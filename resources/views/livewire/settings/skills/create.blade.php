<x-modal.card title="Create Skill" blur align="center" wire:model="createSkillModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input label="Skill Name" placeholder="Enter your skill name" wire:model.defer="skill.skillname"/>
        
        <x-native-select
            label="MOH Class"
            placeholder="Select class"
            :options="$mohClasses"
            wire:model="skill.mohClass"
            option-label="moh_class"
            option-value="moh_class"
        />

       
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