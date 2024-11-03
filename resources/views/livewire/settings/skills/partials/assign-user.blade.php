<x-modal.card title="Add Skill to User" blur wire:model="assignUserSkillModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        
            <x-native-select
            label="User"
            placeholder="Select user"
            :options="$users"
            wire:model="user"
            option-label="name"
            option-value="agent_id"
        />
        
    <x-select
        label="Skill"
        placeholder="Skills"
        multiselect
        :options="$skills"
        wire:model.defer="selectedSkills"
        option-label="skillname"
        option-value="skillid"
    />


    

        

    </div>
 
    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
            <div>

            </div>
 
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Assign" wire:click="assign" />
            </div>
        </div>
    </x-slot>
</x-modal.card>