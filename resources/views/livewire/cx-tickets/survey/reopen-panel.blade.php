<x-modal.card :title="$isReOpen ? 'Service Ticket ReOpen' : 'Service Ticket Skip'" blur align="center" wire:model="cxTicketReOpenModal">

    <div>

        <div class="px-6">
            @if($isReOpen)
            <label class="pb-4" for="">Add comment for reopenning</label>
            @elseif (!$isReOpen)
            <label class="pb-4" for="">Add comment for skipping</label>
            @endif
            <textarea class="pt-2" name="" id="" cols="60" rows="5" wire:model.defer="comment"></textarea>
            @error('comment')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
        </div>
        



        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                {{-- <x-button flat negative label="Delete" wire:click="delete" /> --}}
                <div>

                </div>

                <div class="flex">
                    <x-button flat label="Cancel" x-on:click="close" />

                    @if($isReOpen)
            <x-button primary label="ReOpen" wire:click="reOpenTicket" />
            @elseif (!$isReOpen)
            <x-button primary label="Skip" wire:click="reOpenTicket" />
            @endif
                    
                    
                    
                </div>
            </div>
        </x-slot>
    </div>
</x-modal.card>
