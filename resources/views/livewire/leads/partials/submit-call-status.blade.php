<x-modal.card title="Update Call Status" blur align="center" wire:model="CallStatusModal">
    <div class="p-4 sm:p-6 bg-white rounded-lg shadow-inner">

        <div class="space-y-6">
            <h3 class="text-xl font-bold text-gray-800 text-center">
                Update status for the call
                <span class="{{ $status == 'answered' ? 'text-green-600' : 'text-orange-500' }} ml-1">
                    {{-- {{ $status && $status == 'answered' ? 'answered' : 'not answered' }} --}}
                </span>
            </h3>

            {{-- Dropdown --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                {{-- <select wire:model="selectedOption"
                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- Choose an option --</option>
                    @foreach($options as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select> --}}
                <select wire:model="selectedOption"
                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- Choose an option --</option>
                    @foreach($options as $option)
                                        <option value="{{ json_encode([
                            'id' => $option->id,
                            'value' => $option->option,
                            'type' => $option->type
                        ]) }}">
                                            {{ $option->option }}
                                        </option>
                    @endforeach
                </select>

                @error('selectedOption')
                    <span class="mt-1 text-red-500 text-sm font-medium">{{ $message }}</span>
                @enderror
            </div>

            {{-- Comment --}}
            <div>
                @php
                    $selected = $selectedOption ? json_decode($selectedOption, true) : null;
                @endphp

                @if(optional($selected)['value'] === 'Promised to pay')
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Date</label>
                    <input type="date" wire:model="paymentDate"
                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @error('paymentDate')
                        <span class="mt-1 text-red-500 text-sm font-medium">{{ $message }}</span>
                    @enderror
                @endif

                <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                <textarea wire:model="comment" rows="3"
                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-800 transition duration-150 ease-in-out focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                @error('comment')
                    <span class="mt-1 text-red-500 text-sm font-medium">{{ $message }}</span>
                @enderror

            </div>
        </div>
        <div class="flex justify-end">
            @if($feedCount > 1)
                <div class="flex justify-end">
                    <div class="flex items-center space-x-2 mb-2 mt-4">
                        <input type="checkbox" wire:model="applyToAll" id="applyToAll" class="rounded text-blue-600">
                        <label for="applyToAll" class="text-gray-700">Apply to all items with this
                            number</label>
                    </div>

                </div>
            @endif
        </div>
    </div>

    {{-- Footer buttons --}}
    <x-slot name="footer">
        <div class="flex justify-end gap-x-4 pt-4">
            <x-button flat label="Cancel" x-on:click="close" class="hover:bg-gray-200 transition duration-150" />

            <x-button primary label="Submit" wire:click="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold transition duration-150" />
        </div>
    </x-slot>
</x-modal.card>