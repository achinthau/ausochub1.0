<x-modal.card
    title="Change Language"
    blur
    align="center"
    wire:model="ChangeLangModal"
>
<div class="px-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Select Language</label>

    <select wire:model="lang"
        class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
        <option value="">-- Select a language --</option>
        @foreach($langOptions as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>

    @error('lang')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<x-slot name="footer">
    <div class="flex justify-end gap-x-4">
        <x-button flat label="Cancel" x-on:click="close" />
        <x-button primary label="Submit" wire:click="updateLang" />
    </div>
</x-slot>

</x-modal.card>

<script>
    window.addEventListener('reload-page', () => {
        window.location.reload();
    });
</script>