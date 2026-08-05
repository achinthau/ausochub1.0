<x-modal.card title="Status Options" blur align="center" wire:model="modalOpen">
    @php
        $isSatisfaction = $campaignType === 'satisfaction';
    @endphp
    <div class="p-4">
        <p class="text-sm text-gray-500 mb-4">Manage call status options for <strong>{{ $campaignName }}</strong></p>

        {{-- Add New Option --}}
        <div class="flex items-end gap-2 mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
                <x-input label="New Status Option" placeholder="Type a status option..." wire:model="newOption" />
            </div>
            <div class="w-40">
                <x-select label="Type" wire:model="newType">
                    <x-select.option label="{{ $isSatisfaction ? 'Satisfied' : 'Answered' }}" :value="1" />
                    <x-select.option label="{{ $isSatisfaction ? 'Dissatisfied' : 'Not Answered' }}" :value="2" />
                    <x-select.option label="{{ $isSatisfaction ? 'Cancel' : 'Skip' }}" :value="3" />
                    @if($isSatisfaction)
                        <x-select.option label="Not Answered" :value="4" />
                    @endif
                </x-select>
            </div>
            <div class="pb-0.5">
                <x-button primary label="Add" wire:click="addOption" icon="plus" />
            </div>
        </div>

        {{-- Options List --}}
        @if(empty($options))
            <div class="text-center text-gray-400 py-6 text-sm">No status options added yet.</div>
        @else
            <div class="divide-y rounded-lg border">
                @foreach($options as $option)
                    <div class="flex items-center gap-2 px-3 py-2">
                        @if($editingId === $option->id)
                            {{-- Editing Mode --}}
                            <div class="flex-1 flex items-end gap-2">
                                <div class="flex-1">
                                    <x-input placeholder="Status option" wire:model="editOption" />
                                </div>
                                <div class="w-32">
                                    <x-select wire:model="editType">
                                        <x-select.option label="{{ $isSatisfaction ? 'Satisfied' : 'Answered' }}" :value="1" />
                                        <x-select.option label="{{ $isSatisfaction ? 'Dissatisfied' : 'Not Answered' }}" :value="2" />
                                        <x-select.option label="{{ $isSatisfaction ? 'Cancel' : 'Skip' }}" :value="3" />
                                        @if($isSatisfaction)
                                            <x-select.option label="Not Answered" :value="4" />
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <x-button positive label="Save" wire:click="saveEdit" icon="check" class="text-xs" />
                            <x-button flat label="Cancel" wire:click="cancelEdit" class="text-xs" />
                        @else
                            {{-- Display Mode --}}
                            <div class="flex-1">
                                <span class="text-sm font-medium">{{ $option->option }}</span>
                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full
                                    @if($option->type == 1) bg-green-100 text-green-700
                                    @elseif($option->type == 2) bg-orange-100 text-orange-700
                                    @elseif($option->type == 4) bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    @if($option->type == 1) {{ $isSatisfaction ? 'Satisfied' : 'Answered' }}
                                    @elseif($option->type == 2) {{ $isSatisfaction ? 'Dissatisfied' : 'Not Answered' }}
                                    @elseif($option->type == 4) Not Answered
                                    @else {{ $isSatisfaction ? 'Cancel' : 'Skip' }} @endif
                                </span>
                            </div>
                            <x-button icon="pencil" wire:click="startEdit({{ $option->id }})" class="text-xs p-1" />
                            <x-button icon="trash" wire:click="deleteOption({{ $option->id }})" class="text-xs p-1 text-red-500" />
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-end">
            <x-button flat label="Close" x-on:click="close" />
        </div>
    </x-slot>
</x-modal.card>
