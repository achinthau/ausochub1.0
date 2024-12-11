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

    <div class="flex ">
        @if($isAcw)
        <div class=" font-bold text-gray-700 text-2xl pt-1 pl-4 pr-1">
            {{ gmdate('H:i:s', $time) }}
        </div>
        @endif
        {{-- <button type="button"
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
        </button> --}}

        

        


        @if($pauseTime) <svg  wire:click="$emit('setAcw')" class="w-10 h-10 text-red-500 hover:text-red-600 hover:ring-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" fill="currentColor"><path d="M4 0h12a4 4 0 0 1 4 4v12a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4zm10.126 11.746c.213-.153.397-.348.54-.575.606-.965.365-2.27-.54-2.917L10.07 5.356A1.887 1.887 0 0 0 8.972 5C7.883 5 7 5.941 7 7.102v5.796c0 .417.116.824.334 1.17.607.965 1.832 1.222 2.737.576l4.055-2.898zm-5.2-4.616 4.055 2.898-4.056 2.897V7.13z"></path></svg>

        @elseif($isAcw) <svg  wire:click="$emit('setAcw')" class="w-10 h-10 text-orange-500 hover:text-orange-400 hover:ring-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.7778 2H4.22222C3 2 2 3 2 4.22222V19.7778C2 21 3 22 4.22222 22H19.7778C21 22 22 21 22 19.7778V4.22222C22 3 21 2 19.7778 2ZM16 8H8V16H16V8ZM14.6667 9.33333V14.6667H9.33333V9.33333H14.6667Z" fill="currentColor"></path></svg>

        @else
        <svg wire:click="$emit('setAcw')" class="w-10 h-10 {{ $pauseTime ? 'text-red-500 hover:text-red-600 hover:ring-red-600' : ($isAcw ? 'text-orange-500 hover:text-orange-400 hover:ring-orange-500' : 'text-gray-500 hover:text-gray-600 hover:ring-gray-600') }}" viewBox="0 0 24 24"><path fill="currentColor" d="M3.464 20.536C4.93 22 7.286 22 12 22c4.714 0 7.071 0 8.535-1.465C22 19.072 22 16.714 22 12s0-7.071-1.465-8.536C19.072 2 16.714 2 12 2S4.929 2 3.464 3.464C2 4.93 2 7.286 2 12c0 4.714 0 7.071 1.464 8.535" opacity=".5"></path><path fill="currentColor" fill-rule="evenodd" d="M12 7.25a.75.75 0 0 1 .75.75v3.69l2.28 2.28a.75.75 0 1 1-1.06 1.06l-2.5-2.5a.75.75 0 0 1-.22-.53V8a.75.75 0 0 1 .75-.75" clip-rule="evenodd"></path></svg>
        @endif


    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            setInterval(() => {
                Livewire.emit('updateTime');
            }, 1000);
        });
    </script>
    
    
    </div>

