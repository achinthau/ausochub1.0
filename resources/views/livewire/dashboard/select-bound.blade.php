<div wire:poll="refreshComponent" class="flex items-center">

    <div class="flex">
    <label for="toggle" class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" id="toggle" wire:model="isOutbound" class="sr-only peer" />
        <div
            class="w-11 h-6 bg-gray-400 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:bg-blue-300">
        </div>
        <span
            class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5 peer-checked:left-auto"></span>
    </label>
        
        <span class="ml-3 text-gray-700 font-medium">
            {{$boundType}}
        </span>
    </div>

    <div class="pl-8 ">
        <button type="button"
    class="rounded gap-x-2 text-sm px-4 py-2 ring-secondary-500 text-white 
        {{ $isAcw ? 'bg-red-500 hover:bg-red-600 hover:ring-red-600' : 'bg-gray-500 hover:bg-gray-600 hover:ring-gray-600' }}" 
    wire:click="$emit('setAcw')">
    ACW
</button>

    </div>
    </div>

