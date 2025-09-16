<x-modal.card
    title="Call Status"
    blur
    align="center"
    wire:model="CallStatusModal"
>
<div>
    
    <div class="px-6">

        <div class="px-6 space-y-4">
    <label class="pb-4 text-lg font-medium">
        Submit status for the call {{ $feed?->id ?? '' }}
    </label>

    <div class="flex flex-wrap gap-3">
        <button 
            wire:click="setCallStatus('answered')" 
            class="px-4 py-2 rounded-xl bg-green-500 text-white hover:bg-green-600 transition">
            Answered
        </button>

        <button 
            wire:click="setCallStatus('callback')" 
            class="px-4 py-2 rounded-xl bg-blue-500 text-white hover:bg-blue-600 transition">
            Callback
        </button>

        <button 
            wire:click="setCallStatus('no_answer')" 
            class="px-4 py-2 rounded-xl bg-gray-500 text-white hover:bg-gray-600 transition">
            No Answer
        </button>

        <button 
            wire:click="setCallStatus('wrong_number')" 
            class="px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-600 transition">
            Wrong Number
        </button>

        <button 
            wire:click="setCallStatus('other')" 
            class="px-4 py-2 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 transition">
            Other
        </button>
    </div>
</div>

   

    
</div>

{{-- Footer buttons --}}
{{-- <x-slot name="footer">
    <div class="flex justify-end gap-x-4">
        <x-button flat label="Cancel" x-on:click="close" />

        
            <x-button primary label="Skip Contact" wire:click="skipContact" />

    </div>
</x-slot> --}}
</div>

</x-modal.card>