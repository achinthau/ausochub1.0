<x-modal.card
    :title="$isReOpen == 'reopen' ? 'Service Ticket ReOpen' :
            ($isReOpen == 'skip' ? 'Service Ticket Skip' : 'Add Callback Reminder')"
    blur
    align="center"
    wire:model="cxTicketReOpenModal"
>
@if(!$callBack)
<div class="px-6">
    {{-- Comment Field --}}
    @if($isReOpen == 'reopen')
        <label class="pb-4">Add comment for reopening</label>
    @elseif($isReOpen == 'skip')
        <label class="pb-4">Add comment for skipping</label>
    @elseif($isReOpen == 'remind')
        <label class="pb-4">Add comment for callback</label>
    @endif

    <textarea cols="60" rows="5" wire:model.defer="comment" class="pt-2 w-full border rounded"></textarea>
    @error('comment')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
@endif

{{-- Callback form --}}
@if($callBack)
<div class="space-y-4 px-6 mt-4">
    <div class="flex justify-between items-end gap-4">

        <div class="w-1/2">
            <label class="text-sm font-medium">Select Date</label>
            <input type="date" wire:model="callbackDate" class="mt-1 block w-full border rounded p-2">
            @error('callbackDate')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-1/2">
            <label class="text-sm font-medium">Select Time</label>
            <input type="time" wire:model="callbackTime" class="mt-1 block w-full border rounded p-2">
            @error('callbackTime')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="text-sm font-medium">Comment</label>
        <textarea wire:model="callbackComment" class="mt-1 block w-full border rounded p-2" rows="3"></textarea>
        @error('callbackComment')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
@endif


{{-- Footer buttons --}}
<x-slot name="footer">
    <div class="flex justify-end gap-x-4">
        <x-button flat label="Cancel" x-on:click="close" />

        @if($isReOpen == 'remind')
            <x-button primary label="Save Callback" wire:click="saveCallback" />
        @elseif($isReOpen == 'reopen')
            <x-button primary label="ReOpen" wire:click="reOpenTicket" />
        @elseif($isReOpen == 'skip')
            <x-button primary label="Skip" wire:click="reOpenTicket" />
        @endif
    </div>
</x-slot>

</x-modal.card>
