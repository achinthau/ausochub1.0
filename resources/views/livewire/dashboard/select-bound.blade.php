<div wire:poll="refreshComponent" class="flex items-center">

    <label for="toggle" class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" id="toggle" wire:model="isOutbound" class="sr-only peer" />
        <div
            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:bg-blue-300">
        </div>
        <span
            class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5 peer-checked:left-auto"></span>
    </label>
        
        <span class="ml-3 text-gray-700 font-medium">
            {{$boundType}}
        </span>
    </div>

