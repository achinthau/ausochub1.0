<div wire:init="refreshTimeline">
    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-2">

            <div class="flex justify-between space-x-2">

<div>
                @if(config('app.show_next_contact_button'))
                @if($boundType == 'dialer' && filled($campaign))
                    <button type="button" wire:click="nextContact"
                        class="border border-green-800 bg-green-500 dark:hover:bg-green-400 dark:ring-offset-slate-800 disabled:cursor-not-allowed disabled:opacity-80 duration-150 ease-in focus:ring-2 focus:ring-offset-2 gap-x-2 group hover:bg-green-400 hover:shadow-sm inline-flex items-center justify-center outline-none px-4 py-2 ring-emerald-600 rounded text-white text-m transition-all font-bold">
                        {{-- <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg> --}}
                        Next Customer
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
  <path d="M2 5v14c0 .86 1.012 1.318 1.659 .753l8 -7a1 1 0 0 0 0 -1.506l-8 -7c-.647 -.565 -1.659 -.106 -1.659 .753z"></path>
  <path d="M13 5v14c0 .86 1.012 1.318 1.659 .753l8 -7a1 1 0 0 0 0 -1.506l-8 -7c-.647 -.565 -1.659 -.106 -1.659 .753z"></path>
</svg>
                    </button>
                @endif
                @endif
                </div>
                <div>
                <a href="#" onclick="Livewire.emitTo('leads.create', 'openCreateLeadModal')"
                    class="border border-info-600 dark:hover:bg-slate-700 dark:ring-offset-slate-800 disabled:cursor-not-allowed disabled:opacity-80 duration-150 ease-in focus:ring-2 focus:ring-offset-2 gap-x-2 group hover:bg-info-50 hover:shadow-sm inline-flex items-center justify-center outline-none px-4 py-0.5 ring-info-600 rounded text-info-600 text-sm transition-all">
                    <svg class="w-6 h-6 " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z">
                        </path>
                    </svg>
                    Edit
                </a>

                {{-- @if ($lead->status_id == 2) --}}
                <button type="button" onclick="Livewire.emitTo('tickets.create', 'showCreatingTicket'); return false;"
                    class="border border-info-600 dark:hover:bg-slate-700 dark:ring-offset-slate-800 disabled:cursor-not-allowed disabled:opacity-80 duration-150 ease-in focus:ring-2 focus:ring-offset-2 gap-x-2 group hover:bg-info-50 hover:shadow-sm inline-flex items-center justify-center outline-none px-4 py-0.5 ring-info-600 rounded text-info-600 text-sm transition-all">
                    <svg class="w-6 h-6 " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z">
                        </path>
                    </svg>
                    Ticket
                </button>
                {{-- <a href="#" onclick="$openModal('CreatingOrder')" class="outline-none inline-flex justify-center items-center group transition-all ease-in duration-150 focus:ring-2 focus:ring-offset-2 hover:shadow-sm disabled:opacity-80 disabled:cursor-not-allowed rounded gap-x-2 text-sm px-4 py-0.5     ring-positive-500 text-positive-500 border border-positive-500 hover:bg-positive-50
                                                       dark:ring-offset-slate-800 dark:hover:bg-slate-700">
                    <svg class="w-6 h-6" width="48" height="48" viewBox="0 0 48 48" fill="currentColor"
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

                    Order
                </a> --}}


                {{-- @else
                <x-button icon="pencil" positive label="Complete Profile" onclick="$openModal('showTicketEditModal')" />
                @endif --}}

            </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-2 h-max space-y-2">
                    <div class="bg-white p-4 space-y-2 text-sm">
                        <h1 class=" font-bold">Customer Information</h1>
                        <hr>
                        <div class="grid grid-cols-2">
                            <div class="flex ">
                                <div class="w-1/2">Customer Name : </div>
                                <div class="w-1/2 font-semibold">{{ $lead->full_name ?? '--' }}</div>
                            </div>
                            <div class="flex ">
                                <div class="w-1/2">Contact Number: </div>
                                <div class="w-1/2 font-semibold">{{ $lead->contact_number ?? '--' }} </div>
                            </div>

                        </div>
                        <div class="grid grid-cols-2">
                            <div class="flex ">
                                <div class="w-1/2">Email : </div>
                                <div class="w-1/2">{{ $lead->email ?? '--' }}</div>
                            </div>

                            <div class="flex ">
                                <div class="w-1/2">Alternative Contact : </div>
                                <div class="w-1/2">{{ $lead->contact_number_2 ?? '--' }}</div>
                            </div>
                            {{-- <div class="flex ">
                                <div class="w-1/2">NIC: </div>
                                <div class="w-1/2">{{ $lead->nic ?? '--' }} </div>
                            </div> --}}
                        </div>


                        <div class="grid grid-cols-2">
                            <div class="flex">
                                <div class="w-1/2">Whatsapp:</div>
                                <div class="w-1/2 flex items-center space-x-2">
                                    <span>{{ $lead->whatsapp ?? '--' }}</span>
                                    @if($lead->whatsapp)
                                        <svg wire:click="openWhatsApp" class="w-5 h-5 text-green-600 cursor-pointer" fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 448 512"><!--! Font Awesome Free 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2022 Fonticons, Inc. -->
                                            <path
                                                d="M224 122.8c-72.7 0-131.8 59.1-131.9 131.8 0 24.9 7 49.2 20.2 70.1l3.1 5-13.3 48.6 49.9-13.1 4.8 2.9c20.2 12 43.4 18.4 67.1 18.4h.1c72.6 0 133.3-59.1 133.3-131.8 0-35.2-15.2-68.3-40.1-93.2-25-25-58-38.7-93.2-38.7zm77.5 188.4c-3.3 9.3-19.1 17.7-26.7 18.8-12.6 1.9-22.4.9-47.5-9.9-39.7-17.2-65.7-57.2-67.7-59.8-2-2.6-16.2-21.5-16.2-41s10.2-29.1 13.9-33.1c3.6-4 7.9-5 10.6-5 2.6 0 5.3 0 7.6.1 2.4.1 5.7-.9 8.9 6.8 3.3 7.9 11.2 27.4 12.2 29.4s1.7 4.3.3 6.9c-7.6 15.2-15.7 14.6-11.6 21.6 15.3 26.3 30.6 35.4 53.9 47.1 4 2 6.3 1.7 8.6-1 2.3-2.6 9.9-11.6 12.5-15.5 2.6-4 5.3-3.3 8.9-2 3.6 1.3 23.1 10.9 27.1 12.9s6.6 3 7.6 4.6c.9 1.9.9 9.9-2.4 19.1zM400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zM223.9 413.2c-26.6 0-52.7-6.7-75.8-19.3L64 416l22.5-82.2c-13.9-24-21.2-51.3-21.2-79.3C65.4 167.1 136.5 96 223.9 96c42.4 0 82.2 16.5 112.2 46.5 29.9 30 47.9 69.8 47.9 112.2 0 87.4-72.7 158.5-160.1 158.5z">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.livewire.on('whatsappOpened', url => {
                window.open(url, '_blank');
            });
        });
    </script>



                            <div class="flex">
                                <div class="w-1/2">Behaviour:</div>
                                <div class="w-1/2">
                                    @if($lead->behaviour === '1')
                                        Good Customer
                                    @elseif($lead->behaviour === '0')
                                        Normal Customer
                                    @elseif($lead->behaviour === '-1')
                                        Bad Customer
                                    @else
                                        --
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="grid grid-cols-2">
                            <div class="flex ">
                                <div class="w-1/2">Created At : </div>
                                <div class="w-1/2">{{ $lead->created_at->format('Y-m-d') }}</div>
                            </div>
                            <div class="flex ">
                                <div class="w-1/2">Intial Queue : </div>
                                <div class="w-1/2">{{ $lead->skill ? $lead->skill->skillname : '--' }}</div>
                            </div>
                        </div>
                        <!-- <hr class="pt-1">
                        <div class="flex justify-end space-x-2 pb-1 w-full">
                            <div wire:click="showWhatsAppModal" class="flex items-center space-x-2 border-2 border-gray-200 p-1 pt-0 cursor-pointer rounded-md">
                                @if($lead->whatsapp || $lead->email || $lead->phone)
                                    <span class="text-blue-600 font-semibold">Notify Customer</span>
                                    <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" viewBox="0 0 24 24"><path d="M20 2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h3v3.767L13.277 18H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zm0 14h-7.277L9 18.233V16H4V4h16v12z"></path><path d="m13.803 9.189-1.399-1.398-3.869 3.864v1.399h1.399zm.327-3.123 1.398 1.399-1.066 1.066-1.399-1.398z"></path></svg>
                                @endif
                            </div>
                        </div> -->

                        <hr class="pb-3">
                        {{-- <div class="grid grid-cols-2">
                            <div class="col-span-2  text-center ">
                                @if ($lead->orders->count() > 0)
                                <div class="font-semibold text-green-600">Return Customer</div>
                                @else
                                <div class="font-semibold text-red-600">New Customer</div>
                                @endif
                            </div>
                        </div>
                        <hr> --}}
                        This Call Reaction:
                        <div>
                            <!-- Toggle Button -->
                            <div class="flex justify-between">
                                <div>
                                    @if(!$isNuisance)
                                        <button wire:click="toggle"
                                            class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors duration-200 {{ $moodStatus ? 'bg-red-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 {{ $moodStatus ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                        <span class="ml-2 text-sm">Unsatisfied</span>
                                    @endif
                                </div>

                                <div>
                                    @if(!$moodStatus)
                                        <button wire:click="nuisance"
                                            class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors duration-200 {{ $isNuisance ? 'bg-amber-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 {{ $isNuisance ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                        <span class="ml-2 text-sm">Nuisance</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Comment Box: Show only if toggle is on AND no flash message -->
                            @if (($moodStatus || $isNuisance) && !session()->has('message'))
                                <div class="mt-3">


                                    <div x-data="{ count: 0 }">
                                        <textarea id="message" wire:model="comment" maxlength="200"
                                            x-on:input="count = $event.target.value.length"
                                            class="w-full p-2 border rounded" rows="3"
                                            placeholder="Please tell us what went wrong..."></textarea>

                                        <p class="text-sm text-gray-500 mt-1 text-right">
                                            <span x-text="count">0</span>/200 characters
                                        </p>
                                    </div>



                                    <div class=" flex justify-end">
                                        <button wire:click="submitReaction"
                                            class="mt-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-red-600 transition">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Flash Message -->
                            @if (session()->has('message'))
                                <div class="mt-2 text-green-600 text-sm">
                                    {{ session('message') }}
                                </div>
                            @endif
                        </div>

                        <hr class="pt-1">
                        <div class="flex justify-end space-x-2 pt-1 w-full">
                            @if(!empty($lead->whatsapp) || !empty($lead->email) || !empty($lead->contact_number))

                            <div wire:click="showWhatsAppModal" class="flex items-center space-x-2 border-2 border-green-600 p-1 cursor-pointer rounded-md bg-green-500 hover:bg-green-600 hover:border-gray-400">
                                <!-- Notify Customer -->
                                    <span class="text-white font-semibold">Send Message</span>
                                    <!-- <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" viewBox="0 0 24 24"><path d="M20 2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h3v3.767L13.277 18H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zm0 14h-7.277L9 18.233V16H4V4h16v12z"></path><path d="m13.803 9.189-1.399-1.398-3.869 3.864v1.399h1.399zm.327-3.123 1.398 1.399-1.066 1.066-1.399-1.398z"></path></svg> -->
                                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"></path>
</svg>
                                    
                            </div>
                            @endif
                        </div>



                    </div>
                    {{-- <div class="bg-white p-4 space-y-2 text-xs">
                        <h1 class=" font-bold">Orders</h1>
                        <hr>




                        <div class="grid grid-cols-2">
                            <div class="flex ">
                                <div class="w-1/2">Orders : </div>
                                <div class="w-1/2">{{ $lead->orders->count() }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/2">Order Total : </div>
                                <div class="w-1/2">
                                    {{ number_format($lead->orders->sum('order_total'), 2) }}

                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2">
                            <div class="flex">
                                <div class="w-1/2">Last Ordered At : </div>
                                <div class="w-1/2">
                                    {{ $lead->orders && $lead->orders->max('created_at') ?
                                    $lead->orders->max('created_at')->format('Y-m-d') : '--' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-1/2">Last Ordered Outlet : </div>
                                <div class="w-1/2">
                                    {{ $lead->lastOrder && $lead->lastOrder->outlet ? $lead->lastOrder->outlet->title :
                                    '--' }}
                                </div>
                            </div>

                        </div>

                        <hr>
                        <div class="grid grid-cols-2">
                            <div class="col-span-2  text-center ">
                                <div class="font-semibold text-green-600">Average Basket Value :
                                    {{ number_format($lead->orders->count() == 0 ? 0 : $lead->orders->sum('order_total')
                                    / $lead->orders->count(), 2) }}
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <details class="bg-white p-4 space-y-2 text-xs rounded-md shadow-sm" @if($ticketInfoOpen) open @endif x-on:toggle="$wire.set('ticketInfoOpen', $event.target.open)">
                        <summary class="font-bold cursor-pointer list-none flex items-center justify-between">
                            <span>Ticket Overview & Callback</span>
                            <span class="text-gray-400 text-xs">{{ $ticketInfoOpen ? 'Click to collapse >>' : 'Click to expand  >>' }}</span>
                        </summary>
                        <hr>




                        <div class="grid grid-cols-2">
                            <div class="flex ">
                                <div class="w-1/2">Tickets : </div>
                                <div class="w-1/2">{{ $lead->ticketsNew->count() }}</div>
                            </div>
                            <div class="flex ">
                                <div class="w-1/2">Opened : </div>
                                <div class="w-1/2">{{ $lead->openedTickets->count() }}</div>
                            </div>

                        </div>
                        <div class="grid grid-cols-2">
                            <div class="flex">
                                <div class="w-1/2">Overdue : </div>
                                <div class="w-1/2">
                                    0

                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-1/2">Last Ticket At : </div>
                                <div class="w-1/2">
                                    {{ $lead->ticketsNew && $lead->ticketsNew->max('created_at') ? $lead->ticketsNew->max('created_at')->format('Y-m-d') : '--' }}
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="grid grid-cols-2">

                        </div>

                        <h1 class="font-bold mb-2">Call later</h1>

                        <div class="mb-4">
                            <span>Set time:</span>
                            <button wire:click="toggleCallbackCustomer"
                                class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors duration-200 {{ $callBack ? 'bg-blue-500' : 'bg-gray-300' }}">
                                <span
                                    class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 {{ $callBack ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>

                        @if($callBack)
                            <div class="space-y-4">
                                <div class="flex justify-between  items-end gap-4">
                                    <div class="w-1/2">
                                        <label for="callback_date" class="block text-sm font-medium">Select Date</label>
                                        <input type="date" id="callback_date" wire:model="callbackDate"
                                            class="mt-1 block w-full border rounded p-2">
                                    </div>

                                    <div class="w-1/2">
                                        <label for="callback_time" class="block text-sm font-medium">Select Time</label>
                                        <input type="time" id="callback_time" wire:model="callbackTime"
                                            class="mt-1 block w-full border rounded p-2">
                                    </div>
                                </div>

                                <div>
                                    <label for="callback_comment" class="block text-sm font-medium">Comment</label>
                                    <textarea id="callback_comment" wire:model="callbackComment"
                                        class="mt-1 block w-full border rounded p-2" rows="3"></textarea>
                                </div>

                                <div>
                                    <button wire:click="saveCallback"
                                        class="px-4 py-2 bg-blue-300 rounded hover:bg-blue-400">
                                        Save Callback
                                    </button>

                                </div>
                            </div>

                        @endif
                        @if (session()->has('messagedialog'))
                            <div class="mt-2 text-green-600 text-sm">
                                {{ session('messagedialog') }}
                            </div>
                        @endif
                    </details>
                    @if($boundType == 'dialer')
                        <div class="bg-white p-4 space-y-3 text-xs">

                            @if(collect($feedContacts)->isNotEmpty() || collect($surveyContacts)->isNotEmpty())

                                @php
                                    $loadedContactIds = collect($feedContacts)->pluck('id')
                                        ->merge(collect($surveyContacts)->pluck('feed_contact_id'))
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp

                                <div class="flex justify-between">
                                    <h2 class="font-bold text-sm mb-2">All Work Orders</h2>
                                    <div class="pr-4 space-x-4">
                                        {{-- <div class="relative group">
                                            <svg wire:click="makeCall('{{ $lead->contact_number }}')"
                                                class="w-6 h-6 cursor-pointer text-green-400" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                <path fill-rule="evenodd"
                                                    d="M15 3.75a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.56l-4.72 4.72a.75.75 0 1 1-1.06-1.06l4.72-4.72h-2.69a.75.75 0 0 1-.75-.75Z"
                                                    clip-rule="evenodd"></path>
                                                <path fill-rule="evenodd"
                                                    d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z"
                                                    clip-rule="evenodd"></path>
                                            </svg>

                                            <span
                                                class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2
                                                                     bg-gray-800 text-white text-xs rounded-lg px-2 py-1
                                                                     opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                                                Make a call
                                            </span>
                                        </div> --}}
                                        {{-- <button type="button" wire:click="makeCall('{{ $lead->contact_number }}')"
                                            class="w-24 bg-green-300 font-bold hover:bg-green-400 p-2 rounded-md shadow-md ">
                                            Make a call</button> --}}
                                            
                                        {{-- <button type="button"
                                            wire:click="$emit('openSkipContactModal', '{{ $lead->contact_number }}','{{ $lead->contact_number_2 }}', '{{ $feed_id }}', '{{ $service_type }}', '{{ $loadedContactIds->implode(',') }}')"
                                            class="w-24 bg-orange-300 font-bold hover:bg-orange-400 p-2 rounded-md shadow-md">
                                            Skip
                                        </button> --}}

                                        {{-- @if ($service_type == 'satisfaction')
                                        <button type="button"
                                            wire:click="$emit('openUpdateContactModal', '{{ $lead->contact_number }}','{{ $lead->contact_number_2 }}', '{{ $feed_id }}', '{{ $service_type }}')"
                                            class="w-32 bg-orange-300 font-bold hover:bg-orange-400 p-2 rounded-md shadow-md">
                                            Change Request
                                        </button>
                                        @endif --}}

                                    </div>

                                </div>
                                <hr>

                                <div class="bg-white p-4 rounded-lg shadow-md space-y-3">
                                    <h2 class="text-sm font-semibold text-gray-700">Select Contact</h2>

                                    <div class="space-y-2  text-lg">
                                        {{-- <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="radio" wire:model="selectedContact" value="{{ $lead->contact_number }}"
                                                class="text-green-500">
                                            <span class="text-gray-700">{{ $lead->contact_number }}</span>
                                        </label>

                                        @if($phone2 && strlen($phone2) > 8)
                                            <label class="flex items-center space-x-2 cursor-pointer">
                                                <input type="radio" wire:model="selectedContact" value="{{ $phone2 }}"
                                                    class="text-green-500">
                                                <span class="text-gray-700">{{ $phone2 }}</span>
                                            </label>
                                        @endif --}}
                                        @foreach($phone_numbers as $phone)
                                            @if(strlen($phone) > 8)
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="radio" wire:model="selectedContact" value="{{ $phone }}"
                                                        class="text-green-500">
                                                    <span class="text-gray-700">{{ $phone }}</span>
                                                </label>
                                            @endif
                                        @endforeach

                                    </div>

                                    <div>
                                        {{-- <button type="button" wire:click="makeCall('{{ $selectedContact }}')"
                                            class="w-full bg-green-500 text-white font-bold text-lg hover:bg-green-600 px-4 py-2 rounded-md shadow">
                                            Call
                                        </button> --}}
                                        <button type="button"
        x-data="{ disabled: false }"
        x-bind:disabled="disabled"
        x-on:click="disabled = true; setTimeout(() => disabled = false, 5000)"
        wire:click="makeCall('{{ $selectedContact }}')"
        x-bind:class="disabled ? 'opacity-50 cursor-not-allowed' : ''"
        class="w-full bg-green-500 text-white font-bold text-lg hover:bg-green-600 px-4 py-2 rounded-md shadow">
    Call
</button>
                                    </div>
                                </div>

                               @if ($service_type == 'satisfaction' && $surveyContacts && $surveyContacts->count() > 0)

    @foreach ($surveyContacts as $ticket)
    @php
        $ticket = (object) $ticket;
        $contactData = json_decode($ticket->more_data ?? '{}', true);

        $campaignId = \App\Models\Campaign::where('name', $campaign)->value('id');

        // Status classifications
        $isAnswered = $ticket->status == 'Rated'; 
        // $isNotAnswered = $ticket->status == 'Skip' || $ticket->status == 'ReOpened' || $ticket->status == 'Canceled';
        $isNotAnswered = $ticket->status ? $ticket->status: false;  
        $isNew = $ticket->status == 'Closed'; 
        $notAnswered = $ticket->status == 'Skip' || $ticket->status == 'ReOpened' || $ticket->status == 'Canceled';

        // Check if Not Answered was submitted today
        $lastUpdated = \Carbon\Carbon::parse($ticket->updated_at); 
        // $hideNotAnsweredButtons = $notAnswered && $lastUpdated->isToday();
        // $hideNotAnsweredButtons = $notAnswered && $lastUpdated->greaterThan(now()->startOfDay());
        $hideNotAnsweredButtons = ($ticket->status == 'ReOpened') || ($ticket->status == 'Canceled') || ($ticket->status == 'Skip' && $lastUpdated->greaterThan(\Carbon\Carbon::now()->subMinutes(1)));
        $notAnsweredCount = $satisfactionNotAnsweredCounts[trim($ticket->work_order_no)] ?? strlen((string) $feedContactIdStatus);
    @endphp

    <details class="border rounded-lg shadow-sm p-2
        @if($isAnswered)
            bg-green-50 border-green-300
        @elseif($isNotAnswered == 'Skip' || $isNotAnswered == 'ReOpened' || $isNotAnswered == 'Canceled' || $isNotAnswered == 'Change Request')
            bg-yellow-50 border-yellow-300
        @else
            bg-gray-50 border-gray-200
        @endif
    ">

        {{-- COLLAPSIBLE HEADER WITH WORK ORDER NO --}}
        <summary class="flex justify-between items-center cursor-pointer px-2 py-2 text-lg font-semibold rounded-t-lg
            @if($isAnswered)
                text-green-700 hover:bg-green-100
            @elseif($isNotAnswered == 'Skip' || $isNotAnswered == 'ReOpened' || $isNotAnswered == 'Canceled' || $isNotAnswered == 'Change Request')
                text-yellow-700 hover:bg-yellow-100
            @else
                text-gray-700 hover:bg-gray-100
            @endif
        ">
            <span>{{ $ticket->work_order_no }}

                @if($isAnswered)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                        Submitted
                    </span>
                @elseif($isNotAnswered == 'Skip')
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-yellow-600 rounded-full">
                        Not Answered
                    </span>
                    <span class="ml-2 px-2 py-0.5 text-xs font-bold text-white bg-red-700 rounded-full">
                        {{ $notAnsweredCount }}
                    </span>
                @elseif($isNotAnswered == 'ReOpened')
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-yellow-600 rounded-full">
                        ReoPened
                    </span>
                @elseif($isNotAnswered == 'Canceled')
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-yellow-600 rounded-full">
                        Canceled
                    </span>
                @elseif($isNotAnswered == 'Change Request')
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-yellow-600 rounded-full">
                        Change Request
                    </span>
                @endif

            </span>
        </summary>

        <div class="text-sm mt-4">

            {{-- Product Details --}}
            <div class="border p-3 rounded-lg shadow-md">
                <h1 class="p-1 pl-0 font-bold text-lg">Product Details</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Category:</label>
                            <span>{{ $ticket->category }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Product:</label>
                            <span>{{ $ticket->product }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Model:</label>
                            <span>{{ $ticket->model }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Service Center:</label>
                            <span>{{ $ticket->service_center }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Warranty Status:</label>
                            <span>{{ $ticket->warranty_status }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Sold Date:</label>
                            <span>{{ $ticket->sold_date }}</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Customer Details --}}
            <div class="border p-3 rounded-lg shadow-md mt-4">
                <h1 class="p-1 pl-0 font-bold text-lg">Customer Details</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Customer Name:</label>
                            <span>{{ $ticket->customer_name }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Customer Address:</label>
                            <span>{{ $ticket->customer_address }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Customer Contact 01:</label>
                            <span>{{ $ticket->customer_contact_01 }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Customer Contact 02:</label>
                            <span>{{ $ticket->customer_contact_02 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technician Details --}}
            {{-- <div class="border p-2 rounded-lg shadow-md mt-4">
                <h1 class="p-1 pl-0 font-bold text-lg">Technician Details</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Technician Name:</label>
                            <span>{{ $ticket->technician_name }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Technician Contact:</label>
                            <span>{{ $ticket->technician_contact }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Supervisor Name:</label>
                            <span>{{ $ticket->supervisor_name }}</span>
                        </div>
                        <div class="py-2 flex gap-2">
                            <label class="font-bold">Supervisor Contact:</label>
                            <span>{{ $ticket->supervisor_contact }}</span>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- More Data --}}
            @if(!empty($contactData))
                <div class="border p-3 rounded-lg shadow-md mt-4">
                    <h1 class="p-1 pl-0 font-bold text-lg">More Data</h1>
                    <ul class="grid grid-cols-2 gap-x-4 gap-y-1 text-base">
                        @foreach($contactData as $key => $value)
                            @if(!empty($key))
                                @if(is_array($value))
                                    <li class="col-span-2">
                                        <span class="font-medium text-base">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                        </span>
                                        <div class="mt-2 ml-4 space-y-2">
                                            @foreach($value as $item)
                                                @if(is_array($item))
                                                    <div class="border rounded p-2 bg-gray-50">
                                                        <ul class="grid grid-cols-2 gap-x-4 gap-y-1">
                                                            @foreach($item as $itemKey => $itemValue)
                                                                @if(!is_array($itemValue))
                                                                    <li>
                                                                        <span class="font-medium">
                                                                            {{ ucfirst(str_replace('_', ' ', $itemKey)) }}:
                                                                        </span>
                                                                        {{ $itemValue }}
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="ml-2">{{ $item }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </li>
                                @else
                                    <li>
                                        <span class="font-bold text-base">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                        </span>
                                        {{ $value }}
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Skipped Reasons --}}
            @if($isNotAnswered == 'Skip')
                <div class="border p-3 rounded-lg shadow-md mt-4">
                    <h1 class="p-1 pl-0 font-bold text-lg">Skipped Reasons</h1>
                    <ul class="list-disc ml-4">
                        @foreach(explode(',', $ticket->skipped_reasons) as $reason)
                            <li>{{ trim($reason) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Buttons --}}
            @if(!$isAnswered && !$hideNotAnsweredButtons)
            <div class="flex pt-4 space-x-3 pb-2">
                <div class="group relative inline-flex">
                    <a href="#"
                       wire:click.prevent="$emitTo('cx-tickets.survey.satisfaction-rating-panel','showCxTicketRating', {{ $ticket->feed_contact_id }}, false, {{ $campaignId }})"
                       class="p-2 bg-teal-500 hover:bg-teal-600 text-black rounded-md">
                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"> <path d="M313.4 32.9c26 5.2 42.9 30.5 37.7 56.5l-2.3 11.4c-5.3 26.7-15.1 52.1-28.8 75.2l144 0c26.5 0 48 21.5 48 48c0 18.5-10.5 34.6-25.9 42.6C497 275.4 504 288.9 504 304c0 23.4-16.8 42.9-38.9 47.1c4.4 7.3 6.9 15.8 6.9 24.9c0 21.3-13.9 39.4-33.1 45.6c.7 3.3 1.1 6.8 1.1 10.4c0 26.5-21.5 48-48 48l-97.5 0c-19 0-37.5-5.6-53.3-16.1l-38.5-25.7C176 420.4 160 390.4 160 358.3l0-38.3 0-48 0-24.9c0-29.2 13.3-56.7 36-75l7.4-5.9c26.5-21.2 44.6-51 51.2-84.2l2.3-11.4c5.2-26 30.5-42.9 56.5-37.7zM32 192l64 0c17.7 0 32 14.3 32 32l0 224c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32-14.3-32-32L0 224c0-17.7 14.3-32 32-32z"/> </svg>
                    </a>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Rate</span>
                </div>

                {{-- CANCEL --}}
                <div class="group relative inline-flex">
                    <a href="#"
                       wire:click.prevent="$emitTo('cx-tickets.survey.satisfaction-rating-panel','showCxTicketRating', {{ $ticket->feed_contact_id }}, true, {{ $campaignId }})"
                       class="p-2 bg-red-400 hover:bg-red-500 text-black rounded-md">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"> <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/> </svg>
                    </a>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">Cancel</span>
                </div>

                {{-- REOPEN --}}
                {{-- <div class="group relative inline-flex">
                    <a href="#"
                       wire:click.prevent="$emitTo('cx-tickets.survey.satisfaction-reopen-panel','showReOpenPanel', {{ $ticket->feed_contact_id }}, 'reopen', {{ $campaignId }})"
                       class="p-2 bg-orange-400 hover:bg-orange-500 text-black rounded-md">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"> <path d="M5 4a2 2 0 0 0-2 2v6H0l4 4-4-4H5V6h7l2-2H5zm10 4h-3l4-4 4 4h-3v6a2 2 0 0 1-2 2H6l2-2h7V8z"/> </svg>
                    </a>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition">ReOpen</span>
                </div> --}}

                {{-- Not Answered --}}
                
                    <button type="button"
                        wire:click="$emit('openCallStatusModal', '{{ $ticket->work_order_no }}', 'not_answered', {{$surveyContacts->count()}}, {{ $ticket->feed_contact_id }}, {{ $campaignId }})"
                        class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 transition-colors duration-200">
                        Not Answered
                    </button>
                
            </div>
            @endif

        </div>
    </details>
@endforeach

    @elseif($service_type == 'satisfaction-mini' && $surveyContacts && $surveyContacts->count() > 0)

    @php
        $miniFirst = (object) collect($surveyContacts)->first();
    @endphp

    <div class="bg-white p-4 rounded-lg shadow-md space-y-3">

        @if($miniFirst)
            <div class="flex flex-wrap justify-between gap-x-8 gap-y-1 text-base">
                <div class="flex gap-2">
                    <label class="font-bold">Customer Name:</label>
                    <span>{{ $miniFirst->customer_name ?? '--' }}</span>
                </div>
                <div class="flex gap-2">
                    <label class="font-bold">Service Center:</label>
                    <span>{{ $miniFirst->service_center ?? '--' }}</span>
                </div>
            </div>
        @endif

        <hr>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse min-w-max">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2 border font-semibold">Priority</th>
                        <th class="p-2 border font-semibold">Call Status</th>
                        <th class="p-2 border font-semibold">Rate</th>
                        <th class="p-2 border font-semibold">Reasons</th>
                        <th class="p-2 border font-semibold">Comment</th>
                        <th class="p-2 border font-semibold">Real Completion Date</th>
                        {{-- <th class="p-2 border font-semibold">Sold Date</th> --}}
                        <th class="p-2 border font-semibold">Product</th>
                        <th class="p-2 border font-semibold">Product Description</th>
                        <th class="p-2 border font-semibold">Model</th>
                        <th class="p-2 border font-semibold">Model Description</th>
                        <th class="p-2 border font-semibold">Work Type</th>
                        <th class="p-2 border font-semibold">Warranty Status</th>
                        {{-- <th class="p-2 border font-semibold">More Data</th> --}}
                        <th class="p-2 border font-semibold">Customer Name</th>
                        <th class="p-2 border font-semibold">Customer Address</th> 
                        {{-- <th class="p-2 border font-semibold">Contact 01</th>
                        <th class="p-2 border font-semibold">Contact 02</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveyContacts as $ticket)
                        @php
                            $ticket = (object) $ticket;
                            $id = $ticket->feed_contact_id;
                            $rating = $miniRatings[$id] ?? null;
                            $callStatus = $miniCallStatus[$id] ?? null;

                            if (is_numeric($rating) && (int) $rating >= 1 && (int) $rating <= 5) {
                                $reasonOptions = array_merge($satisfactionReasons, $dissatisfactionReasons);
                            } elseif ($rating === 'cancel' && $callStatus === 'answered') {
                                $reasonOptions = $cancelAnsweredReasons;
                            } elseif ($rating === 'cancel' && $callStatus === 'not_answered') {
                                $reasonOptions = $cancelNotAnsweredReasons;
                            } else {
                                $reasonOptions = [];
                            }

                            $moreData = json_decode($ticket->more_data ?? '{}', true) ?: [];

                            $isSubmitted = in_array($ticket->status, ['Rated', 'Canceled', 'Change Request'], true);
                            $isNotAnswered = $ticket->status == 'Skip';
                        @endphp
                        <tr class="align-top border-b hover:bg-gray-50">
                            <td class="p-2 border font-semibold">
                                {{ $ticket->work_order_no ?? '--' }}
                                @if($isSubmitted)
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                                            Submitted
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="p-2 border">
                                <div x-data="{ open: false }" class="relative">
                                    <button type="button" @click="open = !open" @disabled($isSubmitted)
                                        class="border p-1 rounded w-full text-xs flex items-center justify-center {{ $isSubmitted ? 'bg-gray-100' : 'bg-white' }}">
                                        @if(($miniCallStatus[$id] ?? '') === 'answered')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500">
                                                {{-- <svg class="w-5 h-5 text-white" style="transform: rotate(180deg);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9c-1.6 0-3.15.25-4.6.72v3.1c0 .39-.23.74-.56.9-.98.49-1.87 1.12-2.66 1.85-.18.18-.43.28-.7.28-.28 0-.53-.11-.71-.29L.29 13.08c-.18-.17-.29-.42-.29-.7 0-.28.11-.53.29-.71C3.34 8.78 7.46 7 12 7s8.66 1.78 11.71 4.67c.18.18.29.43.29.71 0 .28-.11.53-.29.71l-2.48 2.48c-.18.18-.43.29-.71.29-.27 0-.52-.11-.7-.28-.79-.74-1.69-1.36-2.67-1.85-.33-.16-.56-.5-.56-.9v-3.1C15.15 9.25 13.6 9 12 9z"></path></svg> --}}
                                                <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"></path><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"></path></svg>
                                            </span>
                                        @elseif(($miniCallStatus[$id] ?? '') === 'not_answered')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-500">
                                                <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9c-1.6 0-3.15.25-4.6.72v3.1c0 .39-.23.74-.56.9-.98.49-1.87 1.12-2.66 1.85-.18.18-.43.28-.7.28-.28 0-.53-.11-.71-.29L.29 13.08c-.18-.17-.29-.42-.29-.7 0-.28.11-.53.29-.71C3.34 8.78 7.46 7 12 7s8.66 1.78 11.71 4.67c.18.18.29.43.29.71 0 .28-.11.53-.29.71l-2.48 2.48c-.18.18-.43.29-.71.29-.27 0-.52-.11-.7-.28-.79-.74-1.69-1.36-2.67-1.85-.33-.16-.56-.5-.56-.9v-3.1C15.15 9.25 13.6 9 12 9z"></path></svg>
                                            </span>
                                        @else
                                            <span class="text-gray-400">-- Select --</span>
                                        @endif
                                    </button>
                                    <div x-show="open" x-cloak x-on:click.away="open = false"
                                        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded shadow-xl text-xs">
                                        <button type="button" wire:click="$set('miniCallStatus.{{ $id }}', 'answered')" @click="open = false"
                                            class="w-full px-2 py-1 text-left hover:bg-gray-50 flex items-center justify-center gap-1">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500">
                                                <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"></path><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"></path></svg>
                                            </span>
                                        </button>
                                        <button type="button" wire:click="$set('miniCallStatus.{{ $id }}', 'not_answered')" @click="open = false"
                                            class="w-full px-2 py-1 text-left hover:bg-gray-50 flex items-center justify-center gap-1">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500">
                                                <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9c-1.6 0-3.15.25-4.6.72v3.1c0 .39-.23.74-.56.9-.98.49-1.87 1.12-2.66 1.85-.18.18-.43.28-.7.28-.28 0-.53-.11-.71-.29L.29 13.08c-.18-.17-.29-.42-.29-.7 0-.28.11-.53.29-.71C3.34 8.78 7.46 7 12 7s8.66 1.78 11.71 4.67c.18.18.29.43.29.71 0 .28-.11.53-.29.71l-2.48 2.48c-.18.18-.43.29-.71.29-.27 0-.52-.11-.7-.28-.79-.74-1.69-1.36-2.67-1.85-.33-.16-.56-.5-.56-.9v-3.1C15.15 9.25 13.6 9 12 9z"></path></svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="p-2 border">
                                <select wire:model="miniRatings.{{ $id }}" @disabled($isSubmitted)
                                    class="border p-1 rounded w-full text-xs text-center {{ $isSubmitted ? 'bg-gray-100' : '' }}">
                                    <option value="">-- Select --</option>
                                    <option value="1" class="text-red-700 bg-red-50">1</option>
    <option value="2" class="text-orange-700 bg-orange-50">2</option>
    <option value="3" class="text-yellow-700 bg-yellow-50">3</option>
    <option value="4" class="text-lime-700 bg-lime-50">4</option>
    <option value="5" class="text-green-700 bg-green-50">5</option>
    <option value="cancel" class="text-gray-700 bg-gray-50">Cancel</option>
    <option value="change_request" class="text-blue-700 bg-blue-50">Change Request</option>
                                </select>
                            </td>

                            <td class="p-2 border">
                                @if($reasonOptions)
                                    <div x-data="{ open: false }" class="relative inline-block">
                                        <button type="button" @click="open = !open" @disabled($isSubmitted)
                                            class="w-44 border p-1 rounded text-xs flex items-center justify-between {{ $isSubmitted ? 'bg-gray-100' : 'bg-white' }}">
                                            <span class="truncate">
                                                {{ count((array) ($miniSelectedReasons[$id] ?? [])) }} selected
                                            </span>
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-cloak x-on:click.away="open = false"
                                            class="absolute z-50 mt-1 w-64 max-h-48 overflow-y-auto border border-gray-200 bg-white rounded shadow-xl p-1">
                                            @foreach($reasonOptions as $reason)
                                                <label class="flex items-center gap-1 p-1 hover:bg-gray-50 cursor-pointer text-xs whitespace-nowrap">
                                                    <input type="checkbox" value="{{ $reason }}"
                                                        @checked(in_array($reason, (array) ($miniSelectedReasons[$id] ?? [])))
                                                        wire:click.prevent="toggleMiniReason({{ $id }}, '{{ addslashes($reason) }}')"
                                                        class="rounded">
                                                    {{ $reason }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">No reasons</span>
                                @endif
                            </td>

                            <td class="p-2 border">
                                <textarea wire:model="miniComments.{{ $id }}" rows="2"
                                    @disabled($isSubmitted)
                                    class="w-40 border p-1 rounded text-xs {{ $isSubmitted ? 'bg-gray-100' : '' }}"
                                    placeholder="Add comment"></textarea>
                            </td>

                            {{-- <td class="p-2 border">{{ $ticket->sold_date ?? '--' }}</td> --}}
                            
                            @php
                                $normalizeKey = function ($k) { return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $k)); };
                                $targetFields = [
                                    'realcompletiondate' => 'Real Completion Date',
                                    'productdescription' => 'Product Description',
                                    'modeldescription' => 'Model Description',
                                    'worktype' => 'Work Type',
                                ];
                                $matched = [];
                                $matchedKeys = [];
                                foreach ($moreData as $k => $v) {
                                    $nk = $normalizeKey($k);
                                    if (isset($targetFields[$nk])) {
                                        $matched[$nk] = $v;
                                        $matchedKeys[] = $k;
                                    }
                                }
                                $remainingData = array_diff_key($moreData, array_flip($matchedKeys));
                            @endphp

                            <td class="p-2 border text-xs">{{ $matched['realcompletiondate'] ?? '--' }}</td>
                            <td class="p-2 border">{{ $ticket->product ?? '--' }}</td>
                            <td class="p-2 border text-xs">{{ $matched['productdescription'] ?? '--' }}</td>
                            <td class="p-2 border">{{ $ticket->model ?? '--' }}</td>
                            <td class="p-2 border text-xs">{{ $matched['modeldescription'] ?? '--' }}</td>
                            <td class="p-2 border text-xs">{{ $matched['worktype'] ?? '--' }}</td>
                            <td class="p-2 border">{{ $ticket->warranty_status ?? '--' }}</td>


                            {{-- <td class="p-2 border">
                                @if($remainingData)
                                    <div class="text-xs space-y-0.5">
                                        @foreach($remainingData as $key => $value)
                                            @if($value === null || $value === '' || $key === '')
                                                @continue
                                            @endif
                                            <div class="whitespace-nowrap">
                                                <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                {{ is_array($value) ? json_encode($value) : $value }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">--</span>
                                @endif
                            </td> --}}

                            <td class="p-2 border">{{ $ticket->customer_name ?? '--' }}</td>
                            <td class="p-2 border">{{ $ticket->customer_address ?? '--' }}</td>
                            {{-- <td class="p-2 border">{{ $ticket->customer_contact_01 ?? '--' }}</td>
                            <td class="p-2 border">{{ $ticket->customer_contact_02 ?? '--' }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-start pt-2">
            <button type="button" wire:click="submitSatisfactionMini" wire:loading.attr="disabled"
                class="bg-teal-500 text-white font-bold py-2 px-6 rounded-md hover:bg-teal-600 transition-colors duration-200">
                Submit
            </button>
        </div>
    </div>

    @elseif($service_type == 'follow-up' && $feedContacts && $feedContacts->count() > 0)

                                @foreach($feedContacts as $contact)
                                                        @php
                                                            $contactData = json_decode($contact->data, true);

                                                            // Status classifications
                                                            $isAnswered = $contact->status == 1;
                                                            // $isNotAnswered = in_array($contact->status, [2, 22, 222]); 
                                                            $isNotAnswered = str_starts_with((string)$contact->status, '2');
                                                            $isNew = is_null($contact->status);

                                                            // Check if Not Answered was submitted today
                                                            $lastUpdated = \Carbon\Carbon::parse($contact->updated_at);
                                                            $hideNotAnsweredButtons = $isNotAnswered && $lastUpdated->greaterThan(\Carbon\Carbon::now()->subMinutes(1));
                                                            $notAnsweredCount = strlen((string) $contact->status);
                                                        @endphp

                                                        <details class="border rounded-lg shadow-sm
                                        @if($isAnswered)
                                            bg-green-50 border-green-300
                                        @elseif($isNotAnswered)
                                            bg-yellow-50 border-yellow-300
                                        @else
                                            bg-gray-50 border-gray-200
                                        @endif
                                    " open>

                                                            <summary class="flex items-center justify-between cursor-pointer px-4 py-2 text-lg font-semibold rounded-t-lg
                                            @if($isAnswered)
                                                text-green-700 hover:bg-green-100
                                            @elseif($isNotAnswered)
                                                text-yellow-700 hover:bg-yellow-100
                                            @else
                                                text-gray-700 hover:bg-gray-100
                                            @endif
                                        ">
                                                                <span>
                                                                    {{ $contact->priority_field ?? 'No Title' }}

                                                                    @if($isAnswered)
                                                                        <span
                                                                            class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                                                                            Submitted
                                                                        </span>
                                                                    @elseif($isNotAnswered)
                                                                        
                                                                        <span
                                                                            class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-yellow-600 rounded-full">
                                                                            Not Answered
                                                                        </span>
                                                                        <span
                                                                            class="ml-2 px-2 py-0.5 text-xs font-bold text-white bg-red-700 rounded-full">
                                                                             {{ $notAnsweredCount }}
                                                                        </span>
                                                                    @endif
                                                                </span>
                                                            </summary>

                                                            <div class="px-4 py-3 border-t text-xs text-gray-600">
                                                                <ul class="grid grid-cols-2 gap-x-4 gap-y-1 text-base">
                                                                    @foreach($contactData as $key => $value)
                                                                        @if(!empty($key))
                                                                            @if(is_array($value))
                                                                                <li class="col-span-2">
                                                                                    <span class="font-medium text-base">
                                                                                        {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                                                    </span>
                                                                                    <div class="mt-2 ml-4 space-y-2">
                                                                                        @foreach($value as $item)
                                                                                            @if(is_array($item))
                                                                                                <div class="border rounded p-2 bg-gray-50">
                                                                                                    <ul class="grid grid-cols-2 gap-x-4 gap-y-1">
                                                                                                        @foreach($item as $itemKey => $itemValue)
                                                                                                            @if(!is_array($itemValue))
                                                                                                                <li>
                                                                                                                    <span class="font-medium">
                                                                                                                        {{ ucfirst(str_replace('_', ' ', $itemKey)) }}:
                                                                                                                    </span>
                                                                                                                    {{ $itemValue }}
                                                                                                                </li>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </ul>
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="ml-2">{{ $item }}</div>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                </li>
                                                                            @else
                                                                                <li>
                                                                                    <span class="font-medium text-base">
                                                                                        {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                                                    </span>
                                                                                    {{ $value }}
                                                                                </li>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>

                                                            {{-- Action buttons --}}
                                                            @if(!$isAnswered && !$hideNotAnsweredButtons)
                                                                <div class="px-4 py-3 flex justify-between gap-2 border-t mt-4">
                                                                    <button type="button"
                                                                        wire:click="$emit('openCallStatusModal', '{{ $contact->id }}', 'answered', '{{ $feedContacts->count() }}')"
                                                                        class="bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600 transition-colors duration-200">
                                                                        Answered
                                                                    </button>
                                                                    <button type="button"
                                                                        wire:click="$emit('openCallStatusModal', '{{ $contact->id }}', 'not_answered', '{{ $feedContacts->count() }}')"
                                                                        class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 transition-colors duration-200">
                                                                        Not Answered
                                                                    </button>
                                                                </div>
                                                            @endif

                                                        </details>
                                @endforeach
                                @else
                                <p class="text-gray-500 text-sm">No work order found.</p>
                            @endif



                            @else
                                <p class="text-gray-500 text-sm">No work order found.</p>
                            @endif

                        </div>
                    @endif


                </div>
                @livewire('leads.partials.activity-log', ['lead' => $lead])
            </div>



        </div>
    </div>





    <x-modal.card blur wire:model="whatsappModal" title="Send Message to Customer">
        <div class="space-y-6">
            <div class="grid grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                <div class="flex flex-col items-center space-y-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 {{ empty($lead->whatsapp) ? 'opacity-50' : '' }}">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.7 17.8 69.4 27.3 106.2 27.3h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157.3zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.1-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.4-11.3 2.5-2.4 5.5-6.5 8.3-9.8 2.8-3.3 3.7-5.5 5.5-9.2 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.3 5.7 23.7 9.1 31.7 11.7 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                        </svg>
                    </div>
                    <!-- <span class="text-xs font-semibold text-gray-700">WhatsApp</span> -->
                    <span class="text-xs font-semibold {{ empty($lead->whatsapp) ? 'text-gray-400' : 'text-gray-700' }}">{{ $lead->whatsapp ?? 'Not Available' }}</span>
                    <x-toggle lg wire:model="notifyWhatsApp" :disabled="empty($lead->whatsapp)" />
                </div>
                <div class="flex flex-col items-center space-y-2 border-x border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 {{ empty($lead->email) ? 'opacity-50' : '' }}">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <!-- <span class="text-xs font-semibold text-gray-700">Email</span> -->
                    <span class="text-xs font-semibold {{ empty($lead->email) ? 'text-gray-400' : 'text-gray-700' }}">{{ $lead->email ?? 'Not Available' }}</span>
                    <x-toggle lg wire:model="notifyEmail" :disabled="empty($lead->email)" />
                </div>
                <div class="flex flex-col items-center space-y-2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 {{ empty($lead->contact_number) ? 'opacity-50' : '' }}">
                        <!-- <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg> -->
                        <svg class="w-6 h-6 text-orange-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2024 Fonticons, Inc. --><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3c0 0 0 0 0 0c0 0 0 0 0 0s0 0 0 0s0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM96 212.8c0-20.3 16.5-36.8 36.8-36.8l19.2 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-19.2 0c-2.7 0-4.8 2.2-4.8 4.8c0 1.6 .8 3.1 2.2 4l29.4 19.6c10.3 6.8 16.4 18.3 16.4 30.7c0 20.3-16.5 36.8-36.8 36.8L112 304c-8.8 0-16-7.2-16-16s7.2-16 16-16l27.2 0c2.7 0 4.8-2.2 4.8-4.8c0-1.6-.8-3.1-2.2-4l-29.4-19.6C102.2 236.7 96 225.2 96 212.8zM372.8 176l19.2 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-19.2 0c-2.7 0-4.8 2.2-4.8 4.8c0 1.6 .8 3.1 2.2 4l29.4 19.6c10.2 6.8 16.4 18.3 16.4 30.7c0 20.3-16.5 36.8-36.8 36.8L352 304c-8.8 0-16-7.2-16-16s7.2-16 16-16l27.2 0c2.7 0 4.8-2.2 4.8-4.8c0-1.6-.8-3.1-2.2-4l-29.4-19.6c-10.2-6.8-16.4-18.3-16.4-30.7c0-20.3 16.5-36.8 36.8-36.8zm-152 6.4L256 229.3l35.2-46.9c4.1-5.5 11.3-7.8 17.9-5.6s10.9 8.3 10.9 15.2l0 96c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48-19.2 25.6c-3 4-7.8 6.4-12.8 6.4s-9.8-2.4-12.8-6.4L224 240l0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-96c0-6.9 4.4-13 10.9-15.2s13.7 .1 17.9 5.6z"></path></svg>
                    </div>
                    <!-- <span class="text-xs font-semibold text-gray-700">Mobile SMS</span> -->
                    <span class="text-xs font-semibold {{ empty($lead->contact_number) ? 'text-gray-400' : 'text-gray-700' }}">{{ $lead->contact_number ?? 'Not Available' }}</span>
                    <x-toggle lg wire:model="notifyPhone" :disabled="empty($lead->contact_number)" />
                </div>
            </div>

            <div class="space-y-4">
                <x-textarea label="Message Content" wire:model="whatsappMessage" placeholder="Type your message here..." rows="4" />
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Attachment (Optional)</label>
                    <div class="flex items-center space-x-3">
                        <input type="file" wire:model="attachment" wire:key="attachment-input-{{ $attachmentResetKey }}" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        <div wire:loading wire:target="attachment" class="text-xs text-blue-600 animate-pulse">Uploading...</div>
                    </div>
                    @if($attachment)
                        <div class="flex items-center space-x-2 mt-2 p-2 bg-gray-100 rounded-md">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span class="text-xs text-gray-600 truncate max-w-[200px]">{{ $attachment->getClientOriginalName() }}</span>
                            <button wire:click="$set('attachment', null)" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                
                <p class="text-[12px] text-gray-400">The message will be sent to the active channels selected above. If an attachment is selected, SMS will be disabled.</p>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <div class="text-xs text-gray-500">
                    Name: <span class="font-medium">{{ $lead->full_name }}</span>
                </div>
                <div class="flex flex-col items-end gap-y-1">
                    <div class="flex gap-x-3">
                        <x-button flat label="Cancel" x-on:click="close" />
                        <x-button secondary label="Send" wire:click="sendWhatsAppMessage" spinner="sendWhatsAppMessage" />
                    </div>
                    @if($channelError)
                        <span class="text-[10px] text-red-500 font-medium">{{ $channelError }}</span>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
@if(config('app.auto_open_create_ticket_modal') && filled(trim((string) ($lead->full_name ?? ''))))
    <script>
        (function() {
            var emitOpenTicketModal = function(attempt) {
                var currentAttempt = attempt || 0;

                if (window.Livewire && typeof window.Livewire.emitTo === 'function') {
                    window.Livewire.emitTo('tickets.create', 'showCreatingTicket');
                    return;
                }

                if (window.livewire && typeof window.livewire.emitTo === 'function') {
                    window.livewire.emitTo('tickets.create', 'showCreatingTicket');
                    return;
                }

                if (currentAttempt < 15) {
                    setTimeout(function() {
                        emitOpenTicketModal(currentAttempt + 1);
                    }, 200);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', emitOpenTicketModal, { once: true });
            } else {
                emitOpenTicketModal();
            }

            document.addEventListener('livewire:load', emitOpenTicketModal, { once: true });
        })();
    </script>
@endif
@push('modals')

    @livewire('leads.create', ['lead' => $lead])
    @livewire('tickets.create', ['leadId' => $lead->id])
    @livewire('orders.create', ['leadId' => $lead->id])
    @livewire('leads.partials.skip-modal', ['leadId' => $lead->contact_number])
    @livewire('leads.partials.update-contact', ['leadId' => $lead->contact_number])
    @livewire('leads.partials.submit-call-status')


    @livewire('cx-tickets.survey.rating-panel')
    @livewire('cx-tickets.survey.reopen-panel')
    @livewire('cx-tickets.survey.satisfaction-rating-panel')
    @livewire('cx-tickets.survey.satisfaction-reopen-panel')
@endpush