<div class="w-full h-[15cm] overflow-y-auto pl-2">
    @foreach ($users as $user)
        <div class="py-2 ">
            <div class=" h-[1.2cm] bg-slate-100 hover:bg-gray-300 transition duration-300 flex justify-between rounded-md cursor-pointer"
            {{-- <div class=" h-[1.2cm] bg-slate-100 hover:bg-gray-300 transition duration-300 flex justify-between rounded-md cursor-pointer @if (in_array($user->id, $highlightedUserIds)) bg-blue-300 font-bold @endif" --}}
                wire:click="userSelected({{ $user->id }})">
                <div class="m-1">
                    {{-- <svg class="w-8 h-8 {{ $user->isLoggedIn() ? 'text-blue-400' : 'text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="currentColor">
                        <path
                                d="M1280 1399c0 146-96 265-213 265H213C96 1664 0 1545 0 1399c0-263 65-567 327-567 81 79 191 128 313 128s232-49 313-128c262 0 327 304 327 567zm-256-887c0 212-172 384-384 384S256 724 256 512s172-384 384-384 384 172 384 384z">

                        </path>
                    </svg> --}}



                    <div class="relative w-6 h-10">
    <!-- New User Icon -->
    <svg class="w-full h-12 {{ $user->isLoggedIn() ? 'text-blue-400' : 'text-gray-500' }}" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/>
    </svg>

    <!-- Message Icon -->
    @if (in_array($user->id, $highlightedUserIds))
    <div class="absolute top-0 right-[-30px] w-8 h-8">
        <svg class="w-full h-full {{ $user->isLoggedIn() ? 'text-green-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <g fill="currentColor">
                <path d="M4 19l-.93-.37a1 1 0 001.125 1.35L4 19zm4.706-.936l.474-.881-.317-.17-.352.07.195.98zm-3.082-3.147l.93.37.163-.414-.196-.399-.897.443zM19 12c0 3.246-2.853 6-6.53 6v2c4.641 0 8.53-3.514 8.53-8h-2zM5.941 12c0-3.246 2.854-6 6.53-6V4C7.83 4 3.94 7.514 3.94 12h2zm6.53-6C16.147 6 19 8.754 19 12h2c0-4.486-3.889-8-8.53-8v2zm0 12c-1.205 0-2.328-.3-3.291-.817l-.948 1.761A8.934 8.934 0 0012.471 20v-2zm-8.276 1.98l4.706-.936-.39-1.961-4.706.936.39 1.962zm2.326-5.506A5.564 5.564 0 015.94 12h-2c0 1.2.282 2.338.786 3.36l1.794-.886zm-1.826.073L3.07 18.631l1.858.738 1.624-4.083-1.858-.739z"/>
                <circle cx="9" cy="12" r="1"/>
                <circle cx="12.5" cy="12" r="1"/>
                <circle cx="16" cy="12" r="1"/>
            </g>
        </svg>
    </div>
    @endif
</div>
                    

                    

                </div>
                <div class="m-2">
                    {{ $user->name }}
                </div>

            </div>
        </div>
        <hr>
    @endforeach
</div>


{{-- <svg class="w-64 h-64" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="currentColor">
    <path
        d="M1280 1399c0 146-96 265-213 265H213C96 1664 0 1545 0 1399c0-263 65-567 327-567 81 79 191 128 313 128s232-49 313-128c262 0 327 304 327 567zm-256-887c0 212-172 384-384 384S256 724 256 512s172-384 384-384 384 172 384 384z">
    </path>
</svg> --}}


{{-- <div class="relative w-8 h-8">
    <!-- New User Icon -->
    <svg class="w-full h-full text-gray-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
        <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/>
    </svg>

    <!-- Message Icon -->
    <div class="absolute top-0 right-[-12px] w-4 h-4">
        <svg class="w-full h-full text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <g fill="currentColor">
                <path d="M4 19l-.93-.37a1 1 0 001.125 1.35L4 19zm4.706-.936l.474-.881-.317-.17-.352.07.195.98zm-3.082-3.147l.93.37.163-.414-.196-.399-.897.443zM19 12c0 3.246-2.853 6-6.53 6v2c4.641 0 8.53-3.514 8.53-8h-2zM5.941 12c0-3.246 2.854-6 6.53-6V4C7.83 4 3.94 7.514 3.94 12h2zm6.53-6C16.147 6 19 8.754 19 12h2c0-4.486-3.889-8-8.53-8v2zm0 12c-1.205 0-2.328-.3-3.291-.817l-.948 1.761A8.934 8.934 0 0012.471 20v-2zm-8.276 1.98l4.706-.936-.39-1.961-4.706.936.39 1.962zm2.326-5.506A5.564 5.564 0 015.94 12h-2c0 1.2.282 2.338.786 3.36l1.794-.886zm-1.826.073L3.07 18.631l1.858.738 1.624-4.083-1.858-.739z"/>
                <circle cx="9" cy="12" r="1"/>
                <circle cx="12.5" cy="12" r="1"/>
                <circle cx="16" cy="12" r="1"/>
            </g>
        </svg>
    </div>
</div> --}}

