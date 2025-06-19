<div class="bg-white p-4 space-y-2 text-xs ">
    <div class="flex">
        <h1 class=" font-bold flex-1">Acitivty Log</h1>
        
    </div>
    <div class="flex  ">
      
        <div class="flex-1 flex space-x-2">
            <div>
                <input type="checkbox" name="" value="2"  wire:model="fliterStatus"> Today Calls
            </div>
            <div>
                <input type="checkbox" name="" value="3"  wire:model="fliterStatus"> Past Calls
            </div>
            <div>
                <input type="checkbox" name="" value="1"  wire:model="fliterStatus"> Tickets
            </div>
        </div>
        <div>
            <div>
                <a href="#" wire:loading.remove wire:click.prevent="loadMore" class="border border-info-600 dark:hover:bg-slate-700 dark:ring-offset-slate-800 disabled:cursor-not-allowed disabled:opacity-80 duration-150 ease-in focus:ring-2 focus:ring-offset-2 gap-x-2 group hover:bg-info-50 hover:shadow-sm inline-flex items-center justify-center outline-none px-2 py-0.5 ring-info-600 rounded text-info-600 text-xs transition-all" data-tooltip-target="tooltip-default">Apply</a>
                <div wire:loading wire:target="loadMore">Loading</div>
            </div>
        </div>
    </div>
    <hr>

    @if (!$isLoad)
        <div class="text-center">
            Loading....
        </div>
    @else
    <div class="grid ">
        <div class="flex px-4">
            <ol class="relative border-l border-gray-200 dark:border-gray-700">
                @foreach ($timelineLogs->sortByDesc('created_at') as $timelineLog)
                    <li class="@if (!$loop->last) mb-10 @endif ml-6">
                        <span
                            class="flex absolute -left-4  justify-center items-center w-8 h-8 {{$timelineLog['last-reaction'] ? 'bg-red-500' : $timelineLog['bg-color'] }} rounded-full ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            @if ($timelineLog['icon'] == 'icon-phone')
                                <svg class="w-5 h-5 {{ $timelineLog['last-reaction'] ? 'text-black' : $timelineLog['icon-color'] }}"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M9.36737 3.31297L10.2271 5.34031C10.6018 6.22377 10.3939 7.26267 9.71313 7.9088L7.81881 9.70681C7.93569 10.7823 8.2972 11.8413 8.90334 12.8838C9.50948 13.9263 10.2665 14.7911 11.1744 15.4782L13.4496 14.7195C14.312 14.4319 15.2512 14.7624 15.7802 15.5396L17.0125 17.3502C17.6275 18.2536 17.5169 19.4999 16.7538 20.2659L15.9361 21.0869C15.1222 21.904 13.9597 22.2004 12.8843 21.8649C10.3454 21.073 8.01109 18.7218 5.88132 14.8113C3.74845 10.8951 2.9957 7.57254 3.62307 4.84353C3.88707 3.69522 4.70458 2.78074 5.77209 2.43963L6.84868 2.09563C7.8575 1.77327 8.93535 2.29422 9.36737 3.31297ZM14.4982 8.43909L20.7188 2.2159C21.0116 1.92293 21.4865 1.92281 21.7794 2.21563C22.0458 2.48183 22.0701 2.89848 21.8523 3.19215L21.7797 3.27629L15.5592 9.49909L20.2503 9.49911C20.63 9.49911 20.9438 9.78126 20.9935 10.1473L21.0003 10.2491C21.0003 10.6288 20.7182 10.9426 20.3521 10.9923L20.2503 10.9991L13.6955 10.9984L13.5973 10.9849L13.502 10.9586L13.4341 10.9301C13.3559 10.8952 13.2829 10.8451 13.2186 10.7809L13.1779 10.7368L13.1529 10.7046L13.1054 10.636L13.0592 10.5445L13.0268 10.453L13.0105 10.3829L13.0006 10.3019L12.9988 10.2491V3.74522C12.9988 3.331 13.3346 2.99522 13.7488 2.99522C14.1285 2.99522 14.4423 3.27737 14.492 3.64344L14.4988 3.74522L14.4982 8.43909L20.7188 2.2159L14.4982 8.43909Z"
                                        fill="currentColor"></path>
                                </svg>
                            @endif
                            @if ($timelineLog['icon'] == 'icon-phone-out')
                                <svg class="w-5 h-5 {{$timelineLog['last-reaction'] ? 'text-gray-900' : $timelineLog['icon-color'] }}"
                                    viewBox="0 0 51 51" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="48" height="48" transform="translate(3)"
                                        fill="white" fill-opacity="0.01"></rect>
                                    <rect x="3" width="48" height="48" fill="white"
                                        fill-opacity="0.01"></rect>
                                    <path d="M31 20L44 7.5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M31 7H44V20" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M17.3757 9.79423C18.1022 9.79423 18.7716 10.1883 19.1243 10.8235L21.5707 15.2303C21.891 15.8073 21.9061 16.5052 21.6109 17.0955L19.2541 21.8092C19.2541 21.8092 19.9371 25.3206 22.7955 28.179C25.654 31.0374 29.1536 31.7086 29.1536 31.7086L33.8665 29.3522C34.4572 29.0568 35.1556 29.0721 35.7328 29.393L40.1521 31.85C40.7868 32.2028 41.1803 32.8719 41.1803 33.598L41.1803 38.6715C41.1803 41.2552 38.7804 43.1213 36.3324 42.2952C31.3044 40.5987 23.4997 37.3685 18.5529 32.4216C13.606 27.4748 10.3758 19.6701 8.67928 14.6422C7.85326 12.1941 9.71935 9.79423 12.303 9.79423L17.3757 9.79423Z"
                                        fill="currentColor" stroke="currentColor" stroke-width="2"
                                        stroke-linejoin="round"></path>
                                </svg>
                            @endif
                            @if ($timelineLog['icon'] == 'icon-order')
                                <svg class="w-5 h-5 {{$timelineLog['last-reaction'] ? 'text-gray-900' : $timelineLog['icon-color'] }}" width="48"
                                    height="48" viewBox="0 0 48 48" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 10.5001C23.1056 10.0529 23.1053 10.0535 23.1053 10.0535L23.1042 10.0557L23.102 10.0601L23.0956 10.0731L23.0749 10.1162C23.0577 10.1523 23.0341 10.203 23.0055 10.2668C22.9483 10.3944 22.8708 10.5752 22.7854 10.7974C22.6156 11.2388 22.4087 11.8575 22.2694 12.554C22.0052 13.8752 21.9175 15.8318 23.2929 17.2072C23.9175 17.8318 24.0052 18.8752 23.7694 20.054C23.6587 20.6075 23.4906 21.1138 23.3479 21.4849C23.2771 21.669 23.2139 21.8162 23.1695 21.9153C23.1474 21.9647 23.13 22.0019 23.1188 22.0253L23.107 22.05L23.1053 22.0534C23.1053 22.0534 23.1056 22.0529 24 22.5001C24.8944 22.9473 24.895 22.9461 24.895 22.9461L24.8958 22.9445L24.898 22.9402L24.9044 22.9271L24.9251 22.8841C24.9423 22.848 24.9659 22.7972 24.9945 22.7334C25.0517 22.6058 25.1292 22.425 25.2146 22.2028C25.3844 21.7614 25.5913 21.1427 25.7306 20.4462C25.9948 19.125 26.0825 17.1684 24.7071 15.793C24.0825 15.1684 23.9948 14.125 24.2306 12.9462C24.3413 12.3927 24.5094 11.8864 24.6521 11.5153C24.7229 11.3312 24.7861 11.184 24.8305 11.0849C24.8526 11.0355 24.87 10.9983 24.8812 10.9749L24.893 10.9502L24.8947 10.9468C24.8947 10.9468 24.8944 10.9473 24 10.5001Z">
                                    </path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4 29.0001C4 28.4478 4.44772 28.0001 5 28.0001H43C43.5523 28.0001 44 28.4478 44 29.0001C44 29.5524 43.5523 30.0001 43 30.0001H40C40 34.4184 36.4183 38.0001 32 38.0001H16C11.5817 38.0001 8 34.4184 8 30.0001H5C4.44772 30.0001 4 29.5524 4 29.0001ZM10 30.0001H38C38 33.3138 35.3137 36.0001 32 36.0001H16C12.6863 36.0001 10 33.3138 10 30.0001Z">
                                    </path>
                                    <path
                                        d="M15.1707 12.4412C15.1707 12.4412 15.1712 12.4406 16 13.0001C16.8288 13.5596 16.8292 13.5591 16.8292 13.5591L16.8169 13.5781C16.8054 13.596 16.787 13.6252 16.7632 13.6643C16.7157 13.7429 16.6475 13.8606 16.5711 14.0078C16.416 14.3066 16.2377 14.7062 16.1217 15.1358C15.8808 16.028 15.9674 16.7315 16.5952 17.1966C18.2174 18.3982 18.1308 20.1947 17.8092 21.3858C17.6439 21.9979 17.4003 22.5357 17.2039 22.914C17.1046 23.1054 17.0145 23.2612 16.9478 23.3714C16.9144 23.4266 16.8867 23.4707 16.8663 23.5024L16.8456 23.5344L16.8415 23.5406L16.8335 23.5526L16.8308 23.5567L16.8297 23.5583C16.8297 23.5583 16.8288 23.5596 16 23.0001C15.1712 22.4406 15.1708 22.4412 15.1708 22.4412L15.1831 22.4221C15.1946 22.4042 15.213 22.3751 15.2368 22.3359C15.2843 22.2573 15.3525 22.1397 15.4289 21.9924C15.584 21.6937 15.7623 21.294 15.8783 20.8644C16.1192 19.9722 16.0326 19.2687 15.4048 18.8037C13.7826 17.602 13.8692 15.8055 14.1908 14.6144C14.3561 14.0024 14.5997 13.4645 14.7961 13.0862C14.8954 12.8949 14.9855 12.7391 15.0522 12.6288C15.0856 12.5736 15.1133 12.5295 15.1337 12.4978C15.1439 12.482 15.1522 12.4692 15.1585 12.4596L15.1665 12.4476L15.1692 12.4435L15.1707 12.4412Z">
                                    </path>
                                    <path
                                        d="M33 13.0001C32.1712 12.4406 32.1707 12.4412 32.1707 12.4412L32.1692 12.4435L32.1665 12.4476L32.1585 12.4596C32.1522 12.4692 32.1439 12.482 32.1337 12.4978C32.1133 12.5295 32.0856 12.5736 32.0522 12.6288C31.9855 12.7391 31.8954 12.8949 31.7961 13.0862C31.5997 13.4645 31.3561 14.0024 31.1908 14.6144C30.8692 15.8055 30.7826 17.602 32.4048 18.8037C33.0326 19.2687 33.1192 19.9722 32.8783 20.8644C32.7623 21.294 32.584 21.6937 32.4289 21.9924C32.3525 22.1397 32.2843 22.2573 32.2367 22.3359C32.213 22.3751 32.1946 22.4042 32.1831 22.4221L32.1708 22.4412C32.1708 22.4412 32.1712 22.4406 33 23.0001C33.8288 23.5596 33.8297 23.5583 33.8297 23.5583L33.8308 23.5567L33.8335 23.5526L33.8415 23.5406L33.8573 23.5164L33.8663 23.5024C33.8867 23.4707 33.9144 23.4266 33.9478 23.3714C34.0145 23.2612 34.1046 23.1054 34.2039 22.914C34.4003 22.5357 34.6439 21.9979 34.8092 21.3858C35.1308 20.1947 35.2174 18.3982 33.5952 17.1966C32.9674 16.7315 32.8808 16.028 33.1217 15.1358C33.2377 14.7062 33.416 14.3066 33.5711 14.0078C33.6475 13.8606 33.7157 13.7429 33.7632 13.6643C33.787 13.6252 33.8054 13.596 33.8169 13.5781L33.8292 13.5591C33.8292 13.5591 33.8288 13.5596 33 13.0001Z">
                                    </path>
                                </svg>
                            @endif
                            @if ($timelineLog['icon'] == 'icon-ticket')
                                <svg class="w-5 h-5 {{$timelineLog['last-reaction'] ? 'text-gray-900' : $timelineLog['icon-color'] }} "
                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z">
                                    </path>
                                </svg>
                            @endif


                        </span>
                        <h3
                            class="flex items-center mb-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $timelineLog['title'] }}
                            @if ($loop->first)
                            
                            <div class="relative group inline-block px-3">

    @if (!empty($timelineLog['last-comment']))
        <div class="pl-2 inline-block relative">
            <div class="cursor-pointer "><span>
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path d="M7.5 10.5H7.51M12 10.5H12.01M16.5 10.5H16.51M7 18V20.3355C7 20.8684 7 21.1348 7.10923 21.2716C7.20422 21.3906 7.34827 21.4599 7.50054 21.4597C7.67563 21.4595 7.88367 21.2931 8.29976 20.9602L10.6852 19.0518C11.1725 18.662 11.4162 18.4671 11.6875 18.3285C11.9282 18.2055 12.1844 18.1156 12.4492 18.0613C12.7477 18 13.0597 18 13.6837 18H16.2C17.8802 18 18.7202 18 19.362 17.673C19.9265 17.3854 20.3854 16.9265 20.673 16.362C21 15.7202 21 14.8802 21 13.2V7.8C21 6.11984 21 5.27976 20.673 4.63803C20.3854 4.07354 19.9265 3.6146 19.362 3.32698C18.7202 3 17.8802 3 16.2 3H7.8C6.11984 3 5.27976 3 4.63803 3.32698C4.07354 3.6146 3.6146 4.07354 3.32698 4.63803C3 5.27976 3 6.11984 3 7.8V14C3 14.93 3 15.395 3.10222 15.7765C3.37962 16.8117 4.18827 17.6204 5.22354 17.8978C5.60504 18 6.07003 18 7 18ZM8 10.5C8 10.7761 7.77614 11 7.5 11C7.22386 11 7 10.7761 7 10.5C7 10.2239 7.22386 10 7.5 10C7.77614 10 8 10.2239 8 10.5ZM12.5 10.5C12.5 10.7761 12.2761 11 12 11C11.7239 11 11.5 10.7761 11.5 10.5C11.5 10.2239 11.7239 10 12 10C12.2761 10 12.5 10.2239 12.5 10.5ZM17 10.5C17 10.7761 16.7761 11 16.5 11C16.2239 11 16 10.7761 16 10.5C16 10.2239 16.2239 10 16.5 10C16.7761 10 17 10.2239 17 10.5Z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</span></div>

            <!-- Tooltip -->
            <div class="absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-800 text-white text-sm rounded-lg px-3 py-2 shadow-lg whitespace-pre-line w-64">
                {{ $timelineLog['last-comment'] }}
            </div>
        </div>
    @endif
</div>

                            
                                <span
                                    class="animate-ping  bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 ml-3">Latest</span>
                            @endif
                        </h3>
                        <time
                            class="block mb-2 text-xs font-normal leading-none text-gray-400 dark:text-gray-500">{{ $timelineLog['created_date'] }}
                            at {{ $timelineLog['created_time'] }}
                        </time>
                        <div
                            class="mb-1 text-xs font-normal text-gray-500 dark:text-gray-400 grid grid-cols-2 gap-1">
                            <div> Agent :{{ $timelineLog['created_by'] }} </div>
                            @if (in_array($timelineLog['title'], ['Order', 'Ticket']))
                                <div>
                                    @if ($timelineLog['outlet'])
                                        Outlet : {{ $timelineLog['outlet'] }}
                                    @endif
                                </div>
                                @if (in_array($timelineLog['title'], ['Order']))
                                    <div> Sale Total : Rs
                                        {{ number_format($timelineLog['order_total'], 2) }}</div>
                                @endif
                                @if (in_array($timelineLog['title'], ['Ticket']))
                                    <div> Category : {{ $timelineLog['category'] }}</div>
                                    <div> Status : {{ $timelineLog['status'] }}</div>
                                @endif
                            @endif

                        </div>
                        @if (in_array($timelineLog['title'], ['Order', 'Ticket']))
                            <a href="#"
                                onclick="Livewire.emitTo('{{ $timelineLog['component'] }}','{{ $timelineLog['action'] }}' ,{{ $timelineLog['id'] }},1);return false;"
                                class="inline-flex items-center py-1 px-2.5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-200 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">


                                <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776">
                                    </path>
                                </svg>
                                View {{ $timelineLog['title'] }}</a>
                        @endif
                        {{-- @if( $timelineLog['last-reaction'])
                        <label class="text-red-500">Last time angry customer </label>
                        @endif --}}

                    </li>
                @endforeach

            </ol>
        </div>
    </div>
    @endif

</div>