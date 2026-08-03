<x-modal.card title="Max Dial Counts" blur align="center" wire:model="modalOpen">
    <div class="p-4">
        <p class="text-sm text-gray-500 mb-4">Set monthly max dial counts for agents in <strong>{{ $campaignName }}</strong></p>

        {{-- Add / Update Entry --}}
        @if(empty($agents))
            <div class="text-center text-gray-400 py-6 text-sm">No agents assigned to this campaign.</div>
        @else
            <div class="flex items-end gap-2 mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <x-select label="Agent" placeholder="Choose an agent" wire:model="selectedAgentId">
                        @foreach($agents as $agent)
                            <x-select.option :label="$agent['name']" :value="$agent['id']" />
                        @endforeach
                    </x-select>
                </div>
                <div class="w-40">
                    <x-input label="Max Count" placeholder="e.g. 100" type="number" min="0" wire:model.defer="maxCount" />
                </div>
                <div class="pb-0.5">
                    <x-button primary label="Save" wire:click="save" icon="plus" />
                </div>
            </div>
        @endif

        {{-- Entries List --}}
        @if(empty($entries))
            <div class="text-center text-gray-400 py-6 text-sm">No max dial counts configured yet.</div>
        @else
            <div class="divide-y rounded-lg border">
                @foreach($entries as $entry)
                    <div class="flex items-center gap-2 px-3 py-2">
                        <div class="flex-1">
                            <span class="text-sm font-medium">{{ $entry->agent?->name ?? 'Unknown Agent' }}</span>
                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                Max: {{ $entry->max_count ?? 'Unlimited' }}
                            </span>
                            <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                Current: {{ $entry->current_count }}
                            </span>
                            <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                Last Month: {{ $entry->last_month_count }}
                            </span>
                        </div>
                        <x-button icon="trash" wire:click="deleteEntry({{ $entry->id }})" class="text-xs p-1 text-red-500" />
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
