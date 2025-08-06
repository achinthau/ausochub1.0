<div>
    {{-- @if (session()->has('success'))
    <div class="text-green-600 mb-2">{{ session('success') }}</div>
    @endif --}}
    @if ($isEdit)

        <input type="datetime-local" wire:model="callback_at" class="border rounded px-2 py-1">

        <button wire:click="updateReminder" class="bg-blue-500 text-white px-3 py-1 rounded">
            Update Reminder
        </button>
    @else
        <div class="flex items-center space-x-2">
            <button wire:click="setEdit" class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">
                Update Reminder
            </button>

            <button  class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">complete</button>
            <button  class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">close</button>

        </div>

        

    @endif
</div>