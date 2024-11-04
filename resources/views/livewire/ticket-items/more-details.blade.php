<div class="justify-center p-12">
    <div class="p-6 bg-white rounded-lg shadow-md space-y-6 text-left"> 
        <h2 class="text-lg font-semibold pl-0">{{$ticketItem->topic}}</h2>
    
        <div class="grid grid-cols-2 gap-6 text-left"> 
            <div class="justify-between text-left"> 
                <p class="pl-2"><strong>Client Name :</strong> Name</p>
                <p><strong>Category :</strong> Cat</p>
                <p><strong>Report at :</strong> n minutes ago</p>
                <p><strong>Description :</strong>{{$ticketItem->description}}</p>
            </div>
            <div class="text-left"> 
                <p><strong>Contact Number :</strong> 119544444444444444</p>
                <p><strong>Sub Category :</strong> sub</p>
                <p><strong>Priority :</strong></p>
                <p><strong>Tags :</strong> tags</p>
            </div>
        </div>
    
        <div class="mt-4 text-left"> 
            <textarea wire:model='comment'
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                rows="3" placeholder="Enter your comment here"></textarea>
                @error('name') <span class="error">{{ $message }}</span> @enderror
            <button wire:click="$emit('commented')" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Comment</button>
        </div>
    
        <div class="space-y-4 mt-6 text-left"> 

            @foreach ($commentedAgents as $commentedAgent )            
            <div class="text-gray-500 text-sm">
                <span class="font-semibold"> Commented on ticket by {{$commentedAgent->user->name}}</span><br>
                {{$commentedAgent->comment}}
            </div>
            @endforeach

            <div class="text-gray-500 text-sm">
                @foreach ($createdAgent as $createdAgent)
                <span class="font-semibold">Ticket created by </span> {{ $createdAgent->user->name}}
                @endforeach
            </div>
            
        </div>
    
        <div class="flex justify-between mt-8 text-left"> 
            <button class="text-red-500 font-semibold hover:text-red-600">Complete</button>
            <div>
                <button type="button" wire:click="changeStatus({{ $ticketItem->id }})" class="text-green-600 hover:text-green-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-semibold rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Start</button>
                <button wire:click="$emit('closeModal')" class="text-gray-500 font-semibold hover:text-gray-600">Exit</button>
            </div>
        </div>
    </div>

    
    <style>
        textarea::placeholder {
            color: #9CA3AF; /* Gray placeholder */
        }
    </style>
</div>
