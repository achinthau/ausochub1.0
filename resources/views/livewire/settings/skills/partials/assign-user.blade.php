<x-modal.card title="Add Skill to User" blur wire:model="assignUserSkillModal">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        {{-- User Select --}}
        <x-native-select
            label="User"
            placeholder="Select user"
            :options="$users"
            wire:model="user"
            option-label="name"
            option-value="agent_id"
        />

        {{-- Type Select --}}
        <x-native-select
            label="Type"
            placeholder="Select type"
            wire:model="type"
        >
            <option value="">-- Select Type --</option>
            <option value="inbound">Inbound</option>
            <option value="dialer">Dialer</option>
        </x-native-select>

        {{-- Skills (only visible when type is chosen) --}}
        @if ($type)
            <x-select
                label="Skill"
                placeholder="Skills"
                multiselect
                :options="$skills"
                wire:model.defer="selectedSkills"
                option-label="skillname"
                option-value="skillid"
            />
        @endif
    </div>
 
    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div></div>
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary label="Assign" wire:click="assign" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
