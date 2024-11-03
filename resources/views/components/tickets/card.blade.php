<div
    class="bg-white rounded-sm p-2 space-y-2 border {{-- border-gray-200 --}}  {{-- hover:scale-105 --}} transition-all border-t-8   hover:{{ $ticket->border_color_hover }} {{ $ticket->border_color }}">
    <div class="flex items-center gap-2">

        <div class="flex-1 text-sm font-bold text-gray-500">
            Ticket# {{ $ticket->ticket_ref }}
        </div>
        <div
            class="text-2xs font-semibold text-gray-500 border px-1 py-0.5 hover:{{ $ticket->border_color_hover }} {{ $ticket->border_color }}">
            {{ $ticket->status->title }}
        </div>
    </div>
    
    <div class="text-sm font-semibold text-slate-500">
        {{ $ticket->topic }}
    </div>
    <div class="grid grid-cols-2 text-gray-500 h-4">

        @if($ticket->outlet)
        <div class="flex space-x-1">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">
                <path
                    d="M12 12H14V14H12V12ZM5.00001 2H15C15.1484 2 15.2891 2.06591 15.3841 2.17991L17.8841 5.17991C18.0009 5.32003 18.0005 5.39121 18.0001 5.48233L18 5.5V7C18 7.8885 17.6137 8.68679 17 9.23611V17.5C17 17.7761 16.7761 18 16.5 18H10V11.5C10 11.2239 9.77614 11 9.5 11H5.5C5.22386 11 5 11.2239 5 11.5V18H3.5C3.22386 18 3 17.7761 3 17.5V9.23611C2.38625 8.68679 2 7.8885 2 7V5.5C2.00002 5.3852 2.03955 5.27152 2.1159 5.17991L4.6159 2.17991C4.71089 2.06591 4.85162 2 5.00001 2ZM3 6V7C3 8.10457 3.89543 9 5 9C6.10457 9 7 8.10457 7 7V6H3ZM8 6V7C8 8.10457 8.89543 9 10 9C11.1046 9 12 8.10457 12 7V6H8ZM13 6V7C13 8.10457 13.8954 9 15 9C16.1046 9 17 8.10457 17 7V6H13ZM5.23419 3L3.56753 5H7.13964L7.8063 3H5.23419ZM11.8063 5L11.1396 3H8.8604L8.19373 5H11.8063ZM12.8604 5H16.4325L14.7658 3H12.1937L12.8604 5ZM11 11.5V14.5C11 14.7761 11.2239 15 11.5 15H14.5C14.7761 15 15 14.7761 15 14.5V11.5C15 11.2239 14.7761 11 14.5 11H11.5C11.2239 11 11 11.2239 11 11.5ZM9 18V12H6V18H9Z"
                    fill="currentColor"></path>
            </svg>


            <div class="text-xs my-auto">
                {{ $ticket->outlet->title }}
            </div>

        </div>
        @endif
        @if ($ticket->lead)
            <div class="flex space-x-1 justify-end">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                        clip-rule="evenodd"></path>
                </svg>


                <div class="text-xs my-auto">
                    {{ $ticket->lead->contact_number }}
                </div>

            </div>
        @endif


    </div>
    <div class="text-xs  text-slate-500">
        @if ($ticket->description)
            {{ $ticket->description }}
        @else
            <div class="invisible">No Desc</div>
        @endif
    </div>
    
    <div>
        @foreach ($ticket->tags as $tag)
            <span class="text-xs  text-white bg-gray-400 px-2 py-1 ">{{ $tag }}</span>
        @endforeach
    </div>
    <hr>
    <div class="grid grid-cols-3">
        <div class="flex text-xs items-center gap-1 font-semibold text-gray-500 cursor-pointer hover:text-indigo-500"
            onclick="Livewire.emitTo('tickets.show', 'openTicket',{{$ticket->id}})">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none"
                viewBox="0 0 24 24">
                <path
                    d="M5 5v14a1 1 0 0 0 1 1h3v-2H7V6h2V4H6a1 1 0 0 0-1 1zm14.242-.97-8-2A1 1 0 0 0 10 3v18a.998.998 0 0 0 1.242.97l8-2A1 1 0 0 0 20 19V5a1 1 0 0 0-.758-.97zM15 12.188a1.001 1.001 0 0 1-2 0v-.377a1 1 0 1 1 2 .001v.376z">
                </path>
            </svg>
            Open
        </div>
        <div class="flex text-xs items-center gap-1 font-semibold text-gray-500 cursor-pointer hover:text-indigo-500">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                <path
                    d="M253.7,133.7l-24,24a8.2,8.2,0,0,1-11.4,0l-24-24a8.4,8.4,0,0,1-1.7-8.8A8,8,0,0,1,200,120h15.6A87.9,87.9,0,0,0,54.1,80.3,8,8,0,0,1,43,82.6a7.9,7.9,0,0,1-2.4-11A104,104,0,0,1,231.7,120H248a8,8,0,0,1,7.4,4.9A8.4,8.4,0,0,1,253.7,133.7ZM213,173.4a8,8,0,0,0-11.1,2.3,86.9,86.9,0,0,1-8.1,10.8h0a81.3,81.3,0,0,0-24.5-23,59.7,59.7,0,0,1-82.6,0,81.3,81.3,0,0,0-24.5,23h0A87.6,87.6,0,0,1,40.4,136H56a8,8,0,0,0,7.4-4.9,8.4,8.4,0,0,0-1.7-8.8l-24-24a8.1,8.1,0,0,0-11.4,0l-24,24a8.4,8.4,0,0,0-1.7,8.8A8,8,0,0,0,8,136H24.3a103.7,103.7,0,0,0,34.5,69.6h0l.4.3a103.9,103.9,0,0,0,156.2-21.5A7.9,7.9,0,0,0,213,173.4ZM128,164a44,44,0,1,0-44-44A44,44,0,0,0,128,164Z">
                </path>
            </svg>
            Assign
        </div>
        <div class="flex text-xs items-center gap-1 font-semibold text-gray-500 cursor-pointer hover:text-red-400">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z">
                </path>
            </svg>
            Close
        </div>

    </div>
</div>
