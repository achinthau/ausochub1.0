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
            @can('is-admin')
            <div>
    

    @if ($showDropdown)
        <div class="flex mt-2">
            <select wire:model="selectedUser" class="border px-2 py-1 rounded">
                <option value="">Select User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <button wire:click="assign" class="ml-2 bg-green-500 text-white px-2 py-1 rounded">
                Save
            </button>
        </div>
        @else
        <button wire:click="toggleDropdown" class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">
        Assign
    </button>
    @endif

    {{-- @if (session()->has('success'))
        <div class="text-green-600 mt-2">{{ session('success') }}</div>
    @endif --}}
</div>

            @endcan

        </div>

        

    @endif
</div>