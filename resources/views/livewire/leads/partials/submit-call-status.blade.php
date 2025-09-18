<x-modal.card
    title="Submit Call Status"
    blur
    align="center"
    wire:model="CallStatusModal"
>
    <div class="p-4 sm:p-6 bg-white rounded-lg shadow-inner">

        <div class="space-y-6">
            <h3 class="text-xl font-bold text-gray-800 text-center">
                Submit a status for the call
                <span class="{{ $status == 'answered' ? 'text-green-600' : 'text-orange-500' }} ml-1">
                    {{ $status && $status == 'answered' ? 'answered' : 'not answered' }}
                </span>
            </h3>

            {{-- Dropdown --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                <select wire:model="selectedOption" class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- Choose an option --</option>
                    @foreach($options as $id => $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selectedOption') 
                    <span class="mt-1 text-red-500 text-sm font-medium">{{ $message }}</span> 
                @enderror
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                <textarea wire:model="comment" rows="3" class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                @error('comment') 
                    <span class="mt-1 text-red-500 text-sm font-medium">{{ $message }}</span> 
                @enderror
            </div>
        </div>
    </div>

    {{-- Footer buttons --}}
    <x-slot name="footer">
        <div class="flex justify-end gap-x-4 pt-4">
            <x-button flat label="Cancel" x-on:click="close" class="hover:bg-gray-200 transition duration-150" />

            <x-button primary label="Submit" wire:click="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold transition duration-150" />
        </div>
    </x-slot>
</x-modal.card>
