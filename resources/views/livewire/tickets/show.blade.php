<div class="pb-4">
    @php
        $title = $ticket ? $ticket->ticket_title : '';
    @endphp

    <x-modal.card :title="$title" blur wire:model="showTicketModal" max-width="3xl" align="center">
        @if ($ticket)
            <div class="p-4 sm:p-6 space-y-4 bg-slate-50/60">
                
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                    <div class="p-4 pr-4 flex-1 min-w-0">
                        <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</div>
                        
                            @if ($ticket->lead)
                            <div class="space-y-2 text-sm text-slate-700">
                                <div>Client Name: <span class="font-medium">{{ $ticket->lead->full_name }}</span></div>
                                <div>Contact Number: <span class="font-medium">{{ $ticket->lead->contact_number }}</span></div>
                            </div>
                        @else
                            <div class="text-sm text-slate-500">No customer details available.</div>
                        @endif
                    </div>

                    <div class="p-4 pl-0 flex shrink-0 justify-end">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                        @if ($ticket && $loggedUser && $loggedUser->id == $ticket->assigned_user_id)
                            @if ($ticket->ticket_status_id == 1)
                                <x-button flat label="Start" wire:click="save"
                                    class="border border-blue-600 !bg-white !text-blue-600 hover:!bg-blue-50" />
                            @endif

                            @if ($ticket->ticket_status_id != 4 && $ticket->ticket_status_id != 1)
                                <x-button flat label="Complete" wire:click="closeTicket"
                                    class="border border-red-600 !bg-white !text-red-600 hover:!bg-red-50" />
                               @endif
                            @if ($ticket->ticket_status_id != 4 && $ticket->ticket_status_id != 1 && $ticket->ticket_status_id != 5)
                                <x-button flat label="Hold" wire:click="holdTicket"
                                    class="border border-yellow-600 !bg-white !text-yellow-600 hover:!bg-yellow-50" />
                            @endif

                            {{-- <x-button flat label="Exit" x-on:click="close" /> --}}
                        @else
                            {{-- <x-button flat label="Exit" x-on:click="close" /> --}}
                        @endif
                        </div>
                    </div>
                    </div>

                    
                </div>
                
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        {{-- <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Classification</div> --}}
                        <div class="space-y-2 text-sm text-slate-700">
                            @if ($ticket->ticket_category_id != 3)
                                <div>Category: <span class="font-medium">{{ $ticket->category->title }}</span></div>
                            @endif

                            <div>
                                {{ $ticket->ticket_category_id == 3 ? 'Delivery Type' : 'Sub Category' }}:
                                <span class="font-medium">{{ $ticket->subCategory->title }}</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span>{{ $ticket->ticket_category_id == 3 ? 'Payment Methods:' : 'Tags:' }}</span>
                                @foreach ($ticket->tags as $tag)
                                    <span class="rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        {{-- <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ticket Overview</div> --}}
                        <div class="text-sm text-slate-700">
                            Department: <span class="font-medium">{{ $ticket->department?->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="text-sm text-slate-700">
                            Assigned User: <span class="font-medium">{{ $ticket->assignedUser?->name ?? 'Not assigned' }}</span>
                        </div>
                    </div>

                    
                </div>

                @if ($ticket->description)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ $ticket->ticket_category_id != 3 ? 'Description' : 'Special Instruction' }}
                        </div>
                        <p class="text-sm leading-6 text-slate-700">{{ $ticket->description }}</p>
                    </div>
                @endif

                

                <div
                    x-data="{
                        assignOption: @entangle('assignOption').defer,
                        changeDepartment: @entangle('changeDepartment').defer
                    }"
                    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        {{-- <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assignment</div> --}}
                        @if ($loggedUser && $loggedUser->user_type_id == 1 && $ticket->ticket_status_id != 4 && $ticket->assigned_user_id)
                            <x-button secondary sm label="Unassign" wire:click="unAssign" />
                        @endif
                    </div>

                    @if ($loggedUser && !$ticket->assigned_user_id && $ticket->ticket_status_id != 4)
                        <div class="space-y-3">
                            <label class="text-sm font-semibold text-slate-700">Assignment Type</label>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <label class="flex items-center gap-2">
                                    <input type="radio" value="me" x-model="assignOption" />
                                    <span>Assign to me</span>
                                </label>

                                @if ($loggedUser->user_type_id == 1)
                                    <label class="flex items-center gap-2">
                                        <input type="radio" value="user" x-model="assignOption" />
                                        <span>Assign to user</span>
                                    </label>
                                @endif
                            </div>

                            <div x-show="assignOption === 'me'" x-cloak>
                                <x-button secondary label="Assign to me" wire:click="assign" />
                            </div>

                            @if ($loggedUser->user_type_id == 1)
                                <div class="flex flex-col gap-2 sm:w-72">
                                    <div x-show="assignOption === 'user'" x-cloak class="flex flex-col gap-2">
                                        <x-native-select label="Users" placeholder="Select user" :options="$users"
                                            wire:model.defer="selectedUser" option-label="name" option-value="id" />
                                        <x-button secondary label="Assign to User" wire:click="assignToUser" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($loggedUser && $loggedUser->user_type_id == 1 && $ticket->ticket_status_id != 4)
                        <div class="pt-2 border-t border-slate-200 space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <input type="radio" value="change" x-model="changeDepartment" />
                                <span>Change Department</span>
                            </label>

                            <div x-show="changeDepartment === 'change'" x-cloak>
                                <div class="flex flex-col gap-2 sm:w-72">
                                    <x-native-select label="Departments" placeholder="Select Department" :options="$departments"
                                        wire:model.defer="selectedDepartment" option-label="name" option-value="id" />
                                    <x-button secondary label="Assign Department" wire:click="assignDepartment" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="text-sm text-slate-600">
                        {{ $ticket->ticket_category_id == 3 ? 'Ordered At' : 'Reported At' }}:
                        <span class="font-medium text-slate-700">{{ $ticket->created_at->diffForHumans() }}</span>
                        <span class="text-xs text-slate-400">({{ $ticket->created_at }})</span>
                    </div>

                    @if ($ticket->ticket_category_id == 3 && !$ticket->crm)
                        <div class="text-sm text-slate-600">
                            Order Ref: <span class="font-medium text-slate-700">{{ $ticket->order_ref }}</span>
                        </div>
                    @endif
                </div>

                

                @if ($ticket->ticket_category_id == 3 && $ticket->crm)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Order Items</div>
                        <div class="grid grid-cols-6 gap-2 text-sm text-slate-600">
                            <div class="col-span-2 font-semibold">Item</div>
                            <div class="font-semibold">Barcode</div>
                            <div class="font-semibold">Unit Price</div>
                            <div class="text-center font-semibold">Qty</div>
                            <div class="text-center font-semibold">Total</div>
                            <hr class="col-span-6" />

                            @foreach ($ticket->items as $item)
                                <div class="col-span-2 flex items-start gap-1">
                                    @if ($item->parent_item_id)
                                        <svg class="mt-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="none">
                                            <path
                                                d="M8.86417 2.15732C8.76968 2.05693 8.63794 2 8.50007 2C8.36221 2 8.23046 2.05692 8.13597 2.15731L4.1359 6.40731C3.94664 6.6084 3.95623 6.92484 4.15732 7.1141C4.3584 7.30336 4.67484 7.29377 4.8641 7.09269L8 3.76085V15C8 16.6569 9.34315 18 11 18H15.5C15.7761 18 16 17.7761 16 17.5C16 17.2239 15.7761 17 15.5 17H11C9.89543 17 9 16.1046 9 15V3.76073L12.1359 7.09268C12.3252 7.29377 12.6416 7.30336 12.8427 7.1141C13.0438 6.92485 13.0534 6.60841 12.8641 6.40732L8.86417 2.15732Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    @endif

                                    {{ $item->item->descr }}
                                </div>
                                <div>{{ $item->item->barcode }}</div>
                                <div>{{ number_format($item->unit_price, 2) }}</div>
                                <div class="text-center">{{ $item->qty }}</div>
                                <div class="text-center">{{ number_format($item->unit_price * $item->qty, 2) }}</div>
                            @endforeach

                            <hr class="col-span-6" />
                            <div class="col-span-5 text-right font-semibold">Sub Total</div>
                            <div class="text-center font-semibold">{{ number_format($ticket->items->sum('line_total'), 2) }}</div>
                        </div>
                    </div>
                @endif

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm space-y-4">
                    {{-- <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Activity Log</div> --}}

                    <div class="space-y-2">
                        <x-textarea wire:model.defer="comment" placeholder="Enter your comment here" rows="2" />
                        <x-button secondary label="Comment" wire:click="comment" />
                    </div>

                    @if ($ticket->activities)
                        <ol class="relative border-l border-slate-200 pl-4">
                            @foreach ($ticket->activities as $activity)
                                @if ($activity->user)
                                    <li class="mb-4 ml-2">
                                        <span
                                            class="absolute -left-2 flex h-4 w-4 items-center justify-center rounded-full bg-blue-500 ring-4 ring-white"></span>
                                        <div class="text-sm font-semibold text-slate-800">{{ $activity->user->name }}</div>
                                        <time class="text-xs text-slate-400">
                                            {{ $activity->type }} on {{ $activity->created_at->diffForHumans() }}
                                        </time>
                                        <p class="text-sm text-slate-600">{{ $activity->comment }}</p>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        @endif
    </x-modal.card>
</div>
