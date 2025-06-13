<div wire:init="refreshTimeline">
    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-2">

            <div class="flex justify-end space-x-2">

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

                @if ($lead->status_id == 2)
                    <a href="#" onclick="Livewire.emitTo('tickets.create', 'showCreatingTicket')"
                        class="border border-info-600 dark:hover:bg-slate-700 dark:ring-offset-slate-800 disabled:cursor-not-allowed disabled:opacity-80 duration-150 ease-in focus:ring-2 focus:ring-offset-2 gap-x-2 group hover:bg-info-50 hover:shadow-sm inline-flex items-center justify-center outline-none px-4 py-0.5 ring-info-600 rounded text-info-600 text-sm transition-all">
                        <svg class="w-6 h-6 " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z">
                            </path>
                        </svg>
                        Ticket
                    </a>
                    {{-- <a href="#" onclick="$openModal('CreatingOrder')"
                        class="outline-none inline-flex justify-center items-center group transition-all ease-in duration-150 focus:ring-2 focus:ring-offset-2 hover:shadow-sm disabled:opacity-80 disabled:cursor-not-allowed rounded gap-x-2 text-sm px-4 py-0.5     ring-positive-500 text-positive-500 border border-positive-500 hover:bg-positive-50
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
                @else
                    <x-button icon="pencil" positive label="Complete Profile"
                        onclick="$openModal('showTicketEditModal')" />
                @endif

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
                            {{--  <div class="flex ">
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
                                    <svg wire:click="openWhatsApp" class="w-5 h-5 text-green-600" fill="currentColor"
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
                                    Livewire.on('whatsappOpened', function (url) {
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

                        <hr>
                        <div class="grid grid-cols-2">
                            <div class="col-span-2  text-center ">
                                @if ($lead->orders->count() > 0)
                                    <div class="font-semibold text-green-600">Return Customer</div>
                                @else
                                    <div class="font-semibold text-red-600">New Customer</div>
                                @endif
                            </div>
                        </div>
                        <hr>
                        This Call Reaction:
                        <div>
    <button
        wire:click="toggle"
        class="relative inline-flex items-center h-6 rounded-full w-11 focus:outline-none transition-colors duration-200 {{ $moodStatus ? 'bg-red-500' : 'bg-gray-300' }}"
    >
        <span
            class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 {{ $moodStatus ? 'translate-x-6' : 'translate-x-1' }}"
        ></span>
    </button>
    <span class="ml-2 text-sm">
        {{-- {{ $moodStatus ? 'Angry' : 'Not Angry' }} --}}
        Unsatisfied
    </span>
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
                                    {{ $lead->orders && $lead->orders->max('created_at') ? $lead->orders->max('created_at')->format('Y-m-d') : '--' }}
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-1/2">Last Ordered Outlet : </div>
                                <div class="w-1/2">
                                    {{ $lead->lastOrder && $lead->lastOrder->outlet ? $lead->lastOrder->outlet->title : '--' }}
                                </div>
                            </div>

                        </div>

                        <hr>
                        <div class="grid grid-cols-2">
                            <div class="col-span-2  text-center ">
                                <div class="font-semibold text-green-600">Average Basket Value :
                                    {{ number_format($lead->orders->count() == 0 ? 0 : $lead->orders->sum('order_total') / $lead->orders->count(), 2) }}
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="bg-white p-4 space-y-2 text-xs">
                        <h1 class=" font-bold">Ticket Information</h1>
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
                    </div>
                </div>
                @livewire('leads.partials.activity-log', ['lead' => $lead])
            </div>



        </div>
    </div>
</div>
@push('modals')
    @livewire('leads.create', ['lead' => $lead])
    @livewire('tickets.create', ['leadId' => $lead->id])
    @livewire('orders.create', ['leadId' => $lead->id])
@endpush
