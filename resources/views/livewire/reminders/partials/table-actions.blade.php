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
            {{-- <button class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">
                Update Reminder
            </button> --}}
                    <svg class="w-8 h-8 cursor-pointer text-orange-500 hover:text-orange-600 "  wire:click="setEdit" wire:click="updateReminder"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><path d="M22 12C22 6.478 17.523 2 12 2C6.477 2 2 6.478 2 12C2 12.2628 2.01014 12.5232 2.03005 12.7809C2.51375 12.3226 3.06786 11.9379 3.67448 11.6446C3.86129 7.2138 7.52406 3.667 12 3.667C16.595 3.667 20.333 7.405 20.333 12C20.333 16.4759 16.7862 20.1387 12.3554 20.3255C12.0621 20.9321 11.6774 21.4863 11.2191 21.97C11.4768 21.9899 11.7372 22 12 22C17.523 22 22 17.522 22 12ZM6.5 23C9.53757 23 12 20.5376 12 17.5C12 14.4624 9.53757 12 6.5 12C3.46243 12 1 14.4624 1 17.5C1 20.5376 3.46243 23 6.5 23ZM5 15V20C5 20.2761 4.77614 20.5 4.5 20.5C4.22386 20.5 4 20.2761 4 20V15C4 14.7239 4.22386 14.5 4.5 14.5C4.77614 14.5 5 14.7239 5 15ZM9 15V20C9 20.2761 8.77614 20.5 8.5 20.5C8.22386 20.5 8 20.2761 8 20V15C8 14.7239 8.22386 14.5 8.5 14.5C8.77614 14.5 9 14.7239 9 15ZM11.75 6C12.1295 6 12.4435 6.28233 12.4931 6.64827L12.5 6.75V12H15.75C16.164 12 16.5 12.336 16.5 12.75C16.5 13.1295 16.2177 13.4435 15.8517 13.4931L15.75 13.5H11.75C11.3705 13.5 11.0565 13.2177 11.0069 12.8517L11 12.75V6.75C11 6.336 11.336 6 11.75 6Z" fill="currentColor"></path></svg>


            {{-- <button  class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">complete</button> --}}
            <svg class="w-8 h-8 cursor-pointer text-green-500 hover:text-green-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" viewBox="0 0 24 24"><path d="M20 10.999h2C22 5.869 18.127 2 12.99 2v2C17.052 4 20 6.943 20 10.999z"></path><path d="M13 8c2.103 0 3 .897 3 3h2c0-3.225-1.775-5-5-5v2zm3.422 5.443a1.001 1.001 0 0 0-1.391.043l-2.393 2.461c-.576-.11-1.734-.471-2.926-1.66-1.192-1.193-1.553-2.354-1.66-2.926l2.459-2.394a1 1 0 0 0 .043-1.391L6.859 3.513a1 1 0 0 0-1.391-.087l-2.17 1.861a1 1 0 0 0-.29.649c-.015.25-.301 6.172 4.291 10.766C11.305 20.707 16.323 21 17.705 21c.202 0 .326-.006.359-.008a.992.992 0 0 0 .648-.291l1.86-2.171a1 1 0 0 0-.086-1.391l-4.064-3.696z"></path></svg>

            {{-- <button  class="bg-blue-500 text-white px-3 py-1 rounded whitespace-nowrap">close</button> --}}
            <svg class="w-8 h-8 cursor-pointer text-red-500 hover:text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM11.4 10l2.83-2.83-1.41-1.41L10 8.59 7.17 5.76 5.76 7.17 8.59 10l-2.83 2.83 1.41 1.41L10 11.41l2.83 2.83 1.41-1.41L11.41 10z"></path></svg>
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