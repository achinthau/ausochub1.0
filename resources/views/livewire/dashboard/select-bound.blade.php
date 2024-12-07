<div wire:poll="refreshComponent" class="flex items-center">

    <div class="flex pr-8">
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

    <div class="relative">
        @if($isAcw)
        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 font-bold text-gray-700 text-lg">
            {{ gmdate('H:i:s', $time) }}
        </div>
        @endif
        <button type="button"
            class="flex items-center justify-center w-24 h-10 rounded gap-x-2 text-sm px-4 py-2 ring-secondary-500 text-white 
            {{ $pauseTime ? 'bg-red-500 hover:bg-red-600 hover:ring-red-600' : ($isAcw ? 'bg-orange-500 hover:bg-orange-400 hover:ring-orange-500' : 'bg-gray-500 hover:bg-gray-600 hover:ring-gray-600') }}" 
            wire:click="$emit('setAcw')">
            @if($pauseTime) <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814z"></path>
              </svg>
            @elseif($isAcw) <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="currentColor"><rect fill="none" height="24" width="24"></rect><path d="M12,2C6.48,2,2,6.48,2,12c0,5.52,4.48,10,10,10s10-4.48,10-10C22,6.48,17.52,2,12,2z M16,16H8V8h8V16z"></path></svg> 
            @else
            ACW
            @endif
        </button>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            setInterval(() => {
                Livewire.emit('updateTime');
            }, 1000);
        });
    </script>
    
    </div>

