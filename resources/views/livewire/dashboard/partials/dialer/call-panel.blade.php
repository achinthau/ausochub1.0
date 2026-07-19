<div wire:poll.1s="loadContact"
    @if($phone || $phone2) 
    wire:click="openProfile('{{ $phone }}','{{ $phone2 }}')" 
    class="bg-white p-4 space-y-2 cursor-pointer" 
    @else class="bg-white p-4 space-y-2" 
    @endif
>
    <h1 class="text-xs text-gray-400 font-semibold">Customer Number</h1>
    <hr>

    {{-- @foreach ($queueWiseData as $data) --}}
    <div class="flex">
        <div class="flex-1  text-xl font-bold text-secondary-700 dark:text-gray-400 mr-2">
            @if($displayNumber == true)
            @if($phone || $phone2)
                <!-- {{ $phone }} -->
                  {{ !empty($phone) ? $phone : $phone2 }}

            @else
                <span class="text-red-500 text-sm">{{ $reason }}</span>
            @endif
             @else 
            <span class="text-red-500 text-sm">Please Login to a campaign</span>
            @endif 
        </div>
        <div>
            
            <div class="relative group">
        <!-- The user's provided SVG with its original attributes -->
        {{-- <svg wire:click="openProfile('{{ $phone }}')" class="w-8 h-8 cursor-pointer text-green-400"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"></path>
            <path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"></path>
            <circle cx="12" cy="12" r="1"></circle>
            <path d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0">
            </path>
        </svg> --}}

        {{-- <svg class="w-8 h-8 cursor-pointer text-green-400" wire:click="openProfile('{{ $phone }}')" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2024 Fonticons, Inc. --><path d="M384 480l48 0c11.4 0 21.9-6 27.6-15.9l112-192c5.8-9.9 5.8-22.1 .1-32.1S555.5 224 544 224l-400 0c-11.4 0-21.9 6-27.6 15.9L48 357.1 48 96c0-8.8 7.2-16 16-16l117.5 0c4.2 0 8.3 1.7 11.3 4.7l26.5 26.5c21 21 49.5 32.8 79.2 32.8L416 144c8.8 0 16 7.2 16 16l0 32 48 0 0-32c0-35.3-28.7-64-64-64L298.5 96c-17 0-33.3-6.7-45.3-18.7L226.7 50.7c-12-12-28.3-18.7-45.3-18.7L64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l23.7 0L384 480z"></path></svg> --}}

        <!-- The text that appears on hover (the tooltip) -->
        <!-- This element is initially hidden and becomes visible on hover of the parent "group" -->
        <span class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2
                     bg-gray-800 text-white text-xs rounded-lg px-2 py-1
                     opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            Open Profile
        </span>
    </div>
        </div>
    </div>
    {{-- @endforeach --}}
</div>
</div>