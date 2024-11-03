<div>
    @php
        $title = $ticket ? $ticket->ticket_title : '';
    @endphp
    <x-modal.card :title="$title" blur fullscreen=true wire:model="showTicketModal" max-width="4xl" align="center">
        <div class="space-y-2">


            @if ($ticket)
                <div class="grid grid-cols-3 gap-4 w-full text-sm">
                    @if ($ticket->lead)
                        <div>Client Name : {{ $ticket->lead->full_name }}</div>
                        <div>Contact Number : {{ $ticket->lead->contact_number }}</div>
                        <div>Priority : </div>
                    @endif
                    @if ($ticket->ticket_category_id != 3)
                        <div>Category : {{ $ticket->category->title }}</div>
                    @endif
                    <div>{{ $ticket->ticket_category_id == 3 ? 'Delivery  Type ' : 'Sub Category' }} :
                        {{ $ticket->subCategory->title }}</div>

                    <div class="flex space-x-2">
                        <div> {{ $ticket->ticket_category_id == 3 ? 'Payment Methods ' : 'Tags' }} :</div>
                        @foreach ($ticket->tags as $tag)
                            <span class="text-xs   px-2 py-1 ">{{ $tag }}</span>
                        @endforeach
                    </div>

                    <div>
                        {{ $ticket->ticket_category_id == 3 ? 'Orderd At ' : 'Report at' }} :
                        {{ $ticket->created_at->diffForHumans() }} <span
                            class="text-2xs text-gray-400">{{ $ticket->created_at }}</span>
                    </div>

                    @if ($ticket->ticket_category_id == 3 && !$ticket->crm)
                        <div> Order Ref : {{ $ticket->order_ref }}</div>
                    @endif

                    <div> Outlet : {{ $ticket->outlet->title }}</div>
                    <div> Pickup At : {{ $ticket->standard_due_at }}</div>
                    <div>
                        @canany(['is-has-outlet', 'client-admin', 'is-super-admin'])
                            @if ($ticket->ticket_status_id == 1 && !$ticket->is_synced)
                                <x-input placeholder="Order Ref" wire:model.defer="orderRef" />
                            @endif
                        @endcanany
                        Outlet Order Ref :{{ $ticket->bill_no }}
                    </div>
                </div>
                @if ($ticket->description)
                    <div class="text-sm">
                        {{ $ticket->ticket_category_id != 3 ? 'Description ' : 'Special Instruction' }} :
                        {{ $ticket->description }}
                    </div>
                @endif


                @if ($ticket->ticket_category_id == 3 && $ticket->crm)
                    <hr>
                    <div class="grid grid-cols-6 mt-4 gap-2">
                        <div class="col-span-2 text-sm text-gray-600">Item</div>
                        <div class=" text-sm text-gray-600">Barcode</div>
                        <div class=" text-sm text-gray-600">Unit Price</div>
                        <div class="text-sm text-gray-600 text-center">Qty</div>
                        <div class="text-sm text-gray-600 text-center">Total</div>
                        <hr class="col-span-6" />
                        @foreach ($ticket->items as $index => $item)
                            <div class="col-span-2 text-sm text-gray-600 flex">
                                @if ($item->parent_item_id)
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M8.86417 2.15732C8.76968 2.05693 8.63794 2 8.50007 2C8.36221 2 8.23046 2.05692 8.13597 2.15731L4.1359 6.40731C3.94664 6.6084 3.95623 6.92484 4.15732 7.1141C4.3584 7.30336 4.67484 7.29377 4.8641 7.09269L8 3.76085V15C8 16.6569 9.34315 18 11 18H15.5C15.7761 18 16 17.7761 16 17.5C16 17.2239 15.7761 17 15.5 17H11C9.89543 17 9 16.1046 9 15V3.76073L12.1359 7.09268C12.3252 7.29377 12.6416 7.30336 12.8427 7.1141C13.0438 6.92485 13.0534 6.60841 12.8641 6.40732L8.86417 2.15732Z"
                                            fill="currentColor"></path>
                                    </svg>
                                @endif
                                {{ $item->item->descr }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $item->item->barcode }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ number_format($item->unit_price, 2) }}
                            </div>
                            <div class="text-sm text-center  ">
                                {{ $item->qty }}
                            </div>
                            <div class="text-sm text-center  ">
                                {{ number_format($item->unit_price * $item->qty, 2) }}
                            </div>
                        @endforeach
                        <hr class="col-span-6" />
                        <div class="col-span-5 text-right">
                            Sub Total
                        </div>
                        <div class="text-center">
                            {{ number_format($ticket->items->sum('line_total'), 2) }}
                        </div>


                    </div>
                @endif

                <div class="space-y-4 border p-4  ">
                    @foreach ($ticket->activities as $activity)
                        <div class="flex space-x-2">
                            <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15"
                                fill="none">
                                <path d="M4 7.5L7 10l4-5m-3.5 9.5a7 7 0 110-14 7 7 0 010 14z" stroke="currentColor">
                                </path>
                            </svg>
                            <div class="text-xs my-auto">{{ $activity->created_at }} Order {{ $activity->type }} by
                                {{ $activity->user ? $activity->user->name : 'N/A' }} </div>
                        </div>
                    @endforeach
                </div>



            @endif





        </div>

        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                @if ($ticket)

                    @canany(['is-has-outlet', 'client-admin', 'is-super-admin'])
                        <x-button flat negative label="Complete" wire:click="closeTicket" />
                    @endcanany
                    <div class="flex">
                        <x-button flat label="Exit" x-on:click="close" />
                        @canany(['is-has-outlet', 'client-admin', 'is-super-admin'])
                            @if ($ticket->ticket_status_id == 1)
                                <div class="flex">
                                    <x-button primary label="Start" wire:click="save" squared />
                                </div>
                            @endif
                        @endcanany
                    </div>
                @endif
            </div>
        </x-slot>
    </x-modal.card>
</div>
