<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Chatter ')  }}
            </h2>
            <div class="flex space-x-2">
                
               
            </div>
        </div>
    </x-slot>
    
    <div class="flex justify-between pt-4 w-full">
        
        <div class="w-1/4 ">
            @livewire('chat.users-panel')
        </div>

        <div class="w-4/5">
            @livewire('chat.messages-panel')
        </div>
    </div>
    



</div>
