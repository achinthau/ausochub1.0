<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{-- {{ __('Chatter ')  }} --}}
            </h2>
            <div class="flex space-x-2">
                
               
            </div>
        </div>
    </x-slot>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4 w-full">
        <div class="flex flex-col lg:flex-row gap-4 w-full min-h-[78vh]">
            <div class="w-full lg:w-80 xl:w-96 shrink-0">
            @livewire('chat.users-panel')
            </div>

            <div class="hidden lg:block border-l border-gray-300"></div>

            <div class="flex-1 min-w-0">
            @livewire('chat.messages-panel')
            </div>
        </div>
    </div>
    



</div>
