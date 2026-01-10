<x-modal.card
    title="Close Reminder"
    blur
    align="center"
    wire:model="ReminderClosingModal"
>
<div>
    
    <div class="px-6">
    {{-- Comment Field --}}
    
        <label class="pb-4">Add comment for close reminder</label>
   

    <textarea cols="60" rows="5" wire:model.defer="comment" class="pt-2 w-full border rounded"></textarea>
    @error('comment')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Footer buttons --}}
<x-slot name="footer">
    <div class="flex justify-end gap-x-4">
        <x-button flat label="Cancel" x-on:click="close" />

        
            <x-button primary label="Close Reminder" wire:click="closeModal" />

    </div>
</x-slot>
</div>

</x-modal.card>