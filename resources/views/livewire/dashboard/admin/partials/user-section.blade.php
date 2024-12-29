<div class="flex flex-col container max-w-md  mx-auto w-full items-center justify-center bg-white dark:bg-gray-800 rounded-lg shadow"
    wire:poll.2000ms>
    <ul class="flex flex-col divide-y w-full">

        @foreach ($users as $user)
            @php
                if($user->extensionDetails)
                {
                    $currentCallStatus = Cache::store('custom_redis')->connection('live_events')->get(strtoupper($user->extensionDetails->exten_type) . '/' . $user->extension);
                    $jsonData = json_decode($currentCallStatus, true);
                }else{
                    $currentCallStatus="No Exten";
                }
                // $inCall = true;
            @endphp
            <li
                class="flex flex-row @if (!$user->current_active_queues_title) order-last grayscale @else @if ($user->user->on_break) order-2 @else order-first @endif @endif ">
                <div class="select-none cursor-pointer hover:bg-gray-50 flex flex-1 items-center p-4">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                        <a href="#" class="block relative">
                            {{--  <img alt="profil"
                                src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&q=80"
                                class="mx-auto object-cover rounded-full h-10 w-10" /> --}}
                            <svg class="mx-auto object-cover rounded-full h-10 w-10" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 256 256"
                                xml:space="preserve">

                                <defs>
                                </defs>
                                <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;"
                                    transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                    <path
                                        d="M 64.643 15.053 C 62.216 6.384 54.264 0 44.831 0 S 27.445 6.384 25.019 15.053 c -1.695 0.217 -3.022 1.659 -3.022 3.411 v 9.872 c 0 1.9 1.555 3.455 3.455 3.455 s 3.455 -1.555 3.455 -3.455 v -9.872 c 0 -1.189 -0.609 -2.243 -1.531 -2.865 c 2.176 -7.596 9.169 -13.177 17.454 -13.177 c 8.286 0 15.279 5.581 17.454 13.177 c -0.921 0.622 -1.53 1.676 -1.53 2.865 v 9.872 c 0 1.307 0.744 2.437 1.821 3.023 c -0.698 3.214 -2.242 6.114 -4.396 8.453 c -0.148 -0.029 -0.299 -0.046 -0.455 -0.046 c -1.32 0 -2.399 1.08 -2.399 2.399 s 1.08 2.399 2.399 2.399 s 2.399 -1.08 2.399 -2.399 c 0 -0.259 -0.052 -0.504 -0.129 -0.737 c 2.467 -2.688 4.225 -6.031 4.991 -9.733 c 1.528 -0.356 2.679 -1.726 2.679 -3.359 v -9.872 C 67.665 16.712 66.339 15.27 64.643 15.053 z"
                                        style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(240,88,47); fill-rule: nonzero; opacity: 1;"
                                        transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                    <path
                                        d="M 55.893 35.514 c 0.655 -0.905 1.196 -1.885 1.611 -2.917 c -0.802 -1.252 -1.249 -2.724 -1.249 -4.26 v -9.872 c 0 -1.313 0.323 -2.582 0.918 -3.708 c -2.231 -4.703 -7.034 -7.834 -12.342 -7.834 s -10.11 3.131 -12.342 7.835 c 0.595 1.126 0.918 2.395 0.918 3.708 v 9.872 c 0 2.391 -1.065 4.533 -2.74 5.993 c 2.703 5.183 8.111 8.754 14.333 8.754 c 2.059 0 4.028 -0.395 5.842 -1.105 C 50.927 38.892 53.034 36.303 55.893 35.514 z"
                                        style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(110,177,225); fill-rule: nonzero; opacity: 1;"
                                        transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                    <path
                                        d="M 61.749 47.762 c -1.134 0.818 -2.523 1.303 -4.024 1.303 c -2.299 0 -4.334 -1.135 -5.588 -2.87 c -2.219 0.862 -4.622 1.345 -7.136 1.345 c -3.281 0 -6.375 -0.816 -9.113 -2.234 c -12.235 1.826 -21.7 12.444 -21.7 25.167 v 16.741 c 0 1.532 1.253 2.786 2.786 2.786 h 56.054 c 1.532 0 2.786 -1.254 2.786 -2.786 V 70.474 C 75.813 60.568 70.064 51.96 61.749 47.762 z"
                                        style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(110,177,225); fill-rule: nonzero; opacity: 1;"
                                        transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                </g>
                            </svg>
                        </a>
                    </div>
                    <div class="flex-1 pl-1">
                        <div
                            class=" dark:text-white @if (!$user->current_active_queues_title) text-gray-500 font-medium @else text-green-600 font-semibold @endif">
                            {{ ucwords($user->fname) }}</div>
                        <div class="text-gray-600 dark:text-gray-200 text-xs py-2">
                            @if ($user->current_active_queues_title)
                                {{-- {{$user->current_active_queues_title}} --}}
                                @foreach ($user->current_active_queues_title as $item)
                                    <span
                                        class="px-1 py-0.5 text-2xs font-normal text-teal-700 uppercase bg-teal-200 rounded-md dark:text-teal-800 dark:bg-teal-400 bg-opacity-40">{{ $item }}</span>
                                @endforeach
                            @endif


                        </div>
                      
                        
                    </div>
                    <div class="flex flex-row justify-center">
                        <div class="text-gray-600 dark:text-gray-200 text-xs">{{ $user->todayQueues->count() }}</div>
                        <button class="w-10 text-right flex justify-end">
                            {{-- <svg width="20" fill="currentColor" height="20"
                                class="hover:text-gray-800 dark:hover:text-white dark:text-gray-200 text-gray-500"
                                viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1363 877l-742 742q-19 19-45 19t-45-19l-166-166q-19-19-19-45t19-45l531-531-531-531q-19-19-19-45t19-45l166-166q19-19 45-19t45 19l742 742q19 19 19 45t-19 45z">
                                </path>
                            </svg> --}}
                            @if ($user->current_active_queues_title)
                                @if ($user->user->on_break)
                                    @if ($user->user->agent_break_type != 'ACW')
                                        <svg width="20" height="20" class="text-amber-500"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill="currentColor"
                                                d="M1.38197865,8.50468479 L1.38197865,13.5631773 C1.38197865,16.3563179 3.64597172,18.6206981 6.43875483,18.6206981 L10.1164674,18.6206981 C12.9092315,18.6206981 15.1731941,16.3563849 15.1731941,13.5632501 L15.1731941,8.50570164 L1.38197865,8.50468479 Z M11.887167,0.161126688 C12.1790318,0.405797785 12.2173171,0.840778552 11.9726796,1.13268342 C11.3308774,1.89849054 11.1893646,2.41967954 11.3874211,2.76994178 C11.4488712,2.8786161 11.9803359,3.71845921 12.1019698,3.97653935 C12.3777947,4.56177877 12.4141083,5.12932211 12.1755421,5.79896276 C12.0096241,6.26468436 11.7585443,6.7072078 11.424894,7.12657781 L15.306126,7.12640952 C15.7430066,7.14261066 16.0599124,7.25440509 16.2568432,7.46179281 C16.3655095,7.57622922 16.4442154,7.72017702 16.4929381,7.89363623 L16.5522181,7.89203692 L16.5522181,7.89203692 C18.4563755,7.89203692 20,9.43587318 20,11.3402917 C20,13.2447103 18.4563755,14.7885465 16.5522181,14.7885465 L16.4365643,14.7847793 C15.8659428,17.7555445 13.2532898,20 10.1164728,20 L6.43872469,20 C2.88430599,20 0.00288657549,17.1180836 0.00288657549,13.5631773 L0.00286521846,8.33673221 C-0.0167941967,7.90387044 0.0647147488,7.59740722 0.247392055,7.41734256 C0.430069361,7.2372779 0.699557002,7.14030022 1.05585498,7.12640952 L4.00913096,7.12670462 C4.04659087,7.06454857 4.09457662,7.00729401 4.15287076,6.95765169 C4.76165561,6.43922067 5.15955413,5.89859775 5.35998008,5.33601443 C5.47365368,5.01693962 5.46146952,4.82651397 5.33804655,4.56463778 C5.25990012,4.39882836 4.76908244,3.62321744 4.67052633,3.44892126 C4.13215786,2.49681825 4.42865329,1.40483106 5.39929252,0.246650998 C5.64393006,-0.0452538722 6.07885117,-0.0835444104 6.370716,0.161126688 C6.66258084,0.405797785 6.70086613,0.840778552 6.45622859,1.13268342 C5.81442645,1.89849054 5.67291366,2.41967954 5.87097011,2.76994178 C5.93242021,2.8786161 6.46388494,3.71845921 6.58551882,3.97653935 C6.86134377,4.56177877 6.89765735,5.12932211 6.65909115,5.79896276 C6.49317317,6.26468436 6.24209337,6.7072078 5.90844306,7.12657781 L6.76735645,7.12670462 C6.80481636,7.06454857 6.8528021,7.00729401 6.91109625,6.95765169 C7.5198811,6.43922067 7.91777962,5.89859775 8.11820556,5.33601443 C8.23187916,5.01693962 8.21969501,4.82651397 8.09627204,4.56463778 C8.0181256,4.39882836 7.52730793,3.62321744 7.42875181,3.44892126 C6.89038335,2.49681825 7.18687878,1.40483106 8.15751801,0.246650998 C8.40215555,-0.0452538722 8.83707666,-0.0835444104 9.12894149,0.161126688 C9.42080633,0.405797785 9.45909161,0.840778552 9.21445407,1.13268342 C8.57265194,1.89849054 8.43113915,2.41967954 8.6291956,2.76994178 C8.6906457,2.8786161 9.22211043,3.71845921 9.34374431,3.97653935 C9.61956926,4.56177877 9.65588284,5.12932211 9.41731664,5.79896276 C9.25139865,6.26468436 9.00031885,6.7072078 8.66666855,7.12657781 L9.52558193,7.12670462 C9.56304184,7.06454857 9.61102759,7.00729401 9.66932174,6.95765169 C10.2781066,6.43922067 10.6760051,5.89859775 10.8764311,5.33601443 C10.9901047,5.01693962 10.9779205,4.82651397 10.8544975,4.56463778 C10.7763511,4.39882836 10.2855334,3.62321744 10.1869773,3.44892126 C9.64860884,2.49681825 9.94510427,1.40483106 10.9157435,0.246650998 C11.160381,-0.0452538722 11.5953021,-0.0835444104 11.887167,0.161126688 Z M16.5522181,9.27133884 L16.5522181,13.4092446 L16.5522181,13.4092446 C17.6947125,13.4092446 18.6208873,12.4829429 18.6208873,11.3402917 C18.6208873,10.1976406 17.6947125,9.27133884 16.5522181,9.27133884 Z">
                                            </path>
                                        </svg>
                                    @elseif($user->user->agent_break_type == 'ACW')
                                        <svg class="w-17 h-17 text-amber-500" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M21 12a9 9 0 1 0 -9.972 8.948c.32 .034 .644 .052 .972 .052"></path>
                                            <path d="M12 7v5l2 2"></path>
                                            <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z">
                                            </path>
                                        </svg>
                                    @endif
                                @else
                                    @php
                                        // $inCall = Cache::get('agent-in-call-' . $user->id);
                                        $inCall = isset($jsonData['status'])&& $jsonData['status']==1;
                                        $currentCallStatus = Cache::get(
                                            $user->extensionDetails->exten_type . '/' . $user->extension,
                                        );
                                        // $inCall = true;
                                    @endphp


                                    @if ($inCall)
                                        <div x-data="{ open: false }">

                                            <svg width="20" height="20" @click="open = !open"
                                                class="text-green-700 @can('is-admin') cursor-pointer @endcan"
                                                {{-- @can('is-admin') wire:click="listenCall({{ $user->extension }})" @endcan --}} xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z">
                                                </path>
                                            </svg>

                                            <div x-show="open" x-cloak
                                                class="flex flex-col items-center space-y-3 mt-4">
                                                @can('is-admin')
                                                    <div class="flex space-x-3">
                                                        <svg class="w-6 h-6 text-red-600 @can('is-admin') cursor-pointer @endcan"
                                                            viewBox="0 0 24 24"
                                                            wire:click="listenCall('{{ $user->extension }}', '{{ $user->extensionDetails->exten_type }}', 'L')"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M4 12H7C8.10457 12 9 12.8954 9 14V19C9 20.1046 8.10457 21 7 21H4C2.89543 21 2 20.1046 2 19V12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12V19C22 20.1046 21.1046 21 20 21H17C15.8954 21 15 20.1046 15 19V14C15 12.8954 15.8954 12 17 12H20C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12Z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex space-x-3 ">
                                                        <svg class="w-6 h-6 text-orange-400 @can('is-admin') cursor-pointer @endcan"
                                                            viewBox="0 0 24 24"
                                                            wire:click="listenCall('{{ $user->extension }}', '{{ $user->extensionDetails->exten_type }}', 'W')"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M2.5 7C2.5 9.20914 4.29086 11 6.5 11C8.70914 11 10.5 9.20914 10.5 7C10.5 4.79086 8.70914 3 6.5 3C4.29086 3 2.5 4.79086 2.5 7ZM2 21V16.5C2 14.0147 4.01472 12 6.5 12C8.98528 12 11 14.0147 11 16.5V21H2ZM17.5 11C15.2909 11 13.5 9.20914 13.5 7C13.5 4.79086 15.2909 3 17.5 3C19.7091 3 21.5 4.79086 21.5 7C21.5 9.20914 19.7091 11 17.5 11ZM13 21V16.5C13 14.0147 15.0147 12 17.5 12C19.9853 12 22 14.0147 22 16.5V21H13Z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex space-x-3">
                                                        <svg class="w-6 h-6 text-green-500 @can('is-admin') cursor-pointer @endcan"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                            wire:click="listenCall('{{ $user->extension }}', '{{ $user->extensionDetails->exten_type }}', 'B')"
                                                            viewBox="0 0 16 16" fill="currentColor">
                                                            <path fill="currentColor"
                                                                d="M5 16v-5.3c-0.6-0.3-1-1-1-1.7v-4c0-0.7 0.4-1.3 1-1.7 0-0.1 0-0.2 0-0.3 0-1.1-0.9-2-2-2s-2 0.9-2 2c0 1.1 0.9 2 2 2h-2c-0.5 0-1 0.5-1 1v4c0 0.5 0.5 1 1 1v5h4z">
                                                            </path>
                                                            <path fill="currentColor"
                                                                d="M15 5h-2c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2c0 0.1 0 0.2 0 0.3 0.6 0.4 1 1 1 1.7v4c0 0.7-0.4 1.4-1 1.7v5.3h4v-5c0.5 0 1-0.5 1-1v-4c0-0.5-0.5-1-1-1z">
                                                            </path>
                                                            <path fill="currentColor"
                                                                d="M10 2c0 1.105-0.895 2-2 2s-2-0.895-2-2c0-1.105 0.895-2 2-2s2 0.895 2 2z">
                                                            </path>
                                                            <path fill="currentColor"
                                                                d="M10 4h-4c-0.5 0-1 0.5-1 1v4c0 0.5 0.5 1 1 1v6h4v-6c0.5 0 1-0.5 1-1v-4c0-0.5-0.5-1-1-1z">
                                                            </path>
                                                        </svg>

                                                    </div>
                                                @endcan

                                            </div>
                                        </div>
                                    @endif

                                @endif
                            @else
                            @endif
                        </button>
                    </div>
                </div>
            </li>
        @endforeach


    </ul>
</div>
